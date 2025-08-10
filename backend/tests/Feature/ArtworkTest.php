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
                'description' => ['en' => 'English Ullam et in animi incidunt est. Accusamus dolore natus ut accusantium.\n\nQuia eum sapiente non fugit id laboriosam earum.', 'es' => 'Español Ullam et in animi incidunt est. Accusamus dolore natus ut accusantium.\n\nQuia eum sapiente non fugit id laboriosam earum.'],
                'images' => [
                    UploadedFile::fake()->create('artwork1.jpg', 100, 'image/jpeg'),
                    UploadedFile::fake()->create('artwork2.jpg', 100, 'image/jpeg'),
                ],
                'creation_date' => '2023-01-01'
            ]);

        $response->assertStatus(201);

        $responseData = $response->json('data');
        $this->assertCount(2, $responseData['images']);
        Storage::disk('public')->assertExists($responseData['images'][0]['path']);
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
                UploadedFile::fake()->create('art.jpg', 100, 'image/jpeg'),
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

        $newImage = UploadedFile::fake()->create('new_image.jpg', 100, 'image/jpeg');

        $response = $this->actingAs($user)
            ->putJson("/api/artworks/{$artwork->id}", [
                'title' => 'Updated Title',
                'images' => [$newImage],
                'description' => ['en' => 'English Ullam et in animi incidunt est. Accusamus dolore natus ut accusantium.\n\nQuia eum sapiente non fugit id laboriosam earum.', 'es' => 'Español Ullam et in animi incidunt est. Accusamus dolore natus ut accusantium.\n\nQuia eum sapiente non fugit id laboriosam earum.'],
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
                    UploadedFile::fake()->create('delete_me.jpg', 100, 'image/jpeg')
                ],
                'description' => ['en' => 'English Ullam et in animi incidunt est. Accusamus dolore natus ut accusantium.\n\nQuia eum sapiente non fugit id laboriosam earum.', 'es' => 'Español Ullam et in animi incidunt est. Accusamus dolore natus ut accusantium.\n\nQuia eum sapiente non fugit id laboriosam earum.'],
                'creation_date' => '2023-01-01'
            ]);
        
        $responseData = $response->json('data');
        $artworkId = $responseData['id'];
        $imageId = $responseData['images'][0]['id'];
        
        $deleteResponse = $this->actingAs($user)
            ->deleteJson("/api/artworks/{$artworkId}/images/{$imageId}");

        $deleteResponse->assertNoContent();
        $this->assertDatabaseMissing('artwork_images', ['id' => $imageId]);
    }

    /** @test */
    public function artist_can_set_primary_image_for_artwork()
    {
        Storage::fake('public');

        $user = User::factory()->create(['role' => 'artist']);
        $artist = Artist::factory()->create(['user_id' => $user->id]);

        $artwork = Artwork::factory()->create(['artist_id' => $artist->id]);

        $image1 = ArtworkImage::factory()->create(['artwork_id' => $artwork->id, 'is_primary' => false]);

        $response = $this->actingAs($user)
            ->patchJson("/api/artworks/{$artwork->id}/images/{$image1->id}/set-primary");

        $response->assertOk();

        $this->assertDatabaseHas('artwork_images', [
            'id' => $image1->id,
            'is_primary' => true,
        ]);

    }
}
