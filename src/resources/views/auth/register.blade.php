@extends('layouts.app')

@section('title', '会員登録')

@section('content')
<div class="min-h-screen bg-slate-50">
    <div class="mx-auto flex min-h-[calc(100vh-80px)] max-w-7xl items-center px-4 py-12 sm:px-6 lg:px-8">
        <div class="grid w-full gap-8 lg:grid-cols-2 lg:items-center">
            {{-- Left --}}
            <section class="hidden lg:block">
                <p class="text-sm font-bold text-indigo-600">
                    USER REGISTER
                </p>

                <h1 class="mt-4 text-4xl font-black leading-tight tracking-tight text-slate-900">
                    一人で頑張る時間を、<br>
                    誰かと一緒に進めよう。
                </h1>

                <p class="mt-6 max-w-xl leading-8 text-slate-600">
                    MokuMoku Matchは、フルリモートで働くITエンジニアや学習者が、
                    一緒に黙々作業・勉強・情報交換できる相手を探すためのサービスです。
                </p>

                <div class="mt-8 grid gap-4 sm:grid-cols-3">
                    <div class="rounded-2xl bg-white p-5 shadow-sm ring-1 ring-slate-200">
                        <div class="text-2xl">💻</div>
                        <p class="mt-3 text-sm font-bold text-slate-900">黙々作業</p>
                    </div>

                    <div class="rounded-2xl bg-white p-5 shadow-sm ring-1 ring-slate-200">
                        <div class="text-2xl">📚</div>
                        <p class="mt-3 text-sm font-bold text-slate-900">勉強仲間</p>
                    </div>

                    <div class="rounded-2xl bg-white p-5 shadow-sm ring-1 ring-slate-200">
                        <div class="text-2xl">🤝</div>
                        <p class="mt-3 text-sm font-bold text-slate-900">情報交換</p>
                    </div>
                </div>
            </section>

            {{-- Register Card --}}
            <section class="mx-auto w-full max-w-md">
                <div class="rounded-3xl bg-white p-6 shadow-sm ring-1 ring-slate-200 sm:p-8">
                    <div class="mb-8 text-center">
                        <div class="mx-auto flex h-14 w-14 items-center justify-center rounded-2xl bg-indigo-600 text-xl font-black text-white shadow-sm">
                            M
                        </div>

                        <h2 class="mt-5 text-2xl font-black text-slate-900">
                            会員登録
                        </h2>

                        <p class="mt-2 text-sm leading-6 text-slate-600">
                            メールアドレス、またはGoogleアカウントで登録できます。
                        </p>
                    </div>

                    @if (session('error'))
                        <div class="mb-6 rounded-xl bg-rose-50 p-4">
                            <p class="text-sm font-semibold text-rose-700">
                                {{ session('error') }}
                            </p>
                        </div>
                    @endif

                    @if (Route::has('auth.redirect'))
                        <a
                            href="{{ route('auth.redirect') }}"
                            class="mb-6 flex w-full items-center justify-center gap-3 rounded-xl border border-slate-300 bg-white px-5 py-3 text-sm font-black text-slate-700 shadow-sm transition hover:bg-slate-50"
                        >
                            <span class="flex h-6 w-6 items-center justify-center rounded-full bg-white text-base font-black text-blue-600 ring-1 ring-slate-200">
                                G
                            </span>
                            <span>Googleで会員登録</span>
                        </a>

                        <div class="relative mb-6">
                            <div class="absolute inset-0 flex items-center" aria-hidden="true">
                                <div class="w-full border-t border-slate-200"></div>
                            </div>
                            <div class="relative flex justify-center">
                                <span class="bg-white px-3 text-xs font-bold text-slate-400">
                                    または
                                </span>
                            </div>
                        </div>
                    @endif

                    <form method="POST" action="{{ route('register') }}" class="space-y-5">
                        @csrf

                        {{-- Name --}}
                        <div>
                            <label for="name" class="mb-2 block text-sm font-bold text-slate-700">
                                名前
                            </label>

                            <input
                                id="name"
                                type="text"
                                name="name"
                                value="{{ old('name') }}"
                                required
                                autofocus
                                autocomplete="name"
                                placeholder="例：山田 太郎"
                                class="block w-full rounded-xl border-slate-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                            >

                            @error('name')
                                <p class="mt-2 text-sm font-semibold text-rose-600">
                                    {{ $message }}
                                </p>
                            @enderror
                        </div>

                        {{-- Email --}}
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
                                autocomplete="username"
                                placeholder="example@example.com"
                                class="block w-full rounded-xl border-slate-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                            >

                            @error('email')
                                <p class="mt-2 text-sm font-semibold text-rose-600">
                                    {{ $message }}
                                </p>
                            @enderror
                        </div>

                        {{-- Password --}}
                        <div>
                            <label for="password" class="mb-2 block text-sm font-bold text-slate-700">
                                パスワード
                            </label>

                            <input
                                id="password"
                                type="password"
                                name="password"
                                required
                                autocomplete="new-password"
                                placeholder="パスワードを入力"
                                class="block w-full rounded-xl border-slate-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                            >

                            @error('password')
                                <p class="mt-2 text-sm font-semibold text-rose-600">
                                    {{ $message }}
                                </p>
                            @enderror
                        </div>

                        {{-- Password Confirmation --}}
                        <div>
                            <label for="password_confirmation" class="mb-2 block text-sm font-bold text-slate-700">
                                パスワード確認
                            </label>

                            <input
                                id="password_confirmation"
                                type="password"
                                name="password_confirmation"
                                required
                                autocomplete="new-password"
                                placeholder="確認用パスワードを入力"
                                class="block w-full rounded-xl border-slate-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                            >

                            @error('password_confirmation')
                                <p class="mt-2 text-sm font-semibold text-rose-600">
                                    {{ $message }}
                                </p>
                            @enderror
                        </div>

                        <button
                            type="submit"
                            class="flex w-full items-center justify-center rounded-xl bg-indigo-600 px-5 py-3 text-sm font-bold text-white shadow-sm transition hover:bg-indigo-700"
                        >
                            会員登録する
                        </button>
                    </form>

                    <div class="mt-6 border-t border-slate-200 pt-6 text-center">
                        <p class="text-sm text-slate-600">
                            すでにアカウントをお持ちですか？
                        </p>

                        <a
                            href="{{ route('login') }}"
                            class="mt-3 inline-flex items-center justify-center rounded-xl border border-slate-300 bg-white px-5 py-3 text-sm font-bold text-slate-700 transition hover:bg-slate-50"
                        >
                            ログインする
                        </a>
                    </div>
                </div>
            </section>
        </div>
    </div>
</div>
@endsection
