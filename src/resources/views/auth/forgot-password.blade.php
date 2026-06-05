@extends('layouts.app')

@section('title', 'パスワード再設定')

@section('content')
<div class="min-h-screen bg-slate-50">
    <div class="mx-auto flex min-h-[calc(100vh-80px)] max-w-7xl items-center justify-center px-4 py-12 sm:px-6 lg:px-8">
        <div class="w-full max-w-md">
            <div class="rounded-3xl bg-white p-6 shadow-sm ring-1 ring-slate-200 sm:p-8">
                <div class="mb-4 text-center">

                    <h1 class="mt-5 text-2xl font-black text-slate-900">
                        パスワード再設定
                    </h1>

                    <p class="mt-2 text-sm leading-6 text-slate-600">
                        登録済みのメールアドレスを入力してください。
                        パスワード再設定用のリンクを送信します。
                    </p>
                </div>

                @if (session('status'))
                    <div class="mb-6 rounded-xl bg-emerald-50 p-4">
                        <p class="text-sm font-semibold text-emerald-700">
                            {{ session('status') }}
                        </p>
                    </div>
                @endif

                <form method="POST" action="{{ route('password.email') }}" class="space-y-5">
                    @csrf

                    <div>
                        <label for="email" class="mb-2 block text-sm font-bold text-slate-700">
                            メールアドレス
                        </label>

                        <input
                            id="email"
                            type="email"
                            name="email"
                            value="{{ old('email') }}"
                            required
                            autofocus
                            placeholder="example@example.com"
                            class="block w-full rounded-xl border-slate-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                        >

                        @error('email')
                            <p class="mt-2 text-sm font-semibold text-rose-600">
                                {{ $message }}
                            </p>
                        @enderror
                    </div>

                    <button
                        type="submit"
                        class="flex w-full items-center justify-center rounded-xl bg-indigo-600 px-5 py-3 text-sm font-bold text-white shadow-sm transition hover:bg-indigo-700"
                    >
                        再設定メールを送信
                    </button>
                </form>

                <div class="mt-6 border-t border-slate-200 pt-6 text-center">
                    <a
                        href="{{ route('login') }}"
                        class="text-sm font-bold text-indigo-600 hover:text-indigo-700"
                    >
                        ログイン画面へ戻る
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
