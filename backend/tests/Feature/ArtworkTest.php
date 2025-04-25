<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class ArtworkTest extends TestCase
{
    /** @test */
    public function test_artist_can_create_artwork()
    {
        $artist = User::factory()->artist()->create();
        
        Storage::fake('public');

        $response = $this->actingAs($artist)
            ->postJson('/api/artworks', [
                'title' => 'Test Artwork',
                'image' => UploadedFile::fake()->image('artwork.jpg')
            ]);

        $response->assertCreated();
        Storage::disk('public')->assertExists('artworks/artwork.jpg');
    }
}
