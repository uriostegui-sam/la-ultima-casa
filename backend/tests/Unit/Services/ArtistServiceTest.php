<?php

namespace Tests\Unit;

use App\Models\Artist;
use App\Models\Skill;
use App\Models\User;
use App\Services\ArtistService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Tests\TestCase;
use Illuminate\Support\Facades\Storage;

class ArtistServiceTest extends TestCase
{
    use RefreshDatabase;

    protected ArtistService $artistService;

    protected function setUp(): void
    {
        parent::setUp();
        $this->artistService = new ArtistService(); 
        Storage::fake('public');
    }
    /** @test */
    public function it_creates_an_artist(){
        $user = User::factory()->create(['role' => 'artist']);
        $skills = Skill::factory()->count(2)->create();

        $data = [
            'user_id' => $user->id,
            'minibio' => ['en' => 'English Ullam et in animi incidunt est. Accusamus dolore natus ut accusantium.\n\nQuia eum sapiente non fugit id laboriosam earum.', 'es' => 'Español Ullam et in animi incidunt est. Accusamus dolore natus ut accusantium.\n\nQuia eum sapiente non fugit id laboriosam earum.'],
            'bio' => ['en' => 'Long English Accusamus dolore natus ut accusantium.\n\nQuia eum sapiente non fugit id laboriosam earum. Aut mollitia unde dolorem nesciunt. Labore illo dolore hic quaerat. Laudantium dicta nemo quo commodi vero accusamus nobis.', 'es' => 'Bio largo español Accusamus dolore natus ut accusantium.\n\nQuia eum sapiente non fugit id laboriosam earum. Aut mollitia unde dolorem nesciunt. Labore illo dolore hic quaerat. Laudantium dicta nemo quo commodi vero accusamus nobis.'],
            'skills' => $skills->pluck('id')->toArray(),
        ];

        $artist = $this->artistService->createArtist($data);

        $this->assertInstanceOf(Artist::class, $artist);
        $this->assertDatabaseHas('artists', [
            'user_id' => $user->id,
        ]);
        $this->assertEquals($data['bio']['en'], $artist->bio['en']);
        $this->assertEquals($data['minibio']['es'], $artist->minibio['es']);
        $this->assertCount(2, $artist->skills);
    }

    /** @test */
    public function it_updates_an_artist()
    {
        $artist = Artist::factory()->create();
        $skills = Skill::factory()->count(2)->create();

        $data = [
            'minibio' => ['en' => 'Updated minibio in English.', 'es' => 'Actualización de minibio en español.'],
            'bio' => ['en' => 'Updated bio in English.', 'es' => 'Actualización de bio en español.'],
            'skills' => $skills->pluck('id')->toArray(),
        ];

        $updatedArtist = $this->artistService->updateArtist($artist, $data);

        $this->assertInstanceOf(Artist::class, $updatedArtist);
        $this->assertDatabaseHas('artists', [
            'id' => $artist->id,
        ]);
        $this->assertEquals($data['bio']['en'], $updatedArtist->bio['en']);
        $this->assertEquals($data['minibio']['es'], $updatedArtist->minibio['es']);
        $this->assertCount(2, $updatedArtist->skills);
    }

    /** @test */
    public function it_deletes_old_profile_picture_of_artist()
    {
        $artist = Artist::factory()->create();
        $oldImagePath = 'artists/profile_images/old_image.jpg';
        Storage::disk('public')->put($oldImagePath, 'fake content');
        $artist->profile_image = $oldImagePath;
        $artist->save();

        $newImage = UploadedFile::fake()->create('new_image.jpg', 100, 'image/jpeg');

        $this->artistService->updateArtist($artist, ['profile_image' => $newImage]);

        Storage::disk('public')->assertMissing($oldImagePath);
        Storage::disk('public')->assertExists($artist->profile_image);
    }

    /** @test */
    public function it_does_not_delete_profile_picture_if_no_new_image_provided()
    {
        $artist = Artist::factory()->create();
        $oldImagePath = 'artists/profile_images/old_image.jpg';
        
        Storage::disk('public')->put($oldImagePath, 'fake content');
        
        $artist->profile_image = $oldImagePath;
        $artist->save();
        $this->artistService->updateArtist($artist, []);
        
        Storage::disk('public')->assertExists($oldImagePath);
    }
}
