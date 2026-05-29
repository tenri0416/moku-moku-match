<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\WorkPost;

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

        return view('home', compact('latestWorkPosts'));
    }
}
