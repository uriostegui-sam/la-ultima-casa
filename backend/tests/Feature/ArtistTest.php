<?php

namespace Tests\Feature;

use App\Models\Artist;
use App\Models\Skill;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class ArtistTest extends TestCase
{
    use RefreshDatabase;
    protected function setUp(): void
    {
        parent::setUp();
        Storage::fake('public');
    }

    /** @test */
    public function it_lists_all_artists()
    {
        $artist = Artist::factory()
            ->has(Skill::factory()->count(2))
            ->create();

        $response = $this->getJson('/api/artists');

        $response->assertOk()
            ->assertJsonStructure([[
                'id',
                'name',
                'profile_image',
                'minibio',
                'bio',
                'skills',
                'social_links' => [
                    'website',
                    'instagram',
                    'twitter',
                    'flickr'
                ]
            ]])
            ->assertJsonFragment([
                'id' => $artist->id,
                'name' => $artist->user->getFullNameAttribute()
            ]);
    }

    /** @test */
    public function it_creates_an_artist_with_skills()
    {
        $admin = User::factory()->admin()->create();
        $skills = Skill::factory()->count(3)->create();
        $image = UploadedFile::fake()->image('artist.jpg');

        $response = $this->actingAs($admin)
            ->postJson('/api/artists', [
                'user_id' => User::factory()->create()->id,
                'profile_image' => $image,
                'minibio' => ['en' => 'English Ullam et in animi incidunt est. Accusamus dolore natus ut accusantium.\n\nQuia eum sapiente non fugit id laboriosam earum.', 'es' => 'Español Ullam et in animi incidunt est. Accusamus dolore natus ut accusantium.\n\nQuia eum sapiente non fugit id laboriosam earum.'],
                'bio' => ['en' => 'Long English Accusamus dolore natus ut accusantium.\n\nQuia eum sapiente non fugit id laboriosam earum. Aut mollitia unde dolorem nesciunt. Labore illo dolore hic quaerat. Laudantium dicta nemo quo commodi vero accusamus nobis.', 'es' => 'Bio largo español Accusamus dolore natus ut accusantium.\n\nQuia eum sapiente non fugit id laboriosam earum. Aut mollitia unde dolorem nesciunt. Labore illo dolore hic quaerat. Laudantium dicta nemo quo commodi vero accusamus nobis.'],
                'skills' => $skills->pluck('id')->toArray()
            ]);

        $response->assertCreated()
            ->assertJsonStructure([
                'artist' => [
                    'id',
                    'profile_image',
                    'skills'
                ]
            ]);

        Storage::disk('public')->assertExists($response->json('artist.profile_image'));
        $this->assertCount(3, $response->json('artist.skills'));
    }

    /** @test */
    public function it_shows_specific_artist_with_artworks()
    {
        $artist = Artist::factory()
            ->hasArtworks(3)
            ->create();

        $response = $this->getJson("/api/artists/{$artist->id}");

        $response->assertOk()
            ->assertJsonStructure([
                'id',
                'artworks',
                'user'
            ])
            ->assertJsonCount(3, 'artworks');
    }

    /** @test */
    public function it_updates_artist_profile_and_skills()
    {
        $admin = User::factory()->admin()->create();
        $artist = Artist::factory()->create();
        $newSkills = Skill::factory()->count(2)->create();
        $newImage = UploadedFile::fake()->image('new-image.jpg');

        $response = $this->actingAs($admin)
            ->putJson("/api/artists/{$artist->id}", [
                'profile_image' => $newImage,
                'minibio' => ['en' => 'Updated Ullam et in animi incidunt est. Accusamus dolore natus ut accusantium.\n\nQuia eum sapiente non fugit id laboriosam earum.', 'es' => 'Español Actualizado Ullam et in animi incidunt est. Accusamus dolore natus ut accusantium.\n\nQuia eum sapiente non fugit id laboriosam earum.'],
                'skills' => $newSkills->pluck('id')->toArray()
            ]);

        $response->assertOk()
            ->assertJsonFragment([
                'minibio' => ['en' => 'Updated Ullam et in animi incidunt est. Accusamus dolore natus ut accusantium.\n\nQuia eum sapiente non fugit id laboriosam earum.', 'es' => 'Español Actualizado Ullam et in animi incidunt est. Accusamus dolore natus ut accusantium.\n\nQuia eum sapiente non fugit id laboriosam earum.'],
            ]);

        Storage::disk('public')->assertExists($response->json('artist.profile_image_url'));
        $this->assertCount(2, $response->json('artist.skills'));
    }

    /** @test */
    public function it_deletes_artist_without_artworks()
    {
        $admin = User::factory()->admin()->create();
        $artist = Artist::factory()->create([
            'profile_image' => 'artists/profile-images/test.jpg'
        ]);
        Storage::disk('public')->put('artists/profile-images/test.jpg', 'test');

        $response = $this->actingAs($admin)
            ->deleteJson("/api/artists/{$artist->id}");

        $response->assertNoContent();
        Storage::disk('public')->assertMissing('artists/profile-images/test.jpg');
        $this->assertDatabaseMissing('artists', ['id' => $artist->id]);
    }

    /** @test */
    public function it_prevents_deleting_artist_with_artworks()
    {
        $admin = User::factory()->admin()->create();
        $artist = Artist::factory()
            ->hasArtworks(1)
            ->create();

        $response = $this->actingAs($admin)
            ->deleteJson("/api/artists/{$artist->id}");

        $response->assertStatus(422)
            ->assertJson(['message' => 'Cannot delete artist with existing artworks']);
    }

    /** @test */
    public function non_admin_cannot_create_artists()
    {
        $user = User::factory()->create();
        $response = $this->actingAs($user)
            ->postJson('/api/artists', [
                'user_id' => $user->id,
            ]);

        $response->assertForbidden();
    }
}
