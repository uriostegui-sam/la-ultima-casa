<?php

namespace Tests\Feature;

use App\Models\News;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class NewsTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function admin_can_create_news()
    {
        Storage::fake('public');

        $user = User::factory()->admin()->create();
        
        $response = $this->actingAs($user)
            ->postJson('/api/news', [
                'title' => [
                    'en' => 'New exhibition.',
                    'es' => 'Nueva exposicion.',
                ],
                'content' => [
                    'en' => 'We are thrilled to announce a new exhibition!',
                    'es' => '¡Estamos emocionados de anunciar una nueva exposición!',
                ],
                'published_at' => now()->toDateString(),
                'image' => UploadedFile::fake()->image('news.jpg')
            ]);

        $response->assertCreated();
        $this->assertDatabaseHas('news', [
            'title->en' => 'New exhibition.',
            'title->es' => 'Nueva exposicion.'
        ]);

        Storage::disk('public')->assertExists(
            str_replace('/storage/', '', $response->json('image_url'))
        );
    }

    /** @test */
    public function admin_can_update_news()
    {
        Storage::fake('public');

        $user = User::factory()->admin()->create();
        $news = News::factory()->create();

        $response = $this->actingAs($user)
            ->putJson("/api/news/{$news->id}", [
                'title' => [
                    'en' => 'Updated English Title',
                    'es' => 'Updated Spanish Title'
                ],
                'content' => [
                    'en' => 'Updated English content',
                    'es' => 'Updated Spanish content'
                ]
            ]);

        $response->assertOk();
        $this->assertDatabaseHas('news', [
            'id' => $news->id,
            'title->en' => 'Updated English Title',
            'title->es' => 'Updated Spanish Title'
        ]);
    }

    /** @test */
    public function admin_can_delete_news()
    {
        $user = User::factory()->admin()->create();
        
        $news = News::factory()->create();

        $response = $this->actingAs($user)
            ->deleteJson("/api/news/{$news->id}");

        $response->assertNoContent();
        $this->assertDatabaseMissing('news', [
            'id' => $news->id,
        ]);
    }

    /** @test */
    public function guests_cannot_create_news()
    {
        $response = $this->postJson('/api/news', []);
        $response->assertUnauthorized();
    }
}
