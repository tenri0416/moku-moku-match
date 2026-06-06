<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\ArticleRequest;
use App\Models\Article;
use App\Models\ArticleCategory;
use App\Models\ArticleTag;
use App\Models\Prefecture;
use App\Models\User;
use App\Services\ArticleSeoKeywordSuggestionService;
use App\Support\ApiActionLogger;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class ArticleController extends Controller
{
    /**
     * 記事一覧
     */
    public function index(Request $request): View
    {
        ApiActionLogger::info(
            'Admin\ArticleController::index',
            '管理者記事一覧画面にアクセス',
            [
                'admin_id' => auth('admin')->id(),
                'keyword' => $request->keyword,
                'status' => $request->status,
                'page' => $request->query('page'),
            ]
        );

        $articles = Article::query()
            ->with(['admin', 'prefecture', 'category', 'tags', 'authorUser.profile'])
            ->withCount('likes')
            ->when($request->keyword, function ($query, $keyword) {
                $query->where(function ($query) use ($keyword) {
                    $query->where('title', 'like', "%{$keyword}%")
                        ->orWhere('slug', 'like', "%{$keyword}%")
                        ->orWhere('short_slug', 'like', "%{$keyword}%");
                });
            })
            ->when($request->status, fn ($query, $status) => $query->where('status', $status))
            ->latest()
            ->paginate(20)
            ->withQueryString();

        return view('admin.articles.index', compact('articles'));
    }

    /**
     * 記事作成画面
     */
    public function create(): View
    {
        ApiActionLogger::info(
            'Admin\ArticleController::create',
            '管理者記事作成画面にアクセス',
            [
                'admin_id' => auth('admin')->id(),
            ]
        );

        $article = new Article();

        $prefectures = Prefecture::orderBy('id')->get();

        $categories = ArticleCategory::with('parent')
            ->where('is_active', true)
            ->orderBy('sort_order')
            ->orderBy('id')
            ->get();

        $tags = ArticleTag::query()
            ->where('is_active', true)
            ->orderBy('sort_order')
            ->orderBy('id')
            ->get();

        $authorUsers = User::query()
            ->with('profile')
            ->orderBy('id')
            ->get();

        return view('admin.articles.create', compact(
            'article',
            'prefectures',
            'categories',
            'tags',
            'authorUsers'
        ));
    }

    /**
     * 記事登録
     */
    public function store(ArticleRequest $request): RedirectResponse
    {
        ApiActionLogger::info(
            'Admin\ArticleController::store',
            '管理者記事作成処理開始',
            [
                'admin_id' => auth('admin')->id(),
                'title' => $request->title,
                'slug' => $request->slug,
                'short_slug' => $request->short_slug,
                'status' => $request->status,
                'prefecture_id' => $request->prefecture_id,
                'article_category_id' => $request->article_category_id,
                'category_id' => $request->category_id,
                'author_user_id' => $request->author_user_id,
                'reading_minutes' => $request->reading_minutes,
                'tag_ids' => $request->input('tag_ids', []),
                'has_thumbnail' => $request->hasFile('thumbnail'),
            ]
        );

        $validated = $request->validated();

        if ($request->hasFile('thumbnail')) {
            $validated['thumbnail_path'] = $request->file('thumbnail')->store('articles', 'public');
        }

        unset($validated['thumbnail'], $validated['tag_ids']);

        $article = Article::create([
            ...$validated,
            'admin_id' => auth('admin')->id(),
        ]);

        $article->tags()->sync($request->input('tag_ids', []));

        ApiActionLogger::info(
            'Admin\ArticleController::store',
            '管理者記事作成成功',
            [
                'admin_id' => auth('admin')->id(),
                'article_id' => $article->id,
                'title' => $article->title,
                'slug' => $article->slug,
                'status' => $article->status,
                'author_user_id' => $article->author_user_id,
                'reading_minutes' => $article->reading_minutes,
            ]
        );

        return redirect()
            ->route('admin.articles.show', $article)
            ->with('success', '記事を作成しました。');
    }

    /**
     * 記事詳細
     */
    public function show(Article $article, ArticleSeoKeywordSuggestionService $seoKeywordSuggestionService): View
    {
        ApiActionLogger::info(
            'Admin\ArticleController::show',
            '管理者記事詳細画面にアクセス',
            [
                'admin_id' => auth('admin')->id(),
                'article_id' => $article->id,
                'title' => $article->title,
                'slug' => $article->slug,
                'status' => $article->status,
            ]
        );

        $article->load(['admin', 'prefecture', 'category', 'tags', 'authorUser.profile']);

        $seoKeywordSuggestions = $seoKeywordSuggestionService->make($article);

        return view('admin.articles.show', compact('article', 'seoKeywordSuggestions'));
    }

    /**
     * 記事編集画面
     */
    public function edit(Article $article): View
    {
        ApiActionLogger::info(
            'Admin\ArticleController::edit',
            '管理者記事編集画面にアクセス',
            [
                'admin_id' => auth('admin')->id(),
                'article_id' => $article->id,
                'title' => $article->title,
                'slug' => $article->slug,
                'status' => $article->status,
            ]
        );

        $article->load(['tags', 'authorUser.profile']);

        $prefectures = Prefecture::orderBy('id')->get();

        $categories = ArticleCategory::with('parent')
            ->where('is_active', true)
            ->orderBy('sort_order')
            ->orderBy('id')
            ->get();

        $tags = ArticleTag::query()
            ->where('is_active', true)
            ->orderBy('sort_order')
            ->orderBy('id')
            ->get();

        $authorUsers = User::query()
            ->with('profile')
            ->orderBy('id')
            ->get();

        return view('admin.articles.edit', compact(
            'article',
            'prefectures',
            'categories',
            'tags',
            'authorUsers'
        ));
    }

    /**
     * 記事更新
     */
    public function update(ArticleRequest $request, Article $article): RedirectResponse
    {
        ApiActionLogger::info(
            'Admin\ArticleController::update',
            '管理者記事更新処理開始',
            [
                'admin_id' => auth('admin')->id(),
                'article_id' => $article->id,
                'title' => $request->title,
                'slug' => $request->slug,
                'short_slug' => $request->short_slug,
                'status' => $request->status,
                'prefecture_id' => $request->prefecture_id,
                'article_category_id' => $request->article_category_id,
                'category_id' => $request->category_id,
                'author_user_id' => $request->author_user_id,
                'reading_minutes' => $request->reading_minutes,
                'tag_ids' => $request->input('tag_ids', []),
                'has_thumbnail' => $request->hasFile('thumbnail'),
            ]
        );

        $validated = $request->validated();

        if ($request->hasFile('thumbnail')) {
            if ($article->thumbnail_path) {
                Storage::disk('public')->delete($article->thumbnail_path);
            }

            $validated['thumbnail_path'] = $request->file('thumbnail')->store('articles', 'public');
        }

        unset($validated['thumbnail'], $validated['tag_ids']);

        $article->update($validated);
        $article->tags()->sync($request->input('tag_ids', []));

        ApiActionLogger::info(
            'Admin\ArticleController::update',
            '管理者記事更新成功',
            [
                'admin_id' => auth('admin')->id(),
                'article_id' => $article->id,
                'title' => $article->title,
                'slug' => $article->slug,
                'status' => $article->status,
                'author_user_id' => $article->author_user_id,
                'reading_minutes' => $article->reading_minutes,
            ]
        );

        return redirect()
            ->route('admin.articles.show', $article)
            ->with('success', '記事を更新しました。');
    }

    /**
     * 記事削除
     */
    public function destroy(Article $article): RedirectResponse
    {
        ApiActionLogger::info(
            'Admin\ArticleController::destroy',
            '管理者記事削除処理開始',
            [
                'admin_id' => auth('admin')->id(),
                'article_id' => $article->id,
                'title' => $article->title,
                'slug' => $article->slug,
                'status' => $article->status,
            ]
        );

        $articleId = $article->id;

        $article->delete();

        ApiActionLogger::info(
            'Admin\ArticleController::destroy',
            '管理者記事削除成功',
            [
                'admin_id' => auth('admin')->id(),
                'article_id' => $articleId,
            ]
        );

        return redirect()
            ->route('admin.articles.index')
            ->with('success', '記事を削除しました。');
    }
}
