<?php

namespace Tests\Feature;

use App\Models\Artist;
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
}
