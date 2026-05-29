<?php

namespace App\Http\Controllers;

use App\Models\Article;
use Illuminate\View\View;

class ArticleController extends Controller
{
    /**
     * 記事一覧
     */
    public function index(): View
    {
        $articles = Article::query()
            ->with('prefecture')
            ->public()
            ->latest('published_at')
            ->paginate(12);

        return view('articles.index', compact('articles'));
    }

    /**
     * 通常の記事詳細
     * URL例：/articles/nara-freelance-work-partner
     */
    public function show(Article $article): View
    {
        $this->abortIfNotPublic($article);

        $article->load('prefecture');

        return view('articles.show', compact('article'));
    }

    /**
     * 短縮URLの記事詳細
     * URL例：/nara
     */
    public function showShort(string $shortSlug): View
    {
        $article = Article::query()
            ->where('short_slug', $shortSlug)
            ->firstOrFail();

        $this->abortIfNotPublic($article);

        $article->load('prefecture');

        return view('articles.show', compact('article'));
    }

    /**
     * 公開記事以外は404にする
     */
    private function abortIfNotPublic(Article $article): void
    {
        abort_if($article->status !== Article::STATUS_PUBLIC, 404);
        abort_if($article->published_at === null || $article->published_at->isFuture(), 404);
    }
}
