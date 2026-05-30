<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ArticleTag;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class ArticleTagController extends Controller
{
    public function index(): View
    {
        $tags = ArticleTag::query()
            ->orderBy('sort_order')
            ->orderBy('id')
            ->get();

        return view('admin.article-tags.index', compact('tags'));
    }

    public function create(): View
    {
        $tag = new ArticleTag();

        return view('admin.article-tags.create', compact('tag'));
    }

    public function store(Request $request): RedirectResponse
    {
        ArticleTag::create($this->validated($request));

        return redirect()
            ->route('admin.article-tags.index')
            ->with('success', 'タグを作成しました。');
    }

    public function edit(ArticleTag $articleTag): View
    {
        $tag = $articleTag;

        return view('admin.article-tags.edit', compact('tag'));
    }

    public function update(Request $request, ArticleTag $articleTag): RedirectResponse
    {
        $articleTag->update($this->validated($request, $articleTag));

        return redirect()
            ->route('admin.article-tags.index')
            ->with('success', 'タグを更新しました。');
    }

    public function destroy(ArticleTag $articleTag): RedirectResponse
    {
        if ($articleTag->articles()->exists()) {
            return back()->with('error', 'このタグを使用している記事があるため削除できません。');
        }

        $articleTag->delete();

        return redirect()
            ->route('admin.article-tags.index')
            ->with('success', 'タグを削除しました。');
    }

    private function validated(Request $request, ?ArticleTag $tag = null): array
    {
        $tagId = $tag?->id;

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'slug' => [
                'nullable',
                'string',
                'max:255',
                'regex:/^[a-z0-9-]+$/',
                Rule::unique('article_tags', 'slug')->ignore($tagId),
            ],
            'description' => ['nullable', 'string'],
            'sort_order' => ['nullable', 'integer', 'min:0'],
            'is_active' => ['nullable', 'boolean'],
        ], [
            'slug.regex' => 'スラッグは半角英数字とハイフンのみ使用できます。',
        ]);

        $validated['slug'] = $validated['slug'] ?: Str::slug($validated['name']);
        $validated['sort_order'] = $validated['sort_order'] ?? 0;
        $validated['is_active'] = $request->boolean('is_active');

        return $validated;
    }
}
