<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\WorkPost;

class HomeController extends Controller
{
    public function index()
    {
        $latestWorkPosts = WorkPost::query()
            ->with('user.profile')
            ->where('status', WorkPost::STATUS_OPEN)
            ->latest()
            ->limit(6)
            ->get();

        return view('home', compact('latestWorkPosts'));
    }
}
