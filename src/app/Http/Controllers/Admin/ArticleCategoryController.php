<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ArticleCategory;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class ArticleCategoryController extends Controller
{
    public function index(): View
    {
        $categories = ArticleCategory::with('parent')
            ->orderBy('sort_order')
            ->orderBy('id')
            ->get();

        return view('admin.article-categories.index', compact('categories'));
    }

    public function create(): View
    {
        $category = new ArticleCategory();

        $parentCategories = ArticleCategory::with('parent')
            ->orderBy('sort_order')
            ->orderBy('id')
            ->get()
            ->filter(fn (ArticleCategory $category) => $category->canHaveChild());

        return view('admin.article-categories.create', compact('category', 'parentCategories'));
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $this->validated($request);

        $this->validateDepth($validated['parent_id'] ?? null);

        ArticleCategory::create($validated);

        return redirect()
            ->route('admin.article-categories.index')
            ->with('success', 'カテゴリーを作成しました。');
    }

    public function edit(ArticleCategory $articleCategory): View
    {
        $category = $articleCategory;

        $parentCategories = ArticleCategory::with('parent')
            ->where('id', '!=', $category->id)
            ->orderBy('sort_order')
            ->orderBy('id')
            ->get()
            ->filter(fn (ArticleCategory $parent) => $parent->canHaveChild());

        return view('admin.article-categories.edit', compact('category', 'parentCategories'));
    }

    public function update(Request $request, ArticleCategory $articleCategory): RedirectResponse
    {
        $validated = $this->validated($request, $articleCategory);

        $this->validateDepth($validated['parent_id'] ?? null);

        $articleCategory->update($validated);

        return redirect()
            ->route('admin.article-categories.index')
            ->with('success', 'カテゴリーを更新しました。');
    }

    public function destroy(ArticleCategory $articleCategory): RedirectResponse
    {
        if ($articleCategory->children()->exists()) {
            return back()->with('error', '子カテゴリーが存在するため削除できません。');
        }

        if ($articleCategory->articles()->exists()) {
            return back()->with('error', 'このカテゴリーを使用している記事があるため削除できません。');
        }

        $articleCategory->delete();

        return redirect()
            ->route('admin.article-categories.index')
            ->with('success', 'カテゴリーを削除しました。');
    }

    private function validated(Request $request, ?ArticleCategory $category = null): array
    {
        $categoryId = $category?->id;

        $validated = $request->validate([
            'parent_id' => ['nullable', 'exists:article_categories,id'],
            'name' => ['required', 'string', 'max:255'],
            'slug' => [
                'nullable',
                'string',
                'max:255',
                'regex:/^[a-z0-9-]+$/',
                Rule::unique('article_categories', 'slug')->ignore($categoryId),
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

    private function validateDepth(?int $parentId): void
    {
        if (! $parentId) {
            return;
        }

        $parent = ArticleCategory::with('parent.parent')->findOrFail($parentId);

        if (! $parent->canHaveChild()) {
            throw ValidationException::withMessages([
                'parent_id' => 'カテゴリーの階層は最大3階層までです。',
            ]);
        }
    }
}
