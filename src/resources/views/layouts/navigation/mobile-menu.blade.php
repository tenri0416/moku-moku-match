@php
    $unreadMessageCount = $unreadMessageCount ?? 0;
    $unreadNotificationCount = $unreadNotificationCount ?? 0;

    $loginUserName = auth()->check()
        ? (auth()->user()->profile->display_name ?? auth()->user()->name)
        : null;
@endphp

<details class="relative md:hidden">
    <summary class="cursor-pointer list-none rounded-xl border border-slate-200 bg-white px-4 py-2 text-sm font-bold text-slate-700 shadow-sm transition hover:bg-slate-50">
        メニュー
    </summary>

    <div class="absolute right-0 mt-3 w-72 overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-xl">
        <div class="border-b border-slate-100 px-4 py-3">
            <p class="text-sm font-bold text-slate-900">
                MokuMoku Match
            </p>
            <p class="mt-1 text-xs text-slate-500">
                メニュー
            </p>
        </div>

        <div class="p-2">
            @auth
                {{-- ログイン中ユーザー表示 --}}
                <a
                    href="{{ route('mypage') }}"
                    class="mb-2 flex items-center gap-3 rounded-xl bg-indigo-50 px-4 py-3 text-sm font-bold text-indigo-900"
                >
                    <span class="flex h-10 w-10 items-center justify-center rounded-full bg-indigo-600 text-sm font-black text-white">
                        {{ mb_substr($loginUserName, 0, 1) }}
                    </span>

                    <span class="min-w-0">
                        <span class="block text-xs text-indigo-500">
                            ログイン中
                        </span>
                        <span class="block truncate">
                            {{ $loginUserName }}
                        </span>
                    </span>
                </a>
            @endauth

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

            @if (Route::has('articles.index'))
                <a
                    href="{{ route('articles.index') }}"
                    class="block rounded-xl px-4 py-3 text-sm font-bold text-slate-700 hover:bg-slate-100"
                >
                    記事
                </a>
            @endif

            @auth

                @if (Route::has('messages.index'))
                    <a
                        href="{{ route('messages.index') }}"
                        class="flex items-center justify-between rounded-xl px-4 py-3 text-sm font-bold text-slate-700 hover:bg-slate-100"
                    >
                        <span>メッセージ</span>

                        @if ($unreadMessageCount > 0)
                            <span class="inline-flex min-h-5 min-w-5 items-center justify-center rounded-full bg-rose-600 px-1.5 text-xs font-bold text-white">
                                {{ $unreadMessageCount > 99 ? '99+' : $unreadMessageCount }}
                            </span>
                        @endif
                    </a>
                @endif

                @if (Route::has('notifications.index'))
                    <button
                        type="button"
                        data-open-notification-modal
                        class="flex w-full items-center justify-between rounded-xl px-4 py-3 text-left text-sm font-bold text-slate-700 hover:bg-slate-100"
                    >
                        <span>通知</span>

                        @if ($unreadNotificationCount > 0)
                            <span class="inline-flex min-h-5 min-w-5 items-center justify-center rounded-full bg-rose-600 px-1.5 text-xs font-bold text-white">
                                {{ $unreadNotificationCount > 99 ? '99+' : $unreadNotificationCount }}
                            </span>
                        @endif
                    </button>
                @endif

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
