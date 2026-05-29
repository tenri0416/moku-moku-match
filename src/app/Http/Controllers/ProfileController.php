<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProfileUpdateRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use Illuminate\View\View;
use Illuminate\Support\Facades\Log;
use App\Models\Prefecture;
use Illuminate\Support\Facades\Storage;

class ProfileController extends Controller
{
    /**
     * Display the user's profile form.
     */
    public function edit(Request $request): View
    {
        Log::info('ユーザープロフィールの編集画面にアクセスされました。', [
            'user_id' => auth()->id(),
        ]);
    
        $profile = auth()->user()->profile;
        $prefectures = Prefecture::orderBy('id')->get();
    
        return view('profile.edit', compact('profile', 'prefectures'));
    }

    /**
     * Update the user's profile information.
     */
    public function update(ProfileUpdateRequest $request): RedirectResponse
    {
        $validated = $request->validated();

        $profile = auth()->user()->profile;

        // プロフィール画像がアップロードされた場合
        if ($request->hasFile('avatar')) {
            // 既存画像がある場合は削除
            if ($profile && $profile->avatar_path) {
                Storage::disk('public')->delete($profile->avatar_path);
            }

            // 新しい画像を保存
            $validated['avatar_path'] = $request->file('avatar')->store('avatars', 'public');
        }

        // avatar は profiles テーブルに保存しないため除外
        unset($validated['avatar']);

        auth()->user()->profile()->updateOrCreate(
            ['user_id' => auth()->id()],
            $validated
        );

        return redirect()->route('mypage')->with('success', 'プロフィールを保存しました。');
    }

    /**
     * Delete the user's account.
     */
    public function destroy(Request $request): RedirectResponse
    {
        $request->validateWithBag('userDeletion', [
            'password' => ['required', 'current_password'],
        ]);

        $user = $request->user();

        Auth::logout();

        $user->delete();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return Redirect::to('/');
    }
}
