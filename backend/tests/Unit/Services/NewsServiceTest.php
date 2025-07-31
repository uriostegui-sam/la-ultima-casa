<?php

namespace Tests\Unit;

use App\Models\News;
use App\Services\NewsService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class NewsServiceTest extends TestCase
{
    use RefreshDatabase;

    protected NewsService $newsService;

    protected function setUp(): void
    {
        parent::setUp();
        $this->newsService = new NewsService();
        Storage::fake('public');
    }

    /** @test */
    public function it_can_create_news(): void
    {
        $data = [
            'title' => ['en' => 'Test News', 'es' => 'Noticia de prueba'],
            'content' => ['en' => 'English content', 'es' => 'Spanish content'],
            'published' => true,
        ];

        $news = $this->newsService->createNews($data);

        $this->assertInstanceOf(News::class, $news);
        $this->assertDatabaseHas('news', ['published' => true]);
        $this->assertEquals('Test News', $news->title['en']);
        $this->assertEquals('Spanish content', $news->content['es']);
    }

    /** @test */
    public function it_can_update_news(): void
    {
        $news = News::factory()->create();
        $data = [
            'title' => ['en' => 'Updated News', 'es' => 'Noticia actualizada'],
            'content' => ['en' => 'Updated content', 'es' => 'Contenido actualizado'],
            'published' => false,
        ];
        $updatedNews = $this->newsService->updateNews($news, $data);

        $this->assertInstanceOf(News::class, $updatedNews);
        $this->assertEquals('Updated News', $updatedNews->title['en']);
        $this->assertEquals('Contenido actualizado', $updatedNews->content['es']);
        $this->assertFalse($updatedNews->published);
    }

    /** @test */

    
}
