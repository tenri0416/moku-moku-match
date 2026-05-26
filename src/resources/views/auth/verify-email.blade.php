@extends('layouts.app')

@section('title', 'メール認証')

@section('content')
<div class="min-h-screen bg-slate-50">
    <div class="mx-auto flex min-h-[calc(100vh-80px)] max-w-7xl items-center justify-center px-4 py-12 sm:px-6 lg:px-8">
        <div class="w-full max-w-md">
            <div class="rounded-3xl bg-white p-6 text-center shadow-sm ring-1 ring-slate-200 sm:p-8">
                <div class="mx-auto flex h-14 w-14 items-center justify-center rounded-2xl bg-indigo-600 text-xl font-black text-white shadow-sm">
                    M
                </div>

                <h1 class="mt-5 text-2xl font-black text-slate-900">
                    メール認証
                </h1>

                <p class="mt-3 text-sm leading-7 text-slate-600">
                    ご登録ありがとうございます。
                    利用を開始する前に、登録されたメールアドレス宛に送信された認証リンクをクリックしてください。
                </p>

                @if (session('status') == 'verification-link-sent')
                    <div class="mt-6 rounded-xl bg-emerald-50 p-4 text-left">
                        <p class="text-sm font-semibold text-emerald-700">
                            新しい認証リンクをメールアドレス宛に送信しました。
                        </p>
                    </div>
                @endif

                <div class="mt-8 space-y-3">
                    <form method="POST" action="{{ route('verification.send') }}">
                        @csrf

                        <button
                            type="submit"
                            class="flex w-full items-center justify-center rounded-xl bg-indigo-600 px-5 py-3 text-sm font-bold text-white shadow-sm transition hover:bg-indigo-700"
                        >
                            認証メールを再送信する
                        </button>
                    </form>

                    <form method="POST" action="{{ route('logout') }}">
                        @csrf

                        <button
                            type="submit"
                            class="flex w-full items-center justify-center rounded-xl border border-slate-300 bg-white px-5 py-3 text-sm font-bold text-slate-700 transition hover:bg-slate-50"
                        >
                            ログアウト
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
