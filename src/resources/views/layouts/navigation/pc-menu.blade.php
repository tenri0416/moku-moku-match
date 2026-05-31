@php
    $unreadMessageCount = $unreadMessageCount ?? 0;
    $unreadNotificationCount = $unreadNotificationCount ?? 0;

    $loginUserName = auth()->check()
        ? (auth()->user()->profile->display_name ?? auth()->user()->name)
        : null;
@endphp

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

    @if (Route::has('articles.index'))
        <a
            href="{{ route('articles.index') }}"
            class="rounded-xl px-3 py-2 text-sm font-bold text-slate-600 transition hover:bg-slate-100 hover:text-slate-900"
        >
            記事
        </a>
    @endif

    @auth

        @if (Route::has('messages.index'))
            <a
                href="{{ route('messages.index') }}"
                class="relative rounded-xl px-3 py-2 text-sm font-bold text-slate-600 transition hover:bg-slate-100 hover:text-slate-900"
            >
                メッセージ

                @if ($unreadMessageCount > 0)
                    <span class="absolute -right-1 -top-1 inline-flex min-h-5 min-w-5 items-center justify-center rounded-full bg-rose-600 px-1.5 text-xs font-bold text-white">
                        {{ $unreadMessageCount > 99 ? '99+' : $unreadMessageCount }}
                    </span>
                @endif
            </a>
        @endif

        @include('layouts.navigation.notification-button')

        {{-- ログアウトの横にログイン中ユーザー名を表示 --}}
        <a
            href="{{ route('mypage') }}"
            class="ml-2 flex items-center gap-2 rounded-xl border border-slate-200 bg-slate-50 px-3 py-2 text-sm font-bold text-slate-700 transition hover:bg-slate-100 hover:text-slate-900"
            title="ログイン中のユーザー"
        >
            <span class="flex h-8 w-8 items-center justify-center rounded-full bg-indigo-600 text-xs font-black text-white">
                {{ mb_substr($loginUserName, 0, 1) }}
            </span>

            <span class="max-w-[140px] truncate">
                {{ $loginUserName }}
            </span>
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
