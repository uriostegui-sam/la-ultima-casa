<?php

namespace Tests\Unit;

use App\Models\Artist;
use App\Models\Artwork;
use App\Services\ArtworkService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;
use Illuminate\Support\Str;

class ArtworkServiceTest extends TestCase
{
    use RefreshDatabase;

    protected ArtworkService $artworkService;

    protected function setUp(): void
    {
        parent::setUp();
        $this->artworkService = new ArtworkService();
        Storage::fake('public');
    }

    /**
     * @test
     */
    public function it_can_create_an_artwork(): void
    {
        $artist = Artist::factory()->create();
        $image = UploadedFile::fake()->create('artwork2.jpg', 100, 'image/jpeg');

        $data = [
            'artist_id' => $artist->id,
            'title' => 'Test Artwork',
            'description' => ['en' => 'English Ullam et in animi incidunt est. Accusamus dolore natus ut accusantium.\n\nQuia eum sapiente non fugit id laboriosam earum.', 'es' => 'Español Ullam et in animi incidunt est. Accusamus dolore natus ut accusantium.\n\nQuia eum sapiente non fugit id laboriosam earum.'],
            'creation_date' => '2023-01-01'
        ];

        $artwork = $this->artworkService->createArtwork($data, $image);

        $this->assertInstanceOf(Artwork::class, $artwork);
        $this->assertDatabaseHas('artworks', ['title' => 'Test Artwork']);
        Storage::disk('public')->assertExists($artwork->image_path);
    }

    /**
     * @test
     */
    public function it_can_store_multiple_images_for_an_artwork(): void
    {
        $artwork = Artwork::factory()->create();
        $artworkImages = [
            UploadedFile::fake()->create('artwork1.jpg', 100, 'image/jpeg'),
            UploadedFile::fake()->create('artwork2.jpg', 100, 'image/jpeg'),
        ];

        $this->artworkService->storeImages($artwork, $artworkImages);

        $this->assertCount(2, $artwork->images);
        foreach ($artwork->images as $index => $image) {
            $number = $index + 1;
            $slug = Str::slug($artwork->title);
            Storage::disk('public')->assertExists($image->path);
            $this->assertEquals("artworks/images/{$slug}-{$number}.jpg", $image->path);
        }
    }

    /**
     * @test
     */
    public function it_can_delete_an_image(): void
    {
        $artwork = Artwork::factory()->create();
        $artworkImages = [
            UploadedFile::fake()->create('artwork1.jpg', 100, 'image/jpeg'),
        ];

        $this->artworkService->storeImages($artwork, $artworkImages);
        $image = $artwork->images->first();

        Storage::disk('public')->assertExists($image->path);

        $this->artworkService->deleteImage($image);

        Storage::disk('public')->assertMissing($image->path);
        $this->assertDatabaseMissing('artwork_images', ['id' => $image->id]);
    }
}
