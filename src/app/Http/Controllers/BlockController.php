<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Block;
use App\Models\User;

class BlockController extends Controller
{
    public function store(User $user)
    {
        abort_if($user->id === auth()->id(), 403);

        Block::firstOrCreate([
            'blocker_id' => auth()->id(),
            'blocked_user_id' => $user->id,
        ]);

        return back()->with('success', 'ユーザーをブロックしました。');
    }

    public function destroy(User $user)
    {
        Block::where('blocker_id', auth()->id())
            ->where('blocked_user_id', $user->id)
            ->delete();

        return back()->with('success', 'ブロックを解除しました。');
    }
}
