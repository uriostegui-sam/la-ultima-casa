<?php

namespace Tests\Feature;

use App\Models\Artist;
use App\Models\Artwork;
use App\Models\ArtworkImage;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class ArtworkTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function test_artist_can_create_artwork()
    {
        Storage::fake('public');
    
        $user = User::factory()->create(['role' => 'artist']);
        $artist = Artist::factory()->create(['user_id' => $user->id]);
    
        $response = $this->actingAs($user)
            ->postJson('/api/artworks', [
                'artist_id' => $artist->id,
                'title' => 'Test Artwork',
                'description' => 'Test description',
                'images' => [
                    UploadedFile::fake()->image('artwork1.jpg'),
                    UploadedFile::fake()->image('artwork2.jpg')
                ],
                'creation_date' => '2023-01-01'
            ]);
    
        $response->assertStatus(201);
        $this->assertCount(2, $response->json('images'));
        Storage::disk('public')->assertExists($response->json('images.0.path'));
    }

    /** @test */
    public function test_guest_cannot_create_artwork()
    {
        $artist = Artist::factory()->create();

        $response = $this->postJson('/api/artworks', [
            'artist_id' => $artist->id,
            'title' => 'Guest try',
            'description' => 'Should fail',
            'images' => [
                UploadedFile::fake()->image('art.jpg')
            ],
            'creation_date' => '2023-01-01'
        ]);

        $response->assertStatus(401);
    }

    /** @test */
    public function artist_can_update_their_artwork()
    {
        Storage::fake('public');

        $user = User::factory()->create(['role' => 'artist']);
        $artist = Artist::factory()->create(['user_id' => $user->id]);

        $artwork = Artwork::factory()->create([
            'artist_id' => $artist->id,
            'title' => 'Old Title'
        ]);

        $newImage = UploadedFile::fake()->image('new_image.jpg');

        $response = $this->actingAs($user)
            ->putJson("/api/artworks/{$artwork->id}", [
                'title' => 'Updated Title',
                'images' => [$newImage],
            ]);

        $response->assertOk()
                ->assertJsonFragment([
                    'title' => 'Updated Title'
                ]);

        // Check if new image exists
        Storage::disk('public')->assertExists(
            $response->json('images.0.path')
        );
    }

    /** @test */
    public function artist_can_delete_their_artwork()
    {
        $user = User::factory()->create(['role' => 'artist']);
        $artist = Artist::factory()->create(['user_id' => $user->id]);

        $artwork = Artwork::factory()->create([
            'artist_id' => $artist->id
        ]);

        $artwork->refresh();

        $response = $this->actingAs($user)
            ->deleteJson("/api/artworks/{$artwork->id}");

        $response->assertNoContent();

        $this->assertDatabaseMissing('artworks', [
            'id' => $artwork->id,
        ]);
    }

    /** @test */
    public function test_artist_cannot_delete_other_artists_artwork()
    {
        $user1 = User::factory()->create(['role' => 'artist']);
        $user2 = User::factory()->create(['role' => 'artist']);

        $artist1 = Artist::factory()->for($user1)->create();
        $artist2 = Artist::factory()->for($user2)->create();

        $artwork = $artist1->artworks()->create([
            'title' => 'Protected',
            'description' => 'Not yours!',
            'creation_date' => '2023-01-01'
        ]);

        $response = $this->actingAs($user2)
            ->deleteJson("/api/artworks/{$artwork->id}");

        $response->assertStatus(403); // Forbidden
    }

    /** @test */
    public function test_admin_can_delete_any_artwork()
    {
        $admin = User::factory()->admin()->create();
        $artist = Artist::factory()->create();
        $artwork = $artist->artworks()->create([
            'title' => 'To be deleted',
            'description' => 'Admin removes this',
            'creation_date' => '2023-01-01'
        ]);

        $response = $this->actingAs($admin)
            ->deleteJson("/api/artworks/{$artwork->id}");

        $response->assertNoContent();
    }

    /** @test */
    public function artist_can_delete_an_image_from_their_artwork()
    {
        Storage::fake('public');

        $user = User::factory()->create(['role' => 'artist']);
        $artist = Artist::factory()->create(['user_id' => $user->id]);

        $response = $this->actingAs($user)
            ->postJson('/api/artworks', [
                'artist_id' => $artist->id,
                'title' => 'Artwork with images',
                'images' => [
                    UploadedFile::fake()->image('delete_me.jpg'),
                ],
                'creation_date' => '2023-01-01'
            ]);

        $artworkId = $response->json('id');
        $imageId = $response->json('images.0.id');

        $deleteResponse = $this->actingAs($user)
            ->deleteJson("/api/artworks/{$artworkId}/images/{$imageId}");

        $deleteResponse->assertNoContent();
        $this->assertDatabaseMissing('artwork_images', ['id' => $imageId]);
    }
}
