<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Article;
use App\Support\ApiActionLogger;
use Illuminate\View\View;

class ArticleViewController extends Controller
{
    /**
     * 記事閲覧数一覧を表示する
     */
    public function index(): View
    {
        ApiActionLogger::info(
            'Admin\ArticleViewController::index',
            '管理者記事閲覧数一覧画面にアクセス',
            [
                'admin_id' => auth('admin')->id(),
            ]
        );

        $articles = Article::query()
            ->with(['category'])
            ->withCount('views')
            ->withCount([
                'views as views_last_7_days_count' => function ($query) {
                    $query->where('created_at', '>=', now()->subDays(7));
                },
                'views as views_last_30_days_count' => function ($query) {
                    $query->where('created_at', '>=', now()->subDays(30));
                },
            ])
            ->latest()
            ->paginate(20);

        return view('admin.article-views.index', compact('articles'));
    }
}
