<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;


class AdminUserController extends Controller
{
     public function index()
    {
        $users = User::with('profile')->latest()->paginate(20);

        return view('admin.users.index', compact('users'));
    }

    public function show(User $user)
    {
        $user->load(['profile', 'workPosts', 'applications']);

        return view('admin.users.show', compact('user'));
    }

    public function suspend(User $user)
    {
        $user->update(['status' => User::STATUS_SUSPENDED]);

        return back()->with('success', 'ユーザーを停止しました。');
    }

    public function activate(User $user)
    {
        $user->update(['status' => User::STATUS_ACTIVE]);

        return back()->with('success', 'ユーザーを有効化しました。');
    }
}
