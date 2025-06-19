<?php

namespace App\Services;

use App\Models\News;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class NewsService
{
    /**
     * Create a new news item with optional image
     */
    public function createNews(array $data, ?UploadedFile $image = null): News
    {
        if ($image instanceof UploadedFile) {
            $data['image_path'] = $this->storeImage($image, $data);
        }

        return News::create([
            'title' => $data['title'],
            'content' => $data['content'],
            'published' => $data['published'] ?? false,
            'image_path' => $data['image_path'] ?? null,
        ]);
    }

    /**
     * Update an existing news item
     */
    public function updateNews(News $news, array $data, ?UploadedFile $image = null): News
    {
        if ($image) {
            $this->deleteImage($news->image_path);
            $data['image_path'] = $this->storeImage($image, $news);
        }

        $news->update($data);
        return $news;
    }

    /**
     * Delete a news item and its associated image
     */
    public function deleteNews(News $news): void
    {
        $this->deleteImage($news->image_path);
        $news->delete();
    }

    /**
     * Store news image to disk
     */
    protected function storeImage(UploadedFile $image, News|array $news): string
    {
        $name = is_array($news) ? $news['title']['es'] : $news->title['es'];
        $slug = Str::slug($name);
        $extension = $image->getClientOriginalExtension();
        $filename = "{$slug}.{$extension}";

        return $image->storeAs('news/images', $filename, 'public');
    }

    /**
     * Delete news image from disk
     */
    protected function deleteImage(?string $path): void
    {
        if ($path && Storage::disk('public')->exists($path)) {
            Storage::disk('public')->delete($path);
        }
    }

    /**
     * Get paginated news items
     */
    public function getPaginatedNews()
    {
        $query = News::query();

        return $query->latest()->paginate(10);
    }
}