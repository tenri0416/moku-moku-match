<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\UserTrainingPointHistory;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class UserProfileController extends Controller
{
    /**
     * ユーザーの自己紹介ページを表示する
     */
    public function show(User $user): View
    {
      
        $user->loadMissing('profile');

        $totalPoints = UserTrainingPointHistory::query()
            ->where('user_id', $user->id)
            ->sum('points');

        $monthlyPoints = UserTrainingPointHistory::query()
            ->where('user_id', $user->id)
            ->whereBetween('earned_on', [
                now()->startOfMonth()->toDateString(),
                now()->endOfMonth()->toDateString(),
            ])
            ->sum('points');

        $trainingCount = UserTrainingPointHistory::query()
            ->where('user_id', $user->id)
            ->count();

        return view('users.profiles.show', [
            'user' => $user,
            'profile' => $user->profile,
            'totalPoints' => $totalPoints,
            'monthlyPoints' => $monthlyPoints,
            'trainingCount' => $trainingCount,
            'isMine' => Auth::id() === $user->id,
        ]);
    }
}
