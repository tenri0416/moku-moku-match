<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Support\ApiActionLogger;
use Illuminate\Support\Facades\Auth;

class AdminUserController extends Controller
{
    public function index()
    {
        ApiActionLogger::info(
            'AdminUserController::index',
            '管理者ユーザー一覧画面にアクセス',
            [
                'admin_id' => Auth::guard('admin')->id(),
            ]
        );

        $users = User::with('profile')->latest()->paginate(20);

        return view('admin.users.index', compact('users'));
    }

    public function show(User $user)
    {
        ApiActionLogger::info(
            'AdminUserController::show',
            '管理者ユーザー詳細画面にアクセス',
            [
                'admin_id' => Auth::guard('admin')->id(),
                'target_user_id' => $user->id,
                'target_user_email' => $user->email,
                'target_user_status' => $user->status,
            ]
        );

        $user->load(['profile', 'workPosts', 'applications']);

        return view('admin.users.show', compact('user'));
    }

    public function suspend(User $user)
    {
        ApiActionLogger::info(
            'AdminUserController::suspend',
            '管理者ユーザー停止処理開始',
            [
                'admin_id' => Auth::guard('admin')->id(),
                'target_user_id' => $user->id,
                'current_status' => $user->status,
            ]
        );

        $user->update(['status' => User::STATUS_SUSPENDED]);

        ApiActionLogger::info(
            'AdminUserController::suspend',
            '管理者ユーザー停止処理成功',
            [
                'admin_id' => Auth::guard('admin')->id(),
                'target_user_id' => $user->id,
                'status' => User::STATUS_SUSPENDED,
            ]
        );

        return back()->with('success', 'ユーザーを停止しました。');
    }

    public function activate(User $user)
    {
        ApiActionLogger::info(
            'AdminUserController::activate',
            '管理者ユーザー有効化処理開始',
            [
                'admin_id' => Auth::guard('admin')->id(),
                'target_user_id' => $user->id,
                'current_status' => $user->status,
            ]
        );

        $user->update(['status' => User::STATUS_ACTIVE]);

        ApiActionLogger::info(
            'AdminUserController::activate',
            '管理者ユーザー有効化処理成功',
            [
                'admin_id' => Auth::guard('admin')->id(),
                'target_user_id' => $user->id,
                'status' => User::STATUS_ACTIVE,
            ]
        );

        return back()->with('success', 'ユーザーを有効化しました。');
    }
}
