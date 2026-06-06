<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ArticleInquiry;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\View\View;

class ArticleInquiryController extends Controller
{
    /**
     * お問い合わせ一覧。
     */
    public function index(Request $request): View
    {
        $status = $request->query('status');

        $query = ArticleInquiry::query()->latest();

        if ($status !== null && $status !== '') {
            $query->where('status', (int) $status);
        }

        $inquiries = $query->paginate(20)->withQueryString();

        return view('admin.article-inquiries.index', compact('inquiries', 'status'));
    }

    /**
     * お問い合わせ詳細。
     */
    public function show(ArticleInquiry $articleInquiry): View
    {
        return view('admin.article-inquiries.show', compact('articleInquiry'));
    }

    /**
     * 管理者から問い合わせユーザーへ返信する。
     */
    public function reply(Request $request, ArticleInquiry $articleInquiry): RedirectResponse
    {
        $validated = $request->validate([
            'reply_body' => ['required', 'string', 'max:5000'],
        ], [
            'reply_body.required' => '返信内容を入力してください。',
            'reply_body.max' => '返信内容は5000文字以内で入力してください。',
        ]);

        Mail::raw($validated['reply_body'], function ($message) use ($articleInquiry) {
            $message
                ->to($articleInquiry->email)
                ->subject('【YomuWorks】お問い合わせへのご返信');
        });

        $articleInquiry->update([
            'admin_reply_body' => $validated['reply_body'],
            'replied_at' => now(),
            'status' => ArticleInquiry::STATUS_REPLIED,
        ]);

        return redirect()
            ->route('admin.article-inquiries.show', $articleInquiry)
            ->with('success', '返信メールを送信しました。');
    }
}
