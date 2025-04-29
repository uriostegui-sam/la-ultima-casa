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
        $news = $this->newsService->createNews(
            $request->validated(),
            $request->file('image')
        );

        return response()->json($news, 201);
    }

    public function show(News $news)
    {
        return $news;
    }

    public function update(UpdateNewsRequest $request, News $news)
    {
        $news = $this->newsService->updateNews(
            $news,
            $request->validated(),
            $request->file('image')
        );

        return response()->json($news);
    }

    public function destroy(News $news)
    {
        $this->newsService->deleteNews($news);
        return response()->noContent();
    }
}