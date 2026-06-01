<?php

namespace App\Http\Controllers;

use App\Models\Article;
use App\Models\WorkPost;
use App\Support\ApiActionLogger;

class HomeController extends Controller
{
    public function index()
    {
        ApiActionLogger::info(
            'HomeController::index',
            'トップページにアクセス',
            [
                'user_id' => auth()->id(),
            ]
        );

        $latestWorkPosts = WorkPost::query()
            ->with([
                'user.profile',
                'user.profile.prefecture',
            ])
            ->latest()
            ->take(6)
            ->get();

        $latestArticles = Article::query()
            ->public()
            ->latest('published_at')
            ->take(6)
            ->get();

        return view('home', compact('latestWorkPosts', 'latestArticles'));
    }
}
