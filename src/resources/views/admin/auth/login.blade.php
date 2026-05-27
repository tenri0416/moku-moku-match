<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>管理者ログイン - MokuMoku Match</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body class="min-h-screen bg-slate-950 text-slate-100 antialiased">
<div class="flex min-h-screen">
    {{-- Left Area --}}
    <section class="hidden w-1/2 items-center justify-center bg-gradient-to-br from-slate-950 via-slate-900 to-indigo-950 px-12 lg:flex">
        <div class="max-w-lg">
            <div class="flex h-16 w-16 items-center justify-center rounded-3xl bg-indigo-600 text-2xl font-black text-white shadow-lg shadow-indigo-900/40">
                A
            </div>

            <p class="mt-8 text-sm font-bold tracking-wide text-indigo-300">
                ADMIN LOGIN
            </p>

            <h1 class="mt-4 text-4xl font-black leading-tight tracking-tight text-white">
                MokuMoku Match<br>
                管理画面
            </h1>

            <p class="mt-6 leading-8 text-slate-300">
                ユーザー、募集投稿、通報内容を管理するための管理者専用ログインページです。
                一般ユーザーのログインは通常ログインページを利用してください。
            </p>

            <div class="mt-8 grid gap-4 sm:grid-cols-3">
                <div class="rounded-2xl bg-white/10 p-5 ring-1 ring-white/10">
                    <div class="text-2xl">👤</div>
                    <p class="mt-3 text-sm font-bold text-white">
                        ユーザー管理
                    </p>
                </div>

                <div class="rounded-2xl bg-white/10 p-5 ring-1 ring-white/10">
                    <div class="text-2xl">📝</div>
                    <p class="mt-3 text-sm font-bold text-white">
                        募集管理
                    </p>
                </div>

                <div class="rounded-2xl bg-white/10 p-5 ring-1 ring-white/10">
                    <div class="text-2xl">⚠️</div>
                    <p class="mt-3 text-sm font-bold text-white">
                        通報管理
                    </p>
                </div>
            </div>
        </div>
    </section>

    {{-- Login Area --}}
    <section class="flex w-full items-center justify-center px-4 py-12 sm:px-6 lg:w-1/2 lg:px-8">
        <div class="w-full max-w-md">
            <div class="rounded-3xl bg-white p-6 text-slate-900 shadow-2xl shadow-slate-950/40 ring-1 ring-slate-800/10 sm:p-8">
                <div class="mb-8 text-center">
                    <div class="mx-auto flex h-14 w-14 items-center justify-center rounded-2xl bg-slate-900 text-xl font-black text-white shadow-sm">
                        A
                    </div>

                    <h2 class="mt-5 text-2xl font-black text-slate-900">
                        管理者ログイン
                    </h2>

                    <p class="mt-2 text-sm leading-6 text-slate-600">
                        管理者アカウントのメールアドレスとパスワードを入力してください。
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

                {{-- Error Summary --}}
                @if ($errors->any())
                    <div class="mb-6 rounded-xl bg-rose-50 p-4">
                        <p class="text-sm font-semibold text-rose-700">
                            入力内容を確認してください。
                        </p>
                    </div>
                @endif

                <form method="POST" action="{{ route('admin.login.store') }}" class="space-y-5">
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
                            placeholder="admin@example.com"
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
                    <label for="remember_me" class="flex items-center gap-2 text-sm font-semibold text-slate-600">
                        <input
                            id="remember_me"
                            type="checkbox"
                            name="remember"
                            class="rounded border-slate-300 text-indigo-600 shadow-sm focus:ring-indigo-500"
                        >
                        <span>ログイン状態を保持する</span>
                    </label>

                    {{-- Submit --}}
                    <button
                        type="submit"
                        class="flex w-full items-center justify-center rounded-xl bg-slate-900 px-5 py-3 text-sm font-bold text-white shadow-sm transition hover:bg-slate-800"
                    >
                        管理画面にログイン
                    </button>
                </form>

                <div class="mt-6 border-t border-slate-200 pt-6 text-center">
                    <a
                        href="{{ route('login') }}"
                        class="text-sm font-bold text-indigo-600 hover:text-indigo-700"
                    >
                        一般ユーザー用ログインへ戻る
                    </a>
                </div>
            </div>

            <p class="mt-6 text-center text-xs leading-6 text-slate-400">
                このページは管理者専用です。権限のないユーザーはログインできません。
            </p>
        </div>
    </section>
</div>
</body>
</html>
