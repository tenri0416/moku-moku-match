<header class="sticky top-0 z-50 border-b border-slate-200 bg-white/90 backdrop-blur">
    <nav class="mx-auto flex max-w-7xl items-center justify-between px-4 py-3 sm:px-6 lg:px-8">
        {{-- ロゴ --}}
        <a href="{{ route('home') }}" class="flex items-center gap-2">
            <span class="flex h-9 w-9 items-center justify-center rounded-xl bg-indigo-600 text-sm font-black text-white shadow-sm">
                M
            </span>
            <span class="text-lg font-bold tracking-tight text-slate-900">
                MokuMoku Match
            </span>
        </a>

        {{-- PCメニュー --}}
        <div class="hidden items-center gap-2 md:flex">
            <a
                href="{{ route('home') }}"
                class="rounded-lg px-3 py-2 text-sm font-semibold text-slate-600 transition hover:bg-slate-100 hover:text-slate-900"
            >
                ホーム
            </a>

            <a
                href="{{ route('work-posts.index') }}"
                class="rounded-lg px-3 py-2 text-sm font-semibold text-slate-600 transition hover:bg-slate-100 hover:text-slate-900"
            >
                募集一覧
            </a>

            @if (Route::has('articles.index'))
                <a
                    href="{{ route('articles.index') }}"
                    class="rounded-lg px-3 py-2 text-sm font-semibold text-slate-600 transition hover:bg-slate-100 hover:text-slate-900"
                >
                    記事
                </a>
            @endif

            @auth
                @php
                    $unreadNotificationCount = auth()->user()->unreadNotifications()->count();
                    $unreadMessageCount = auth()->user()->receivedMessages()
                        ->whereNull('read_at')
                        ->count();
                @endphp

                <a
                    href="{{ route('messages.index') }}"
                    class="relative rounded-lg px-3 py-2 text-sm font-semibold text-slate-600 transition hover:bg-slate-100 hover:text-slate-900"
                >
                    メッセージ

                    @if ($unreadMessageCount > 0)
                        <span class="absolute -right-1 -top-1 inline-flex min-h-5 min-w-5 items-center justify-center rounded-full bg-rose-600 px-1.5 text-xs font-bold text-white">
                            {{ $unreadMessageCount > 99 ? '99+' : $unreadMessageCount }}
                        </span>
                    @endif
                </a>

                <a
                    href="{{ route('notifications.index') }}"
                    class="relative rounded-lg px-3 py-2 text-sm font-semibold text-slate-600 transition hover:bg-slate-100 hover:text-slate-900"
                    aria-label="通知"
                >
                    <span class="text-lg">🔔</span>

                    @if ($unreadNotificationCount > 0)
                        <span class="absolute -right-1 -top-1 inline-flex min-h-5 min-w-5 items-center justify-center rounded-full bg-rose-600 px-1.5 text-xs font-bold text-white">
                            {{ $unreadNotificationCount > 99 ? '99+' : $unreadNotificationCount }}
                        </span>
                    @endif
                </a>

                <a
                    href="{{ route('mypage') }}"
                    class="rounded-lg px-3 py-2 text-sm font-semibold text-slate-600 transition hover:bg-slate-100 hover:text-slate-900"
                >
                    マイページ
                </a>

                <a
                    href="{{ route('work-posts.create') }}"
                    class="rounded-lg bg-indigo-600 px-4 py-2 text-sm font-bold text-white shadow-sm transition hover:bg-indigo-700"
                >
                    募集作成
                </a>

                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button
                        type="submit"
                        class="rounded-lg px-3 py-2 text-sm font-semibold text-slate-600 transition hover:bg-slate-100 hover:text-slate-900"
                    >
                        ログアウト
                    </button>
                </form>
            @else
                <a
                    href="{{ route('login') }}"
                    class="rounded-lg px-3 py-2 text-sm font-semibold text-slate-600 transition hover:bg-slate-100 hover:text-slate-900"
                >
                    ログイン
                </a>

                <a
                    href="{{ route('register') }}"
                    class="rounded-lg bg-indigo-600 px-4 py-2 text-sm font-bold text-white shadow-sm transition hover:bg-indigo-700"
                >
                    会員登録
                </a>
            @endauth
        </div>

        {{-- スマホメニュー --}}
        <details class="relative md:hidden">
            <summary class="list-none rounded-lg border border-slate-200 px-3 py-2 text-sm font-bold text-slate-700 shadow-sm">
                メニュー
            </summary>

            <div class="absolute right-0 mt-3 w-64 overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-xl">
                <div class="p-2">
                    <a
                        href="{{ route('home') }}"
                        class="block rounded-lg px-4 py-3 text-sm font-semibold text-slate-700 hover:bg-slate-100"
                    >
                        ホーム
                    </a>

                    <a
                        href="{{ route('work-posts.index') }}"
                        class="block rounded-lg px-4 py-3 text-sm font-semibold text-slate-700 hover:bg-slate-100"
                    >
                        募集一覧
                    </a>

                    @if (Route::has('articles.index'))
                        <a
                            href="{{ route('articles.index') }}"
                            class="block rounded-lg px-4 py-3 text-sm font-semibold text-slate-700 hover:bg-slate-100"
                        >
                            記事
                        </a>
                    @endif

                    @auth
                        @php
                            $unreadNotificationCount = auth()->user()->unreadNotifications()->count();
                            $unreadMessageCount = auth()->user()->receivedMessages()
                                ->whereNull('read_at')
                                ->count();
                        @endphp

                        <a
                            href="{{ route('messages.index') }}"
                            class="flex items-center justify-between rounded-lg px-4 py-3 text-sm font-semibold text-slate-700 hover:bg-slate-100"
                        >
                            <span>メッセージ</span>

                            @if ($unreadMessageCount > 0)
                                <span class="inline-flex min-h-5 min-w-5 items-center justify-center rounded-full bg-rose-600 px-1.5 text-xs font-bold text-white">
                                    {{ $unreadMessageCount > 99 ? '99+' : $unreadMessageCount }}
                                </span>
                            @endif
                        </a>

                        <a
                            href="{{ route('notifications.index') }}"
                            class="flex items-center justify-between rounded-lg px-4 py-3 text-sm font-semibold text-slate-700 hover:bg-slate-100"
                        >
                            <span>通知</span>

                            @if ($unreadNotificationCount > 0)
                                <span class="inline-flex min-h-5 min-w-5 items-center justify-center rounded-full bg-rose-600 px-1.5 text-xs font-bold text-white">
                                    {{ $unreadNotificationCount > 99 ? '99+' : $unreadNotificationCount }}
                                </span>
                            @endif
                        </a>

                        <a
                            href="{{ route('mypage') }}"
                            class="block rounded-lg px-4 py-3 text-sm font-semibold text-slate-700 hover:bg-slate-100"
                        >
                            マイページ
                        </a>

                        <a
                            href="{{ route('work-posts.create') }}"
                            class="mt-1 block rounded-lg bg-indigo-600 px-4 py-3 text-sm font-bold text-white hover:bg-indigo-700"
                        >
                            募集作成
                        </a>

                        <form method="POST" action="{{ route('logout') }}" class="mt-1">
                            @csrf
                            <button
                                type="submit"
                                class="block w-full rounded-lg px-4 py-3 text-left text-sm font-semibold text-slate-700 hover:bg-slate-100"
                            >
                                ログアウト
                            </button>
                        </form>
                    @else
                        <a
                            href="{{ route('login') }}"
                            class="block rounded-lg px-4 py-3 text-sm font-semibold text-slate-700 hover:bg-slate-100"
                        >
                            ログイン
                        </a>

                        <a
                            href="{{ route('register') }}"
                            class="mt-1 block rounded-lg bg-indigo-600 px-4 py-3 text-sm font-bold text-white hover:bg-indigo-700"
                        >
                            会員登録
                        </a>
                    @endauth
                </div>
            </div>
        </details>
    </nav>
</header>
