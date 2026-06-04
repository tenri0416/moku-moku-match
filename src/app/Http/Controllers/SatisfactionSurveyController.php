<?php

namespace App\Http\Controllers;

use App\Models\UserSatisfactionSurvey;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class SatisfactionSurveyController extends Controller
{
    /**
     * 満足度調査アンケートを保存する。
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'satisfaction' => ['required', 'integer', 'between:1,5'],
            'improvement_text' => ['nullable', 'string', 'max:500'],
        ], [
            'satisfaction.required' => '満足度を選択してください。',
            'satisfaction.integer' => '満足度の値が正しくありません。',
            'satisfaction.between' => '満足度の値が正しくありません。',
            'improvement_text.max' => '改善してほしい点は500文字以内で入力してください。',
        ]);

        UserSatisfactionSurvey::create([
            'user_id' => Auth::id(),
            'status' => UserSatisfactionSurvey::STATUS_ANSWERED,
            'satisfaction' => $validated['satisfaction'],
            'improvement_text' => $validated['improvement_text'] ?? null,
            'next_display_at' => now()->addDays((int) config('satisfaction_survey.interval_days', 30)),
        ]);

        return back()->with('success', 'アンケートにご回答いただきありがとうございます。');
    }

    /**
     * 今月は回答しない。
     */
    public function skip(Request $request): RedirectResponse
    {
        UserSatisfactionSurvey::create([
            'user_id' => Auth::id(),
            'status' => UserSatisfactionSurvey::STATUS_SKIPPED,
            'satisfaction' => null,
            'improvement_text' => null,
            'next_display_at' => now()->addDays((int) config('satisfaction_survey.interval_days', 30)),
        ]);

        return back()->with('success', '今月はアンケートを表示しないようにしました。');
    }
}
