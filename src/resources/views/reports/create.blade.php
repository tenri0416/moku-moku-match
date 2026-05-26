<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'MokuMoku Match')</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body class="min-h-screen bg-slate-50 text-slate-900 antialiased">
<header class="sticky top-0 z-50 border-b border-slate-200 bg-white/90 backdrop-blur">
    <nav class="mx-auto flex max-w-7xl items-center justify-between px-4 py-3 sm:px-6 lg:px-8">
        {{-- ロゴ --}}
        <a href="{{ route('home') }}" class="flex items-center gap-3">
            <span class="flex h-10 w-10 items-center justify-center rounded-2xl bg-indigo-600 text-base font-black text-white shadow-sm">
                M
            </span>

            <span class="leading-tight">
                <span class="block text-lg font-black tracking-tight text-slate-900">
                    MokuMoku Match
                </span>
                <span class="hidden text-xs font-semibold text-slate-500 sm:block">
                    リモート作業仲間を見つける
                </span>
            </span>
        </a>

        {{-- PCメニュー --}}
        <div class="hidden items-center gap-2 md:flex">
            <a
                href="{{ route('home') }}"
                class="rounded-xl px-3 py-2 text-sm font-bold text-slate-600 transition hover:bg-slate-100 hover:text-slate-900"
            >
                ホーム
            </a>

            <a
                href="{{ route('work-posts.index') }}"
                class="rounded-xl px-3 py-2 text-sm font-bold text-slate-600 transition hover:bg-slate-100 hover:text-slate-900"
            >
                募集一覧
            </a>

            @auth
                <a
                    href="{{ route('mypage') }}"
                    class="rounded-xl px-3 py-2 text-sm font-bold text-slate-600 transition hover:bg-slate-100 hover:text-slate-900"
                >
                    マイページ
                </a>

                <a
                    href="{{ route('work-posts.create') }}"
                    class="rounded-xl bg-indigo-600 px-4 py-2 text-sm font-bold text-white shadow-sm transition hover:bg-indigo-700"
                >
                    募集作成
                </a>

                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button
                        type="submit"
                        class="rounded-xl px-3 py-2 text-sm font-bold text-slate-600 transition hover:bg-slate-100 hover:text-slate-900"
                    >
                        ログアウト
                    </button>
                </form>
            @else
                <a
                    href="{{ route('login') }}"
                    class="rounded-xl px-3 py-2 text-sm font-bold text-slate-600 transition hover:bg-slate-100 hover:text-slate-900"
                >
                    ログイン
                </a>

                <a
                    href="{{ route('register') }}"
                    class="rounded-xl bg-indigo-600 px-4 py-2 text-sm font-bold text-white shadow-sm transition hover:bg-indigo-700"
                >
                    会員登録
                </a>
            @endauth
        </div>

        {{-- スマホメニュー --}}
        <details class="relative md:hidden">
            <summary class="cursor-pointer list-none rounded-xl border border-slate-200 bg-white px-4 py-2 text-sm font-bold text-slate-700 shadow-sm transition hover:bg-slate-50">
                メニュー
            </summary>

            <div class="absolute right-0 mt-3 w-64 overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-xl">
                <div class="border-b border-slate-100 px-4 py-3">
                    <p class="text-sm font-bold text-slate-900">
                        MokuMoku Match
                    </p>
                    <p class="mt-1 text-xs text-slate-500">
                        メニュー
                    </p>
                </div>

                <div class="p-2">
                    <a
                        href="{{ route('home') }}"
                        class="block rounded-xl px-4 py-3 text-sm font-bold text-slate-700 hover:bg-slate-100"
                    >
                        ホーム
                    </a>

                    <a
                        href="{{ route('work-posts.index') }}"
                        class="block rounded-xl px-4 py-3 text-sm font-bold text-slate-700 hover:bg-slate-100"
                    >
                        募集一覧
                    </a>

                    @auth
                        <a
                            href="{{ route('mypage') }}"
                            class="block rounded-xl px-4 py-3 text-sm font-bold text-slate-700 hover:bg-slate-100"
                        >
                            マイページ
                        </a>

                        <a
                            href="{{ route('work-posts.create') }}"
                            class="mt-1 block rounded-xl bg-indigo-600 px-4 py-3 text-sm font-bold text-white hover:bg-indigo-700"
                        >
                            募集作成
                        </a>

                        <form method="POST" action="{{ route('logout') }}" class="mt-1">
                            @csrf
                            <button
                                type="submit"
                                class="block w-full rounded-xl px-4 py-3 text-left text-sm font-bold text-slate-700 hover:bg-slate-100"
                            >
                                ログアウト
                            </button>
                        </form>
                    @else
                        <a
                            href="{{ route('login') }}"
                            class="block rounded-xl px-4 py-3 text-sm font-bold text-slate-700 hover:bg-slate-100"
                        >
                            ログイン
                        </a>

                        <a
                            href="{{ route('register') }}"
                            class="mt-1 block rounded-xl bg-indigo-600 px-4 py-3 text-sm font-bold text-white hover:bg-indigo-700"
                        >
                            会員登録
                        </a>
                    @endauth
                </div>
            </div>
        </details>
    </nav>
</header>

<main>
    <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
        @include('components.flash-message')
    </div>

    @yield('content')
</main>
</body>
</html>
