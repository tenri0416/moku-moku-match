<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class WithdrawalController extends Controller
{
    public function edit()
    {
        if (session('admin_impersonation.active')) {
            return redirect()
                ->route('mypage')
                ->with('error', '管理者代理ログイン中はユーザー本人の退会手続きはできません。管理者画面から強制退会してください。');
        }
        return view('withdrawal.edit');
    }

    public function destroy(Request $request)
    {

        if (session('admin_impersonation.active')) {
            return redirect()
                ->route('mypage')
                ->with('error', '管理者代理ログイン中はユーザー本人の退会手続きはできません。管理者画面から強制退会してください。');
        }
        $request->validate([
            'confirm_text' => ['required', 'string', 'in:退会します'],
            'withdrawal_reason' => ['nullable', 'string', 'max:1000'],
        ], [
            'confirm_text.required' => '確認文言を入力してください。',
            'confirm_text.in' => '「退会します」と正しく入力してください。',
            'withdrawal_reason.max' => '退会理由は1000文字以内で入力してください。',
        ]);

        $user = Auth::user();

        $user->forceFill([
            'status' => \App\Models\User::STATUS_WITHDRAWN,
            'withdrawn_at' => now(),
            'withdrawal_reason' => $request->withdrawal_reason,
            'withdrawal_type' => \App\Models\User::WITHDRAWAL_TYPE_SELF,
            'withdrawn_by_admin_id' => null,

            // 自主退会なので利用停止情報は入れない
            'suspended_at' => null,
            'suspension_reason' => null,
            'suspended_by_admin_id' => null,
        ])->save();

        Log::info('ユーザーが退会しました。', [
            'user_id' => $user->id,
            'email' => $user->email,
        ]);

        Auth::guard('web')->logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()
            ->route('home')
            ->with('status', '退会処理が完了しました。ご利用ありがとうございました。');
    }
}
