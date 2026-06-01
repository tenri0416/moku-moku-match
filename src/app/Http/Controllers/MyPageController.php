<?php

namespace App\Http\Controllers;

use App\Models\Application;
use App\Models\Message;
use App\Support\ApiActionLogger;

class MyPageController extends Controller
{
    public function index()
    {
        ApiActionLogger::info(
            'MyPageController::index',
            'マイページにアクセス',
            [
                'user_id' => auth()->id(),
            ]
        );

        $user = auth()->user()->load('profile');

        $workPosts = $user->workPosts()->latest()->get();

        $applications = $user->applications()
            ->with('workPost.user.profile')
            ->latest()
            ->get();

        $approvedApplications = $user->applications()
            ->with('workPost.user.profile')
            ->where('status', Application::STATUS_APPROVED)
            ->latest()
            ->get();

        $messages = Message::query()
            ->with(['workPost', 'sender.profile', 'receiver.profile'])
            ->where('sender_id', $user->id)
            ->orWhere('receiver_id', $user->id)
            ->latest()
            ->limit(10)
            ->get();

        return view('mypage', compact('user', 'workPosts', 'applications', 'approvedApplications', 'messages'));
    }
}
