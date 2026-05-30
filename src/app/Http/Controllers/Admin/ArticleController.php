<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\ArticleRequest;
use App\Models\Article;
use App\Models\Prefecture;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;
use App\Models\ArticleCategory;
use App\Models\ArticleTag;

class ArticleController extends Controller
{
    /**
     * 記事一覧
     */
    public function index(Request $request): View
    {
        $articles = Article::query()
            ->with(['admin', 'prefecture'])
            ->when($request->keyword, function ($query, $keyword) {
                $query->where(function ($query) use ($keyword) {
                    $query->where('title', 'like', "%{$keyword}%")
                        ->orWhere('slug', 'like', "%{$keyword}%")
                        ->orWhere('short_slug', 'like', "%{$keyword}%");
                });
            })
            ->when($request->status, fn($query, $status) => $query->where('status', $status))
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

        return view('admin.articles.create', compact('article', 'prefectures', 'categories', 'tags'));
    }

    /**
     * 記事登録
     */
    public function store(ArticleRequest $request): RedirectResponse
    {
        $validated = $request->validated();

        if ($request->hasFile('thumbnail')) {
            $validated['thumbnail_path'] = $request->file('thumbnail')->store('articles', 'public');
        }

        unset($validated['thumbnail']);

        $article = Article::create([
            ...$validated,
            'admin_id' => auth('admin')->id(),
        ]);
        $article->tags()->sync($request->input('tag_ids', []));

        return redirect()
            ->route('admin.articles.show', $article)
            ->with('success', '記事を作成しました。');
    }

    /**
     * 記事詳細
     */
    public function show(Article $article): View
    {
        $article->load(['admin', 'prefecture']);

        return view('admin.articles.show', compact('article'));
    }

    /**
     * 記事編集画面
     */
    public function edit(Article $article): View
    {
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

        return view('admin.articles.edit', compact('article', 'prefectures', 'categories', 'tags'));
    }

    /**
     * 記事更新
     */
    public function update(ArticleRequest $request, Article $article): RedirectResponse
    {
        $validated = $request->validated();

        if ($request->hasFile('thumbnail')) {
            if ($article->thumbnail_path) {
                Storage::disk('public')->delete($article->thumbnail_path);
            }

            $validated['thumbnail_path'] = $request->file('thumbnail')->store('articles', 'public');
        }

        unset($validated['thumbnail']);

        $article->update($validated);
        $article->tags()->sync($request->input('tag_ids', []));

        return redirect()
            ->route('admin.articles.show', $article)
            ->with('success', '記事を更新しました。');
    }

    /**
     * 記事削除
     */
    public function destroy(Article $article): RedirectResponse
    {
        $article->delete();

        return redirect()
            ->route('admin.articles.index')
            ->with('success', '記事を削除しました。');
    }
}
