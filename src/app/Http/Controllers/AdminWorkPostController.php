<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\WorkPost;

class AdminWorkPostController extends Controller
{
    public function index()
    {
        $workPosts = WorkPost::with('user.profile')->latest()->paginate(20);

        return view('admin.work-posts.index', compact('workPosts'));
    }

    public function show(WorkPost $workPost)
    {
        $workPost->load(['user.profile', 'applications.user.profile']);

        return view('admin.work-posts.show', compact('workPost'));
    }

    public function private(WorkPost $workPost)
    {
        $workPost->update(['status' => WorkPost::STATUS_PRIVATE]);

        return back()->with('success', '募集を非公開にしました。');
    }

    public function open(WorkPost $workPost)
    {
        $workPost->update(['status' => WorkPost::STATUS_OPEN]);

        return back()->with('success', '募集を再公開しました。');
    }
}
