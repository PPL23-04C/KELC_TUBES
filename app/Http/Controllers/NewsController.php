<?php

namespace App\Http\Controllers;

use App\Services\NewsService;
use Illuminate\View\View;

class NewsController extends Controller
{
    /**
     * Create a new controller instance.
     */
    public function __construct(
        private NewsService $newsService
    ) {
    }

    /**
     * Display a listing of SDG 7 news articles.
     */
    public function index(): View
    {
        $articles = $this->newsService->getSdg7News(15);
        return view('news.index', compact('articles'));
    }

    /**
     * Display the specified news article.
     */
    public function show(string $id): View
    {
        $articles = $this->newsService->getSdg7News(15);
        $article = collect($articles)->firstWhere('id', $id);

        if (!$article) {
            abort(404, 'Berita tidak ditemukan.');
        }

        return view('news.show', compact('article'));
    }
}
