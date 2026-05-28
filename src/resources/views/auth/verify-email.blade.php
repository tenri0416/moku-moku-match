@extends('layouts.app')

@section('title', 'メールアドレス認証')

@section('content')
<div class="min-h-screen bg-slate-50 py-12">
    <div class="mx-auto max-w-xl px-4">
        <div class="rounded-2xl bg-white p-8 shadow-sm ring-1 ring-slate-200">
            <h1 class="text-2xl font-bold text-slate-900">
                メールアドレスの認証が必要です
            </h1>

            <p class="mt-4 leading-8 text-slate-600">
                この機能を利用するには、メールアドレスの認証が必要です。
                登録されているメールアドレス宛に認証メールを送信してください。
            </p>

            @if (session('status'))
                <div class="mt-5 rounded-lg bg-green-50 p-4 text-sm text-green-700">
                    @if (session('status') === 'verification-link-sent')
                        認証メールを送信しました。メール内のリンクをクリックして認証を完了してください。
                    @else
                        {{ session('status') }}
                    @endif
                </div>
            @endif

            <form method="POST" action="{{ route('verification.send') }}" class="mt-6">
                @csrf

                <button
                    type="submit"
                    class="w-full rounded-xl bg-indigo-600 px-5 py-3 text-sm font-bold text-white hover:bg-indigo-700"
                >
                    認証メールを送信する
                </button>
            </form>

            <div class="mt-5 text-center">
                <a
                    href="{{ route('mypage') }}"
                    class="text-sm font-bold text-slate-600 hover:text-slate-900"
                >
                    マイページに戻る
                </a>
            </div>
        </div>
    </div>
</div>
@endsection
