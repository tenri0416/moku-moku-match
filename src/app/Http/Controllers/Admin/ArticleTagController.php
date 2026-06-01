<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ArticleTag;
use App\Support\ApiActionLogger;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class ArticleTagController extends Controller
{
    public function index(): View
    {
        ApiActionLogger::info(
            'Admin\ArticleTagController::index',
            '管理者記事タグ一覧画面にアクセス',
            [
                'admin_id' => auth('admin')->id(),
            ]
        );

        $tags = ArticleTag::query()
            ->orderBy('sort_order')
            ->orderBy('id')
            ->get();

        return view('admin.article-tags.index', compact('tags'));
    }

    public function create(): View
    {
        ApiActionLogger::info(
            'Admin\ArticleTagController::create',
            '管理者記事タグ作成画面にアクセス',
            [
                'admin_id' => auth('admin')->id(),
            ]
        );

        $tag = new ArticleTag();

        return view('admin.article-tags.create', compact('tag'));
    }

    public function store(Request $request): RedirectResponse
    {
        ApiActionLogger::info(
            'Admin\ArticleTagController::store',
            '管理者記事タグ作成処理開始',
            [
                'admin_id' => auth('admin')->id(),
                'name' => $request->name,
                'slug' => $request->slug,
                'sort_order' => $request->sort_order,
                'is_active' => $request->boolean('is_active'),
            ]
        );

        $tag = ArticleTag::create($this->validated($request));

        ApiActionLogger::info(
            'Admin\ArticleTagController::store',
            '管理者記事タグ作成成功',
            [
                'admin_id' => auth('admin')->id(),
                'article_tag_id' => $tag->id,
                'name' => $tag->name,
                'slug' => $tag->slug,
            ]
        );

        return redirect()
            ->route('admin.article-tags.index')
            ->with('success', 'タグを作成しました。');
    }

    public function edit(ArticleTag $articleTag): View
    {
        ApiActionLogger::info(
            'Admin\ArticleTagController::edit',
            '管理者記事タグ編集画面にアクセス',
            [
                'admin_id' => auth('admin')->id(),
                'article_tag_id' => $articleTag->id,
                'name' => $articleTag->name,
                'slug' => $articleTag->slug,
            ]
        );

        $tag = $articleTag;

        return view('admin.article-tags.edit', compact('tag'));
    }

    public function update(Request $request, ArticleTag $articleTag): RedirectResponse
    {
        ApiActionLogger::info(
            'Admin\ArticleTagController::update',
            '管理者記事タグ更新処理開始',
            [
                'admin_id' => auth('admin')->id(),
                'article_tag_id' => $articleTag->id,
                'name' => $request->name,
                'slug' => $request->slug,
                'sort_order' => $request->sort_order,
                'is_active' => $request->boolean('is_active'),
            ]
        );

        $articleTag->update($this->validated($request, $articleTag));

        ApiActionLogger::info(
            'Admin\ArticleTagController::update',
            '管理者記事タグ更新成功',
            [
                'admin_id' => auth('admin')->id(),
                'article_tag_id' => $articleTag->id,
                'name' => $articleTag->name,
                'slug' => $articleTag->slug,
            ]
        );

        return redirect()
            ->route('admin.article-tags.index')
            ->with('success', 'タグを更新しました。');
    }

    public function destroy(ArticleTag $articleTag): RedirectResponse
    {
        ApiActionLogger::info(
            'Admin\ArticleTagController::destroy',
            '管理者記事タグ削除処理開始',
            [
                'admin_id' => auth('admin')->id(),
                'article_tag_id' => $articleTag->id,
                'name' => $articleTag->name,
                'slug' => $articleTag->slug,
            ]
        );

        if ($articleTag->articles()->exists()) {
            ApiActionLogger::info(
                'Admin\ArticleTagController::destroy',
                '使用中の記事が存在するためタグ削除不可',
                [
                    'admin_id' => auth('admin')->id(),
                    'article_tag_id' => $articleTag->id,
                ]
            );

            return back()->with('error', 'このタグを使用している記事があるため削除できません。');
        }

        $tagId = $articleTag->id;

        $articleTag->delete();

        ApiActionLogger::info(
            'Admin\ArticleTagController::destroy',
            '管理者記事タグ削除成功',
            [
                'admin_id' => auth('admin')->id(),
                'article_tag_id' => $tagId,
            ]
        );

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
