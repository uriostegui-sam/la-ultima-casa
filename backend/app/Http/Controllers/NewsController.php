<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreNewsRequest;
use App\Http\Requests\UpdateNewsRequest;
use App\Models\News;
use App\Services\NewsService;

class NewsController extends Controller
{
    public function __construct(
        protected NewsService $newsService
    ) {}

    public function index()
    {
        return $this->newsService->getPaginatedNews();
    }

    public function store(StoreNewsRequest $request)
    {
        $this->authorize('create', News::class);

        $news = $this->newsService->createNews(
            $request->validated(),
            $request->file('image')
        );

        return response()->json($news, 201);
    }

    public function show(News $news)
    {
        return response()->json([
            'id' => $news->id,
            'title' => translate($news->title),
            'content' => translate($news->content),
            'image_url' => $news->image_url,
            'published_at' => $news->published_at,
        ]);
    }

    public function update(UpdateNewsRequest $request, News $news)
    {
        $this->authorize('update', $news);

        $news = $this->newsService->updateNews(
            $news,
            $request->validated(),
            $request->file('image')
        );

        return response()->json($news);
    }

    public function destroy(News $news)
    {
        $this->authorize('delete', $news);

        $this->newsService->deleteNews($news);
        return response()->noContent();
    }
}