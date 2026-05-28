<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', '管理画面') - MokuMoku Match</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body class="min-h-screen bg-slate-50 text-slate-900 antialiased">
<div class="min-h-screen lg:flex">
    {{-- PC Sidebar --}}
    <aside class="hidden w-72 shrink-0 border-r border-slate-200 bg-white lg:block">
        <div class="sticky top-0 flex h-screen flex-col">
            {{-- Logo --}}
            <div class="border-b border-slate-200 px-6 py-5">
                <a href="{{ route('admin.dashboard') }}" class="flex items-center gap-3">
                    <span class="flex h-11 w-11 items-center justify-center rounded-2xl bg-indigo-600 text-base font-black text-white shadow-sm">
                        A
                    </span>

                    <span>
                        <span class="block text-lg font-black tracking-tight text-slate-900">
                            Admin
                        </span>
                        <span class="block text-xs font-semibold text-slate-500">
                            MokuMoku Match 管理画面
                        </span>
                    </span>
                </a>
            </div>

            {{-- Navigation --}}
            <nav class="flex-1 space-y-1 px-4 py-5">
                <a
                    href="{{ route('admin.dashboard') }}"
                    class="flex items-center justify-between rounded-xl px-4 py-3 text-sm font-bold transition
                        {{ request()->routeIs('admin.dashboard') ? 'bg-indigo-50 text-indigo-700' : 'text-slate-700 hover:bg-slate-100' }}"
                >
                    <span>ダッシュボード</span>
                    <span class="text-slate-400">›</span>
                </a>

                <a
                    href="{{ route('admin.users.index') }}"
                    class="flex items-center justify-between rounded-xl px-4 py-3 text-sm font-bold transition
                        {{ request()->routeIs('admin.users.*') ? 'bg-indigo-50 text-indigo-700' : 'text-slate-700 hover:bg-slate-100' }}"
                >
                    <span>ユーザー管理</span>
                    <span class="text-slate-400">›</span>
                </a>

                <a
                    href="{{ route('admin.work-posts.index') }}"
                    class="flex items-center justify-between rounded-xl px-4 py-3 text-sm font-bold transition
                        {{ request()->routeIs('admin.work-posts.*') ? 'bg-indigo-50 text-indigo-700' : 'text-slate-700 hover:bg-slate-100' }}"
                >
                    <span>募集管理</span>
                    <span class="text-slate-400">›</span>
                </a>

                <a
                    href="{{ route('admin.reports.index') }}"
                    class="flex items-center justify-between rounded-xl px-4 py-3 text-sm font-bold transition
                        {{ request()->routeIs('admin.reports.*') ? 'bg-rose-50 text-rose-700' : 'text-slate-700 hover:bg-slate-100' }}"
                >
                    <span>通報管理</span>
                    <span class="text-slate-400">›</span>
                </a>
                <a
                    href="{{ route('admin.database.index') }}"
                     class="flex items-center justify-between rounded-xl px-4 py-3 text-sm font-bold transition
                        {{ request()->routeIs('admin.database.*') ? 'bg-indigo-50 text-indigo-700' : 'text-slate-700 hover:bg-slate-100' }}"
                    >
                    <span>DBテーブルを見る</span>
                    <span class="text-slate-400">›</span>
                </a>
            </nav>

            {{-- Bottom --}}
            <div class="border-t border-slate-200 p-4">
                <a
                    href="{{ route('home') }}"
                    class="flex w-full items-center justify-center rounded-xl border border-slate-300 bg-white px-4 py-3 text-sm font-bold text-slate-700 transition hover:bg-slate-50"
                >
                    サイトへ戻る
                </a>

                <form method="POST" action="{{ route('admin.logout') }}" class="mt-3">
                    @csrf

                    <button
                        type="submit"
                        class="flex w-full items-center justify-center rounded-xl bg-slate-900 px-4 py-3 text-sm font-bold text-white transition hover:bg-slate-800"
                    >
                        ログアウト
                    </button>
                </form>
            </div>
        </div>
    </aside>

    {{-- Main --}}
    <div class="min-w-0 flex-1">
        {{-- Mobile Header --}}
        <header class="sticky top-0 z-50 border-b border-slate-200 bg-white/90 backdrop-blur lg:hidden">
            <div class="flex items-center justify-between px-4 py-3">
                <a href="{{ route('admin.dashboard') }}" class="flex items-center gap-3">
                    <span class="flex h-10 w-10 items-center justify-center rounded-2xl bg-indigo-600 text-sm font-black text-white shadow-sm">
                        A
                    </span>

                    <span>
                        <span class="block text-base font-black text-slate-900">
                            Admin
                        </span>
                        <span class="block text-xs font-semibold text-slate-500">
                            管理画面
                        </span>
                    </span>
                </a>

                <details class="relative">
                    <summary class="cursor-pointer list-none rounded-xl border border-slate-200 bg-white px-4 py-2 text-sm font-bold text-slate-700 shadow-sm transition hover:bg-slate-50">
                        メニュー
                    </summary>

                    <div class="absolute right-0 mt-3 w-64 overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-xl">
                        <div class="border-b border-slate-100 px-4 py-3">
                            <p class="text-sm font-bold text-slate-900">
                                管理メニュー
                            </p>
                            <p class="mt-1 text-xs text-slate-500">
                                MokuMoku Match
                            </p>
                        </div>

                        <div class="p-2">
                            <a
                                href="{{ route('admin.dashboard') }}"
                                class="block rounded-xl px-4 py-3 text-sm font-bold
                                    {{ request()->routeIs('admin.dashboard') ? 'bg-indigo-50 text-indigo-700' : 'text-slate-700 hover:bg-slate-100' }}"
                            >
                                ダッシュボード
                            </a>

                            <a
                                href="{{ route('admin.users.index') }}"
                                class="block rounded-xl px-4 py-3 text-sm font-bold
                                    {{ request()->routeIs('admin.users.*') ? 'bg-indigo-50 text-indigo-700' : 'text-slate-700 hover:bg-slate-100' }}"
                            >
                                ユーザー管理
                            </a>

                            <a
                                href="{{ route('admin.work-posts.index') }}"
                                class="block rounded-xl px-4 py-3 text-sm font-bold
                                    {{ request()->routeIs('admin.work-posts.*') ? 'bg-indigo-50 text-indigo-700' : 'text-slate-700 hover:bg-slate-100' }}"
                            >
                                募集管理
                            </a>

                            <a
                                href="{{ route('admin.reports.index') }}"
                                class="block rounded-xl px-4 py-3 text-sm font-bold
                                    {{ request()->routeIs('admin.reports.*') ? 'bg-rose-50 text-rose-700' : 'text-slate-700 hover:bg-slate-100' }}"
                            >
                                通報管理
                            </a>

                            <div class="my-2 border-t border-slate-100"></div>

                            <a
                                href="{{ route('home') }}"
                                class="block rounded-xl px-4 py-3 text-sm font-bold text-slate-700 hover:bg-slate-100"
                            >
                                サイトへ戻る
                            </a>

                            <form method="POST" action="{{ route('admin.logout') }}">
                                @csrf

                                <button
                                    type="submit"
                                    class="block w-full rounded-xl px-4 py-3 text-left text-sm font-bold text-slate-700 hover:bg-slate-100"
                                >
                                    ログアウト
                                </button>
                            </form>
                        </div>
                    </div>
                </details>
            </div>
        </header>

        {{-- Desktop Top Header --}}
        <header class="hidden border-b border-slate-200 bg-white lg:block">
            <div class="mx-auto flex max-w-7xl items-center justify-between px-8 py-4">
                <div>
                    <p class="text-sm font-bold text-slate-500">
                        管理画面
                    </p>
                    <p class="mt-1 text-lg font-black text-slate-900">
                        @yield('title', '管理画面')
                    </p>
                </div>

                <div class="flex items-center gap-3">
                    <a
                        href="{{ route('home') }}"
                        class="rounded-xl border border-slate-300 bg-white px-4 py-2 text-sm font-bold text-slate-700 transition hover:bg-slate-50"
                    >
                        サイトへ戻る
                    </a>

                    <form method="POST" action="{{ route('admin.logout') }}">
                        @csrf

                        <button
                            type="submit"
                            class="rounded-xl bg-slate-900 px-4 py-2 text-sm font-bold text-white transition hover:bg-slate-800"
                        >
                            ログアウト
                        </button>
                    </form>
                </div>
            </div>
        </header>

        {{-- Content --}}
        <main>
            <div class="mx-auto max-w-7xl px-4 pt-6 sm:px-6 lg:px-8">
                @include('components.flash-message')
            </div>

            @yield('content')
        </main>
    </div>
</div>
</body>
</html>
