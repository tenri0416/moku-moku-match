<?php

namespace App\Http\Controllers;

use App\Models\Article;
use Illuminate\View\View;
use App\Models\ArticleCategory;
use App\Models\ArticleTag;
use App\Models\ArticleView;
use Illuminate\Support\Facades\Auth;

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
        ArticleView::create([
            'article_id' => $article->id,
            'user_id' => Auth::id(),
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
            'referer' => request()->headers->get('referer'),
        ]);

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
    public function category(ArticleCategory $category)
    {
        abort_unless($category->is_active, 404);

        $articles = Article::query()
            ->with(['category', 'tags'])
            ->where('article_category_id', $category->id)
            ->where('status', 2)
            ->whereNotNull('published_at')
            ->where('published_at', '<=', now())
            ->latest('published_at')
            ->paginate(12);

        return view('articles.index', [
            'articles' => $articles,
            'pageTitle' => $category->name . 'の記事一覧',
            'pageDescription' => $category->description ?: $category->name . 'に関する記事一覧です。',
            'currentCategory' => $category,
        ]);
    }

    public function tag(ArticleTag $tag)
    {
        abort_unless($tag->is_active, 404);

        $articles = Article::query()
            ->with(['category', 'tags'])
            ->whereHas('tags', function ($query) use ($tag) {
                $query->where('article_tags.id', $tag->id);
            })
            ->where('status', 2)
            ->whereNotNull('published_at')
            ->where('published_at', '<=', now())
            ->latest('published_at')
            ->paginate(12);

        return view('articles.index', [
            'articles' => $articles,
            'pageTitle' => $tag->name . 'の記事一覧',
            'pageDescription' => $tag->description ?: $tag->name . 'に関する記事一覧です。',
            'currentTag' => $tag,
        ]);
    }
}
