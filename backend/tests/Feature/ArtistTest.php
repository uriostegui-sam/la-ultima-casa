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
        ->assertJsonStructure([
                'data' => [[
                    'id',
                    'user_id',
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
                ]]
            ])
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

        $response = $this->actingAs($admin)
            ->postJson('/api/artists', [
                'user_id' => User::factory()->create()->id,
                'minibio' => ['en' => 'English Ullam et in animi incidunt est. Accusamus dolore natus ut accusantium.\n\nQuia eum sapiente non fugit id laboriosam earum.', 'es' => 'Español Ullam et in animi incidunt est. Accusamus dolore natus ut accusantium.\n\nQuia eum sapiente non fugit id laboriosam earum.'],
                'bio' => ['en' => 'Long English Accusamus dolore natus ut accusantium.\n\nQuia eum sapiente non fugit id laboriosam earum. Aut mollitia unde dolorem nesciunt. Labore illo dolore hic quaerat. Laudantium dicta nemo quo commodi vero accusamus nobis.', 'es' => 'Bio largo español Accusamus dolore natus ut accusantium.\n\nQuia eum sapiente non fugit id laboriosam earum. Aut mollitia unde dolorem nesciunt. Labore illo dolore hic quaerat. Laudantium dicta nemo quo commodi vero accusamus nobis.'],
                'skills' => $skills->pluck('id')->toArray()
            ]);

        $response->assertCreated()
            ->assertJsonStructure([
                'data' => [
                    'id',
                    'user_id',
                    'name',
                    'profile_image',
                    'minibio',
                    'bio',
                    'skills',
                    'social_links',
                    'profile_image_url'
                ]
            ]);

        $artistId = $response->json('data.id');
        $artist = Artist::find($artistId);
        Storage::disk('public')->assertExists($artist->profile_image);
            

        $this->assertCount(3, $artist->skills);
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
                'data' => [
                    'id',
                    'artworks',
                    'user' => ['id', 'name']
                ]
            ])
            ->assertJsonCount(3, 'data.artworks');
    }

    /** @test */
    public function it_updates_artist_profile_and_skills()
    {
        $admin = User::factory()->admin()->create();
        $artist = Artist::factory()
            ->for(User::factory()) // ensure user is attached
            ->create();

        $newSkills = Skill::factory()->count(2)->create();
        $newImage = UploadedFile::fake()->create('new-image.jpg');

        $response = $this->actingAs($admin)
            ->putJson("/api/artists/{$artist->id}", [
                'profile_image' => $newImage,
                'minibio' => [
                    'en' => 'Updated Ullam et in animi incidunt est. Accusamus dolore natus ut accusantium.\n\nQuia eum sapiente non fugit id laboriosam earum.',
                    'es' => 'Español Actualizado Ullam et in animi incidunt est. Accusamus dolore natus ut accusantium.\n\nQuia eum sapiente non fugit id laboriosam earum.'
                ],
                'skills' => $newSkills->pluck('id')->toArray(),
                'user' => [
                    'name' => 'Updated Name',
                    'lastname' => 'Updated Lastname',
                ],
                'bio' => [
                    'es' => 'Biografía actualizada en español. Lorem ipsum dolor sit amet, consectetur adipiscing elit. Ut aliquet sapien et augue faucibus egestas ac non ligula.',
                ],
            ]);

        $response->assertOk()
            ->assertJsonPath('data.minibio.es', 'Español Actualizado Ullam et in animi incidunt est. Accusamus dolore natus ut accusantium.\n\nQuia eum sapiente non fugit id laboriosam earum.');


        Storage::disk('public')->assertExists(
            str_replace('/storage/', '', $response->json('data.profile_image_url'))
        );


        $this->assertCount(2, $response->json('data.skills'));
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
            ->assertJson(['message' => 'cannotArtistWArtwork']);
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
