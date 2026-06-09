<?php

namespace App\Http\Controllers;

use App\Models\Article;
use Illuminate\Http\Request;
use Illuminate\View\View;
use App\Models\ArticleTag;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class ArticleController extends Controller
{
    /**
     * 記事一覧。
     */
    public function index(Request $request): View
    {
        $keyword = trim((string) $request->query('keyword', ''));
        $sort = (string) $request->query('sort', 'new');

        $query = Article::query()
            ->with(['category', 'prefecture', 'tags', 'authorUser.profile'])
            ->withCount('likes')
            ->where('status', Article::STATUS_PUBLIC)
            ->whereNotNull('published_at')
            ->where('published_at', '<=', now());

        if ($keyword !== '') {
            $query->where(function ($keywordQuery) use ($keyword) {
                $keywordQuery
                    ->where('title', 'like', '%' . $keyword . '%')
                    ->orWhere('h1_title', 'like', '%' . $keyword . '%')
                    ->orWhere('excerpt', 'like', '%' . $keyword . '%')
                    ->orWhere('body_html', 'like', '%' . $keyword . '%')
                    ->orWhere('seo_title', 'like', '%' . $keyword . '%')
                    ->orWhere('seo_description', 'like', '%' . $keyword . '%');
            });
        }

        if ($sort === 'category') {
            $query
                ->leftJoin('article_categories', 'articles.article_category_id', '=', 'article_categories.id')
                ->select('articles.*')
                ->orderBy('article_categories.name')
                ->orderByDesc('articles.published_at')
                ->orderByDesc('articles.id');
        } else {
            $query
                ->orderByDesc('articles.published_at')
                ->orderByDesc('articles.id');
        }

        $articles = $query->paginate(12)->withQueryString();

        $pageTitle = match (true) {
            $keyword !== '' => '「' . $keyword . '」の検索結果',
            $sort === 'category' => 'カテゴリー順の記事一覧',
            default => '新着記事一覧',
        };

        $pageDescription = $keyword !== ''
            ? 'YomuWorks内で「' . $keyword . '」に関連する記事を表示しています。'
            : '技術、個人開発、暮らし、働き方、MokuMoku Matchの活用方法を届ける記事一覧です。';

        return view('articles.index', compact(
            'articles',
            'keyword',
            'sort',
            'pageTitle',
            'pageDescription'
        ));
    }

    /**
     * 記事詳細。
     */
    public function show(Article $article): View
    {
        abort_unless(
            (int) $article->status === Article::STATUS_PUBLIC
                && $article->published_at
                && $article->published_at->lte(now()),
            404
        );

        DB::transaction(function () use ($article) {
            $article->increment('view_count');
        });

        $article->refresh();

        $article->load(['category', 'prefecture', 'tags', 'authorUser.profile']);
        $article->loadCount('likes');

        $relatedArticles = Article::query()
            ->with(['category', 'prefecture', 'tags', 'authorUser.profile'])
            ->where('status', Article::STATUS_PUBLIC)
            ->whereNotNull('published_at')
            ->where('published_at', '<=', now())
            ->where('id', '!=', $article->id)
            ->latest('published_at')
            ->take(4)
            ->get();

        return view('articles.show', compact('article', 'relatedArticles'));
    }

    /**
     * タグ別の記事一覧。
     */
    public function tag(string $tag): View
    {
        $articleTag = ArticleTag::query()
            ->where('slug', $tag)
            ->firstOrFail();

        $articles = Article::query()
            ->with(['category', 'prefecture', 'tags', 'authorUser.profile'])
            ->where('status', Article::STATUS_PUBLIC)
            ->whereNotNull('published_at')
            ->where('published_at', '<=', now())
            ->whereHas('tags', function ($query) use ($articleTag) {
                $query->where('article_tags.id', $articleTag->id);
            })
            ->orderByDesc('published_at')
            ->orderByDesc('id')
            ->paginate(12)
            ->withQueryString();

        $keyword = '';
        $sort = 'new';
        $pageTitle = '「' . $articleTag->name . '」の記事一覧';
        $pageDescription = '「' . $articleTag->name . '」に関連する記事を表示しています。';

        return view('articles.index', compact(
            'articles',
            'keyword',
            'sort',
            'pageTitle',
            'pageDescription',
            'articleTag'
        ));
    }


    /**
     * 記事いいね。
     *
     * ゲストは browser_key、ログインユーザーは user_id で同じ記事に1回だけ押せる。
     */
    public function like(Request $request, Article $article): JsonResponse
    {
        abort_unless(
            (int) $article->status === Article::STATUS_PUBLIC
                && $article->published_at
                && $article->published_at->lte(now()),
            404
        );

        $validated = $request->validate([
            'browser_key' => ['nullable', 'string', 'max:100'],
        ]);

        $userId = Auth::id();
        $browserKey = $validated['browser_key'] ?? null;

        if (!$userId && !$browserKey) {
            return response()->json([
                'message' => 'ブラウザ情報を取得できませんでした。',
            ], 422);
        }

        try {
            if ($userId) {
                $article->likes()->firstOrCreate(
                    [
                        'user_id' => $userId,
                    ],
                    [
                        'browser_key' => $browserKey,
                        'ip_address' => $request->ip(),
                        'user_agent' => $request->userAgent(),
                    ]
                );
            } else {
                $article->likes()->firstOrCreate(
                    [
                        'browser_key' => $browserKey,
                    ],
                    [
                        'user_id' => null,
                        'ip_address' => $request->ip(),
                        'user_agent' => $request->userAgent(),
                    ]
                );
            }
        } catch (\Throwable $e) {
            // unique制約で重複した場合も、画面上は成功扱いにする
        }

        return response()->json([
            'liked' => true,
            'like_count' => $article->likes()->count(),
        ]);
    }

    /**
     * 短縮URLの記事詳細。
     */
    public function showShort(string $shortSlug): View
    {
        $article = Article::query()
            ->where('short_slug', $shortSlug)
            ->firstOrFail();

        return $this->show($article);
    }
}



// <!-- admax -->
// <script src="https://adm.shinobi.jp/s/7a6e9ec6bd134cb6a61110cbfe221f82"></script>
// <!-- admax -->
