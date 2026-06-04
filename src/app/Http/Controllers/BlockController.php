<?php

namespace App\Http\Controllers;

use App\Models\Block;
use App\Models\User;
use App\Support\ApiActionLogger;
use Illuminate\Http\RedirectResponse;

class BlockController extends Controller
{
    public function store(User $user): RedirectResponse
    {
        $loginUser = auth()->user();

        abort_unless($loginUser, 403);
        abort_if((int) $user->id === (int) $loginUser->id, 403);

        ApiActionLogger::info(
            'BlockController::store',
            'ユーザーブロック処理開始',
            [
                'user_id' => $loginUser->id,
                'blocked_user_id' => $user->id,
            ]
        );

        Block::firstOrCreate([
            'blocker_id' => $loginUser->id,
            'blocked_user_id' => $user->id,
        ]);

        ApiActionLogger::info(
            'BlockController::store',
            'ユーザーブロック成功',
            [
                'user_id' => $loginUser->id,
                'blocked_user_id' => $user->id,
            ]
        );

        return back()->with('success', 'ユーザーをブロックしました。');
    }

    public function destroy(User $user): RedirectResponse
    {
        $loginUser = auth()->user();

        abort_unless($loginUser, 403);
        abort_if((int) $user->id === (int) $loginUser->id, 403);

        ApiActionLogger::info(
            'BlockController::destroy',
            'ユーザーブロック解除処理開始',
            [
                'user_id' => $loginUser->id,
                'blocked_user_id' => $user->id,
            ]
        );

        Block::where('blocker_id', $loginUser->id)
            ->where('blocked_user_id', $user->id)
            ->delete();

        ApiActionLogger::info(
            'BlockController::destroy',
            'ユーザーブロック解除成功',
            [
                'user_id' => $loginUser->id,
                'blocked_user_id' => $user->id,
            ]
        );

        return back()->with('success', 'ブロックを解除しました。');
    }
}
