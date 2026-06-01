<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ArticleCategory;
use App\Support\ApiActionLogger;
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
        ApiActionLogger::info(
            'Admin\ArticleCategoryController::index',
            '管理者記事カテゴリー一覧画面にアクセス',
            [
                'admin_id' => auth('admin')->id(),
            ]
        );

        $categories = ArticleCategory::with('parent')
            ->orderBy('sort_order')
            ->orderBy('id')
            ->get();

        return view('admin.article-categories.index', compact('categories'));
    }

    public function create(): View
    {
        ApiActionLogger::info(
            'Admin\ArticleCategoryController::create',
            '管理者記事カテゴリー作成画面にアクセス',
            [
                'admin_id' => auth('admin')->id(),
            ]
        );

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
        ApiActionLogger::info(
            'Admin\ArticleCategoryController::store',
            '管理者記事カテゴリー作成処理開始',
            [
                'admin_id' => auth('admin')->id(),
                'name' => $request->name,
                'slug' => $request->slug,
                'parent_id' => $request->parent_id,
                'sort_order' => $request->sort_order,
                'is_active' => $request->boolean('is_active'),
            ]
        );

        $validated = $this->validated($request);

        $this->validateDepth($validated['parent_id'] ?? null);

        $category = ArticleCategory::create($validated);

        ApiActionLogger::info(
            'Admin\ArticleCategoryController::store',
            '管理者記事カテゴリー作成成功',
            [
                'admin_id' => auth('admin')->id(),
                'article_category_id' => $category->id,
                'name' => $category->name,
                'slug' => $category->slug,
            ]
        );

        return redirect()
            ->route('admin.article-categories.index')
            ->with('success', 'カテゴリーを作成しました。');
    }

    public function edit(ArticleCategory $articleCategory): View
    {
        ApiActionLogger::info(
            'Admin\ArticleCategoryController::edit',
            '管理者記事カテゴリー編集画面にアクセス',
            [
                'admin_id' => auth('admin')->id(),
                'article_category_id' => $articleCategory->id,
                'name' => $articleCategory->name,
                'slug' => $articleCategory->slug,
            ]
        );

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
        ApiActionLogger::info(
            'Admin\ArticleCategoryController::update',
            '管理者記事カテゴリー更新処理開始',
            [
                'admin_id' => auth('admin')->id(),
                'article_category_id' => $articleCategory->id,
                'name' => $request->name,
                'slug' => $request->slug,
                'parent_id' => $request->parent_id,
                'sort_order' => $request->sort_order,
                'is_active' => $request->boolean('is_active'),
            ]
        );

        $validated = $this->validated($request, $articleCategory);

        $this->validateDepth($validated['parent_id'] ?? null);

        $articleCategory->update($validated);

        ApiActionLogger::info(
            'Admin\ArticleCategoryController::update',
            '管理者記事カテゴリー更新成功',
            [
                'admin_id' => auth('admin')->id(),
                'article_category_id' => $articleCategory->id,
                'name' => $articleCategory->name,
                'slug' => $articleCategory->slug,
            ]
        );

        return redirect()
            ->route('admin.article-categories.index')
            ->with('success', 'カテゴリーを更新しました。');
    }

    public function destroy(ArticleCategory $articleCategory): RedirectResponse
    {
        ApiActionLogger::info(
            'Admin\ArticleCategoryController::destroy',
            '管理者記事カテゴリー削除処理開始',
            [
                'admin_id' => auth('admin')->id(),
                'article_category_id' => $articleCategory->id,
                'name' => $articleCategory->name,
                'slug' => $articleCategory->slug,
            ]
        );

        if ($articleCategory->children()->exists()) {
            ApiActionLogger::info(
                'Admin\ArticleCategoryController::destroy',
                '子カテゴリーが存在するためカテゴリー削除不可',
                [
                    'admin_id' => auth('admin')->id(),
                    'article_category_id' => $articleCategory->id,
                ]
            );

            return back()->with('error', '子カテゴリーが存在するため削除できません。');
        }

        if ($articleCategory->articles()->exists()) {
            ApiActionLogger::info(
                'Admin\ArticleCategoryController::destroy',
                '使用中の記事が存在するためカテゴリー削除不可',
                [
                    'admin_id' => auth('admin')->id(),
                    'article_category_id' => $articleCategory->id,
                ]
            );

            return back()->with('error', 'このカテゴリーを使用している記事があるため削除できません。');
        }

        $categoryId = $articleCategory->id;

        $articleCategory->delete();

        ApiActionLogger::info(
            'Admin\ArticleCategoryController::destroy',
            '管理者記事カテゴリー削除成功',
            [
                'admin_id' => auth('admin')->id(),
                'article_category_id' => $categoryId,
            ]
        );

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
