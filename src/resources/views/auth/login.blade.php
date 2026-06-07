@extends('layouts.app')

@section('title', 'ログイン')

@section('content')
<div class="min-h-screen bg-slate-50">
    <div class="mx-auto flex min-h-[calc(100vh-80px)] max-w-7xl justify-center px-4 pt-4 pb-12 sm:px-6 lg:px-8">
        <div class="w-full max-w-md">

            {{-- Login Card --}}
            <section class="w-full">
                <div class="rounded-3xl bg-white p-4 shadow-sm ring-1 ring-slate-200 sm:p-8">
                    <div class="mb-2 text-center">
                        <h2 class="text-2xl font-black text-slate-900">
                            ログイン
                        </h2>

                        <p class="mt-2 text-sm leading-6 text-slate-600">
                            メールアドレス、またはGoogleアカウントでログインできます。
                        </p>
                    </div>

                    {{-- Session Status --}}
                    @if (session('status'))
                        <div class="mb-6 rounded-xl bg-emerald-50 p-4">
                            <p class="text-sm font-semibold text-emerald-700">
                                {{ session('status') }}
                            </p>
                        </div>
                    @endif

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
                        <svg class="h-5 w-5" viewBox="0 0 533.5 544.3" aria-hidden="true">
                            <path fill="#4285F4" d="M533.5 278.4c0-18.5-1.5-37.1-4.7-55.3H272.1v104.7h147c-6.1 33.8-25.7 63.7-54.4 82.7v68h88.1c51.6-47.5 80.7-117.6 80.7-200.1z"/>
                            <path fill="#34A853" d="M272.1 544.3c73.7 0 135.8-24.2 181.1-65.8l-88.1-68c-24.5 16.7-56.1 26.2-93 26.2-71.4 0-131.9-48.2-153.6-112.9H27.7v70.1c46.4 92.3 140.8 150.4 244.4 150.4z"/>
                            <path fill="#FBBC04" d="M118.5 323.8c-11.5-33.8-11.5-70.6 0-104.4v-70.1H27.7c-38.8 77.1-38.8 168.2 0 245.3l90.8-70.8z"/>
                            <path fill="#EA4335" d="M272.1 107.7c38.9-.6 76.3 14 104.6 40.7l78-78C405.3 24.2 339.8-1.2 272.1 0 168.5 0 74.1 58.1 27.7 150.4l90.8 70.1c21.7-64.7 82.2-112.8 153.6-112.8z"/>
                        </svg>
                            <span>Googleでログイン</span>
                        </a>

                        <div class="relative mb-6">
                            <div class="absolute inset-0 flex items-center" aria-hidden="true">
                                <div class="w-full border-t border-slate-200"></div>
                            </div>
                            <div class="relative flex justify-center">
                                <span class="bg-white px- text-xs font-bold text-slate-400">
                                    または
                                </span>
                            </div>
                        </div>
                    @endif

                    <form method="POST" action="{{ route('login') }}" class="space-y-5">
                        @csrf

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
                                autofocus
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
                                autocomplete="current-password"
                                placeholder="パスワードを入力"
                                class="block w-full rounded-xl border-slate-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                            >

                            @error('password')
                                <p class="mt-2 text-sm font-semibold text-rose-600">
                                    {{ $message }}
                                </p>
                            @enderror
                        </div>

                        {{-- Remember --}}
                        <div class="flex items-center justify-between gap-4">
                            <label for="remember_me" class="flex items-center gap-2 text-sm font-semibold text-slate-600">
                                <input
                                    id="remember_me"
                                    type="checkbox"
                                    name="remember"
                                    class="rounded border-slate-300 text-indigo-600 shadow-sm focus:ring-indigo-500"
                                >
                                <span>ログイン状態を保持する</span>
                            </label>

                            @if (Route::has('password.request'))
                                <a
                                    href="{{ route('password.request') }}"
                                    class="text-sm font-bold text-indigo-600 hover:text-indigo-700"
                                >
                                    パスワードを忘れた方
                                </a>
                            @endif
                        </div>

                        {{-- Submit --}}
                        <button
                            type="submit"
                            class="flex w-full items-center justify-center rounded-xl bg-indigo-600 px-5 py-3 text-sm font-bold text-white shadow-sm transition hover:bg-indigo-700"
                        >
                            ログインする
                        </button>
                    </form>

                    {{-- Register --}}
                    @if (Route::has('register'))
                        <div class="mt-6 border-t border-slate-200 pt-6 text-center">
                            <p class="text-sm text-slate-600">
                                まだアカウントをお持ちではありませんか？
                            </p>

                            <a
                                href="{{ route('register') }}"
                                class="mt-3 inline-flex items-center justify-center rounded-xl border border-slate-300 bg-white px-5 py-3 text-sm font-bold text-slate-700 transition hover:bg-slate-50"
                            >
                                会員登録する
                            </a>
                        </div>
                    @endif
                </div>
            </section>
        </div>
    </div>
</div>
@endsection
