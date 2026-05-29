<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\WorkPost;
use App\Models\Article;

class HomeController extends Controller
{
    public function index()
    {
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
