<?php

namespace App\Http\Controllers;

use App\Models\WorkPost;
use App\Support\ApiActionLogger;
use Illuminate\Support\Facades\Auth;

class AdminWorkPostController extends Controller
{
    public function index()
    {
        ApiActionLogger::info(
            'AdminWorkPostController::index',
            '管理者募集一覧画面にアクセス',
            [
                'admin_id' => Auth::guard('admin')->id(),
            ]
        );

        $workPosts = WorkPost::with('user.profile')->latest()->paginate(20);

        return view('admin.work-posts.index', compact('workPosts'));
    }

    public function show(WorkPost $workPost)
    {
        ApiActionLogger::info(
            'AdminWorkPostController::show',
            '管理者募集詳細画面にアクセス',
            [
                'admin_id' => Auth::guard('admin')->id(),
                'work_post_id' => $workPost->id,
                'work_post_owner_id' => $workPost->user_id,
                'status' => $workPost->status,
            ]
        );

        $workPost->load(['user.profile', 'applications.user.profile']);

        return view('admin.work-posts.show', compact('workPost'));
    }

    public function private(WorkPost $workPost)
    {
        ApiActionLogger::info(
            'AdminWorkPostController::private',
            '管理者募集非公開処理開始',
            [
                'admin_id' => Auth::guard('admin')->id(),
                'work_post_id' => $workPost->id,
                'current_status' => $workPost->status,
            ]
        );

        $workPost->update(['status' => WorkPost::STATUS_PRIVATE]);

        ApiActionLogger::info(
            'AdminWorkPostController::private',
            '管理者募集非公開処理成功',
            [
                'admin_id' => Auth::guard('admin')->id(),
                'work_post_id' => $workPost->id,
                'status' => WorkPost::STATUS_PRIVATE,
            ]
        );

        return back()->with('success', '募集を非公開にしました。');
    }

    public function open(WorkPost $workPost)
    {
        ApiActionLogger::info(
            'AdminWorkPostController::open',
            '管理者募集再公開処理開始',
            [
                'admin_id' => Auth::guard('admin')->id(),
                'work_post_id' => $workPost->id,
                'current_status' => $workPost->status,
            ]
        );

        $workPost->update(['status' => WorkPost::STATUS_OPEN]);

        ApiActionLogger::info(
            'AdminWorkPostController::open',
            '管理者募集再公開処理成功',
            [
                'admin_id' => Auth::guard('admin')->id(),
                'work_post_id' => $workPost->id,
                'status' => WorkPost::STATUS_OPEN,
            ]
        );

        return back()->with('success', '募集を再公開しました。');
    }
}
