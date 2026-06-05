@extends('layouts.app')

@section('title', '退会手続き')

@section('content')
<div class="min-h-screen bg-slate-50 px-4 py-10">
    <div class="mx-auto max-w-2xl">
        <div class="rounded-3xl bg-white p-6 shadow-sm ring-1 ring-slate-200 sm:p-8">
            <p class="text-sm font-bold text-rose-600">WITHDRAWAL</p>

            <h1 class="mt-2 text-2xl font-black text-slate-900">
                退会手続き
            </h1>

            <p class="mt-3 text-sm leading-7 text-slate-600">
                退会すると、ログインできなくなります。投稿・メッセージ・トレーニング履歴は、サービス運営上の整合性を保つため削除せず、退会済みユーザーとして保持されます。
            </p>

            <div class="mt-6 rounded-2xl border border-rose-200 bg-rose-50 p-4 text-sm leading-7 text-rose-800">
                <p class="font-bold">注意事項</p>
                <ul class="mt-2 list-disc space-y-1 pl-5">
                    <li>退会後はログインできません。</li>
                    <li>退会後の復元は管理者対応が必要です。</li>
                    <li>投稿やメッセージ履歴は完全削除されません。</li>
                </ul>
            </div>

            <form method="POST" action="{{ route('withdrawal.destroy') }}" class="mt-6 space-y-5">
                @csrf
                @method('DELETE')

                <div>
                    <label class="block text-sm font-bold text-slate-700">
                        退会理由 任意
                    </label>
                    <textarea
                        name="withdrawal_reason"
                        rows="4"
                        class="mt-2 w-full rounded-2xl border-slate-300 text-sm shadow-sm focus:border-rose-500 focus:ring-rose-500"
                        placeholder="よろしければ退会理由を教えてください。"
                    >{{ old('withdrawal_reason') }}</textarea>

                    @error('withdrawal_reason')
                        <p class="mt-2 text-sm font-bold text-rose-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label class="block text-sm font-bold text-slate-700">
                        確認のため「退会します」と入力してください
                    </label>
                    <input
                        type="text"
                        name="confirm_text"
                        value="{{ old('confirm_text') }}"
                        class="mt-2 w-full rounded-2xl border-slate-300 text-sm shadow-sm focus:border-rose-500 focus:ring-rose-500"
                        placeholder="退会します"
                    >

                    @error('confirm_text')
                        <p class="mt-2 text-sm font-bold text-rose-600">{{ $message }}</p>
                    @enderror
                </div>

                <div class="flex flex-col gap-3 sm:flex-row sm:justify-between">
                    <a
                        href="{{ route('mypage') }}"
                        class="inline-flex items-center justify-center rounded-2xl border border-slate-300 bg-white px-5 py-3 text-sm font-bold text-slate-700 hover:bg-slate-50"
                    >
                        マイページへ戻る
                    </a>

                    <button
                        type="submit"
                        class="inline-flex items-center justify-center rounded-2xl bg-rose-600 px-5 py-3 text-sm font-black text-white hover:bg-rose-700"
                        onclick="return confirm('本当に退会しますか？この操作後はログインできなくなります。');"
                    >
                        退会する
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
