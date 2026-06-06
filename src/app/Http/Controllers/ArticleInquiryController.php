<?php

namespace App\Http\Controllers;

use App\Models\ArticleInquiry;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class ArticleInquiryController extends Controller
{
    /**
     * YomuWorksお問い合わせを保存する。
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'email' => ['required', 'email', 'max:255'],
            'body' => ['required', 'string', 'max:5000'],
        ], [
            'email.required' => 'メールアドレスを入力してください。',
            'email.email' => 'メールアドレスの形式で入力してください。',
            'body.required' => 'お問い合わせ内容を入力してください。',
            'body.max' => 'お問い合わせ内容は5000文字以内で入力してください。',
        ]);

        ArticleInquiry::create([
            'email' => $validated['email'],
            'body' => $validated['body'],
            'status' => ArticleInquiry::STATUS_OPEN,
        ]);

        return back()->with('article_inquiry_success', 'お問い合わせを送信しました。内容を確認後、必要に応じてご連絡します。');
    }
}
