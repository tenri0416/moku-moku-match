<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Support\ApiActionLogger;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;

class PasswordController extends Controller
{
    /**
     * Update the user's password.
     */
    public function update(Request $request): RedirectResponse
    {
        ApiActionLogger::info(
            'PasswordController::update',
            'ユーザーパスワード更新処理開始',
            [
                'user_id' => $request->user()->id,
                'email' => $request->user()->email,
            ]
        );

        $validated = $request->validateWithBag('updatePassword', [
            'current_password' => ['required', 'current_password'],
            'password' => ['required', Password::defaults(), 'confirmed'],
        ]);

        $request->user()->update([
            'password' => Hash::make($validated['password']),
        ]);

        ApiActionLogger::info(
            'PasswordController::update',
            'ユーザーパスワード更新成功',
            [
                'user_id' => $request->user()->id,
                'email' => $request->user()->email,
            ]
        );

        return back()->with('status', 'password-updated');
    }
}
