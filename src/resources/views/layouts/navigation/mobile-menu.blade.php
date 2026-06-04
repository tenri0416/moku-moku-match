@php
    $unreadMessageCount = $unreadMessageCount ?? 0;
    $unreadNotificationCount = $unreadNotificationCount ?? 0;

    $loginUser = auth()->user();

    $loginUserName = auth()->check()
        ? ($loginUser?->profile?->display_name ?? $loginUser?->name)
        : null;

    $profile = auth()->check() ? $loginUser?->profile : null;

    $avatarPath = $profile?->avatar_path;
    $avatarUrl = $avatarPath
        ? asset('storage/' . ltrim($avatarPath, '/'))
        : asset('images/default-avatar.png');
@endphp

<details class="relative md:hidden">
    <summary
    class="relative flex h-11 w-11 cursor-pointer list-none items-center justify-center rounded-full text-[32px] text-[#071433] transition hover:bg-[#F4F8FF]"
    aria-label="メニューを開く"
>
    ☰

    @auth
        <span
            data-header-message-count
            class="{{ $unreadMessageCount > 0 ? '' : 'hidden' }} absolute -right-1 -top-1 inline-flex min-h-[20px] min-w-[20px] items-center justify-center rounded-full bg-rose-600 px-1.5 text-[11px] font-black leading-none text-white shadow-[0_4px_10px_rgba(225,29,72,0.35)] ring-2 ring-white"
        >
            {{ $unreadMessageCount > 99 ? '99+' : $unreadMessageCount }}
        </span>
    @endauth
</summary>

    <div class="fixed left-4 right-4 top-[84px] z-[80] max-h-[calc(100vh-110px)] overflow-y-auto rounded-2xl border border-[#DDE6F5] bg-white shadow-[0_18px_48px_rgba(15,43,95,0.22)]">
        <div class="border-b border-[#E5ECF7] px-4 py-4">
            <div class="flex items-center gap-3">
                <div class="flex h-11 w-11 shrink-0 items-center justify-center rounded-2xl bg-indigo-600 text-[18px] font-black text-white shadow-sm">
                    M
                </div>

                <div class="min-w-0">
                    <p class="truncate text-[15px] font-black text-[#071433]">
                        MokuMoku Match
                    </p>
                    <p class="mt-1 text-[12px] font-bold text-[#64748B]">
                        メニュー
                    </p>
                </div>
            </div>
        </div>

        <div class="p-2">
            @auth
                <a
                    href="{{ route('mypage') }}"
                    class="mb-2 flex items-center gap-3 rounded-xl bg-blue-50 px-4 py-3"
                >
                    <img
                        src="{{ $avatarUrl }}"
                        alt="{{ $loginUserName }}"
                        class="h-11 w-11 shrink-0 rounded-full object-cover"
                    >

                    <span class="min-w-0">
                        <span class="block text-[12px] font-bold text-[#0D4FE8]">
                            ログイン中
                        </span>
                        <span class="block truncate text-[14px] font-black text-[#071433]">
                            {{ $loginUserName }}
                        </span>
                    </span>
                </a>
            @endauth

            @auth
                @if (Route::has('messages.index'))
                    <a
                        href="{{ route('messages.index') }}"
                        class="flex items-center justify-between rounded-xl px-4 py-3 text-[15px] font-black text-[#071433] hover:bg-[#F8FAFF]"
                    >
                        <span>メッセージ</span>

                        <span
                            data-header-message-count
                            class="{{ $unreadMessageCount > 0 ? '' : 'hidden' }} inline-flex min-h-5 min-w-5 items-center justify-center rounded-full bg-rose-600 px-1.5 text-xs font-bold text-white"
                        >
                            {{ $unreadMessageCount > 99 ? '99+' : $unreadMessageCount }}
                        </span>
                    </a>
                @endif

                @if (Route::has('notifications.index'))
                    <button
                        type="button"
                        data-open-notification-modal
                        class="flex w-full items-center justify-between rounded-xl px-4 py-3 text-left text-[15px] font-black text-[#071433] hover:bg-[#F8FAFF]"
                    >
                        <span>通知</span>

                        <span
                            data-header-notification-count
                            class="{{ $unreadNotificationCount > 0 ? '' : 'hidden' }} inline-flex min-h-5 min-w-5 items-center justify-center rounded-full bg-rose-600 px-1.5 text-xs font-bold text-white"
                        >
                            {{ $unreadNotificationCount > 99 ? '99+' : $unreadNotificationCount }}
                        </span>
                    </button>
                @endif

                <form method="POST" action="{{ route('logout') }}" class="mt-1">
                    @csrf

                    <button
                        type="submit"
                        class="block w-full rounded-xl px-4 py-3 text-left text-[15px] font-black text-[#071433] hover:bg-[#F8FAFF]"
                    >
                        ログアウト
                    </button>
                </form>
            @else
                <a
                    href="{{ route('login') }}"
                    class="block rounded-xl px-4 py-3 text-[15px] font-black text-[#071433] hover:bg-[#F8FAFF]"
                >
                    ログイン
                </a>

                <a
                    href="{{ route('register') }}"
                    class="mt-1 block rounded-xl bg-[#0D4FE8] px-4 py-3 text-[15px] font-black text-white"
                >
                    会員登録
                </a>
            @endauth
        </div>
    </div>
</details>
