<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Support\ApiActionLogger;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Http\Request;

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
                'remember_token' => null,
            ]
        );

        $user->load(['profile', 'workPosts', 'applications']);

        return view('admin.users.show', compact('user'));
    }

    public function suspend(Request $request, User $user)
    {
        $request->validate([
            'suspension_reason' => ['nullable', 'string', 'max:1000'],
        ], [
            'suspension_reason.max' => '利用停止理由は1000文字以内で入力してください。',
        ]);

        $admin = Auth::guard('admin')->user();

        $user->forceFill([
            'status' => User::STATUS_SUSPENDED,
            'suspended_at' => now(),
            'suspension_reason' => $request->input('suspension_reason'),
            'suspended_by_admin_id' => $admin?->id,
            'remember_token' => null,

            // 利用停止なので退会情報は入れない
            'withdrawn_at' => null,
            'withdrawal_reason' => null,
            'withdrawal_type' => null,
            'withdrawn_by_admin_id' => null,
        ])->save();

        Log::info('管理者がユーザーを利用停止にしました。', [
            'admin_id' => $admin?->id,
            'user_id' => $user->id,
            'suspension_reason' => $request->input('suspension_reason'),
        ]);

        return back()->with('status', 'ユーザーを利用停止にしました。');
    }

    public function activate(User $user)
    {
        if ($user->isWithdrawn()) {
            return back()->with('error', '退会済みユーザーは管理者の有効化では復活できません。ユーザー本人が新規登録すると復活します。');
        }
        ApiActionLogger::info(
            'AdminUserController::activate',
            '管理者ユーザー有効化処理開始',
            [
                'admin_id' => Auth::guard('admin')->id(),
                'target_user_id' => $user->id,
                'current_status' => $user->status,
            ]
        );

            $user->forceFill([
                'status' => User::STATUS_ACTIVE,
                'suspended_at' => null,
                'suspension_reason' => null,
                'suspended_by_admin_id' => null,
                'remember_token' => null,
            ])->save();
        

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

    public function forceWithdraw(Request $request, User $user)
    {
        $request->validate([
            'withdrawal_reason' => ['nullable', 'string', 'max:1000'],
        ]);

        $admin = Auth::guard('admin')->user();

        $user->forceFill([
            'status' => User::STATUS_WITHDRAWN,
            'withdrawn_at' => now(),
            'withdrawal_reason' => $request->input('withdrawal_reason'),
            'withdrawal_type' => User::WITHDRAWAL_TYPE_ADMIN,
            'withdrawn_by_admin_id' => $admin?->id,

            'suspended_at' => null,
            'suspension_reason' => null,
            'suspended_by_admin_id' => null,
            'remember_token' => null,
        ])->save();

        Log::info('管理者がユーザーを強制退会にしました。', [
            'admin_id' => $admin?->id,
            'user_id' => $user->id,
        ]);

        return back()->with('status', 'ユーザーを強制退会にしました。');
    }
}
