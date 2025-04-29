<?php

namespace App\Services;

use App\Models\News;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

class NewsService
{
    /**
     * Create a new news item with optional image
     */
    public function createNews(array $data, ?UploadedFile $image = null): News
    {
        if ($image) {
            $data['image_path'] = $this->storeImage($image);
        }

        return News::create($data);
    }

    /**
     * Update an existing news item
     */
    public function updateNews(News $news, array $data, ?UploadedFile $image = null): News
    {
        if ($image) {
            $this->deleteImage($news->image_path);
            $data['image_path'] = $this->storeImage($image);
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
    protected function storeImage(UploadedFile $image): string
    {
        return $image->store('news/images', 'public');
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
    public function getPaginatedNews(int $perPage = 10)
    {
        return News::latest()->paginate($perPage);
    }
}