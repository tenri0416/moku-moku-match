<?php

namespace App\Http\Controllers;

use App\Models\Block;
use App\Models\User;
use App\Support\ApiActionLogger;

class BlockController extends Controller
{
    public function store(User $user)
    {
        ApiActionLogger::info(
            'BlockController::store',
            'ユーザーブロック処理開始',
            [
                'user_id' => auth()->id(),
                'blocked_user_id' => $user->id,
            ]
        );

        abort_if($user->id === auth()->id(), 403);

        Block::firstOrCreate([
            'blocker_id' => auth()->id(),
            'blocked_user_id' => $user->id,
        ]);

        ApiActionLogger::info(
            'BlockController::store',
            'ユーザーブロック成功',
            [
                'user_id' => auth()->id(),
                'blocked_user_id' => $user->id,
            ]
        );

        return back()->with('success', 'ユーザーをブロックしました。');
    }

    public function destroy(User $user)
    {
        ApiActionLogger::info(
            'BlockController::destroy',
            'ユーザーブロック解除処理開始',
            [
                'user_id' => auth()->id(),
                'blocked_user_id' => $user->id,
            ]
        );

        Block::where('blocker_id', auth()->id())
            ->where('blocked_user_id', $user->id)
            ->delete();

        ApiActionLogger::info(
            'BlockController::destroy',
            'ユーザーブロック解除成功',
            [
                'user_id' => auth()->id(),
                'blocked_user_id' => $user->id,
            ]
        );

        return back()->with('success', 'ブロックを解除しました。');
    }
}
