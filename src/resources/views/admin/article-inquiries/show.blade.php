@extends('layouts.admin')

@section('title', 'お問い合わせ詳細')

@section('content')
<div class="min-h-screen bg-slate-50">
    <div class="mx-auto max-w-4xl px-4 py-10 sm:px-6 lg:px-8">
        <div class="mb-8">
            <a href="{{ route('admin.article-inquiries.index') }}" class="text-sm font-bold text-indigo-600">
                ← 一覧へ戻る
            </a>

            <p class="mt-6 text-sm font-bold text-indigo-600">
                ARTICLE INQUIRY DETAIL
            </p>

            <h1 class="mt-2 text-3xl font-bold text-slate-900">
                お問い合わせ詳細
            </h1>
        </div>

        @if (session('success'))
            <div class="mb-5 rounded-xl bg-emerald-50 px-4 py-3 text-sm font-bold text-emerald-700">
                {{ session('success') }}
            </div>
        @endif

        <div class="rounded-2xl bg-white p-6 shadow-sm ring-1 ring-slate-200">
            <dl class="space-y-5">
                <div>
                    <dt class="text-sm font-bold text-slate-500">メールアドレス</dt>
                    <dd class="mt-1 break-all text-slate-900">{{ $articleInquiry->email }}</dd>
                </div>

                <div>
                    <dt class="text-sm font-bold text-slate-500">状態</dt>
                    <dd class="mt-1">
                        @if ($articleInquiry->isReplied())
                            <span class="rounded-full bg-emerald-50 px-3 py-1 text-xs font-bold text-emerald-700">
                                返信済み
                            </span>
                        @else
                            <span class="rounded-full bg-amber-50 px-3 py-1 text-xs font-bold text-amber-700">
                                未対応
                            </span>
                        @endif
                    </dd>
                </div>

                <div>
                    <dt class="text-sm font-bold text-slate-500">お問い合わせ内容</dt>
                    <dd class="mt-2 whitespace-pre-wrap rounded-xl bg-slate-50 p-4 leading-7 text-slate-800">{{ $articleInquiry->body }}</dd>
                </div>

                @if ($articleInquiry->admin_reply_body)
                    <div>
                        <dt class="text-sm font-bold text-slate-500">返信内容</dt>
                        <dd class="mt-2 whitespace-pre-wrap rounded-xl bg-emerald-50 p-4 leading-7 text-emerald-900">{{ $articleInquiry->admin_reply_body }}</dd>

                        @if ($articleInquiry->replied_at)
                            <p class="mt-2 text-sm text-slate-500">
                                返信日時：{{ $articleInquiry->replied_at->format('Y/m/d H:i') }}
                            </p>
                        @endif
                    </div>
                @endif
            </dl>
        </div>

        <div class="mt-6 rounded-2xl bg-white p-6 shadow-sm ring-1 ring-slate-200">
            <h2 class="text-xl font-bold text-slate-900">
                このユーザーにメール返信する
            </h2>

            <form method="POST" action="{{ route('admin.article-inquiries.reply', $articleInquiry) }}" class="mt-5">
                @csrf

                <textarea
                    name="reply_body"
                    rows="8"
                    class="w-full rounded-xl border-slate-300"
                    placeholder="返信内容を入力してください"
                    required
                >{{ old('reply_body') }}</textarea>

                @error('reply_body')
                    <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                @enderror

                <button
                    type="submit"
                    class="mt-4 inline-flex items-center justify-center rounded-xl bg-indigo-600 px-5 py-3 text-sm font-bold text-white hover:bg-indigo-700"
                >
                    メールを送信する
                </button>
            </form>
        </div>
    </div>
</div>
@endsection
