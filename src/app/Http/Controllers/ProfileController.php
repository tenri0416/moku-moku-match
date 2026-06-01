<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProfileUpdateRequest;
use App\Models\Prefecture;
use App\Support\ApiActionLogger;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class ProfileController extends Controller
{
    /**
     * Display the user's profile form.
     */
    public function edit(Request $request): View
    {
        ApiActionLogger::info(
            'ProfileController::edit',
            'ユーザープロフィール編集画面にアクセス',
            [
                'user_id' => auth()->id(),
            ]
        );

        $profile = auth()->user()->profile;
        $prefectures = Prefecture::orderBy('id')->get();

        return view('profile.edit', compact('profile', 'prefectures'));
    }

    /**
     * Update the user's profile information.
     */
    public function update(ProfileUpdateRequest $request): RedirectResponse
    {
        ApiActionLogger::info(
            'ProfileController::update',
            'ユーザープロフィール更新処理開始',
            [
                'user_id' => auth()->id(),
                'has_avatar' => $request->hasFile('avatar'),
            ]
        );

        $validated = $request->validated();

        $profile = auth()->user()->profile;

        if ($request->hasFile('avatar')) {
            if ($profile && $profile->avatar_path) {
                Storage::disk('public')->delete($profile->avatar_path);
            }

            $validated['avatar_path'] = $request->file('avatar')->store('avatars', 'public');
        }

        unset($validated['avatar']);

        auth()->user()->profile()->updateOrCreate(
            ['user_id' => auth()->id()],
            $validated
        );

        ApiActionLogger::info(
            'ProfileController::update',
            'ユーザープロフィール更新成功',
            [
                'user_id' => auth()->id(),
                'has_avatar' => isset($validated['avatar_path']),
            ]
        );

        return redirect()->route('mypage')->with('success', 'プロフィールを保存しました。');
    }

    /**
     * Delete the user's account.
     */
    public function destroy(Request $request): RedirectResponse
    {
        ApiActionLogger::info(
            'ProfileController::destroy',
            'ユーザー退会処理開始',
            [
                'user_id' => auth()->id(),
            ]
        );

        $request->validateWithBag('userDeletion', [
            'password' => ['required', 'current_password'],
        ]);

        $user = $request->user();
        $userId = $user->id;

        Auth::logout();

        $user->delete();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        ApiActionLogger::info(
            'ProfileController::destroy',
            'ユーザー退会処理成功',
            [
                'deleted_user_id' => $userId,
            ]
        );

        return Redirect::to('/');
    }
}
