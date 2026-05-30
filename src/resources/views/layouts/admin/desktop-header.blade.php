@php
    $adminUnreadNotificationCount = auth('admin')->check()
        ? auth('admin')->user()->unreadNotifications()->count()
        : 0;
@endphp

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
            <button
                type="button"
                data-open-admin-notification-modal
                class="relative inline-flex h-10 w-10 items-center justify-center rounded-xl border border-slate-200 bg-white text-slate-700 transition hover:bg-slate-50 hover:text-slate-900"
                aria-label="管理者通知"
            >
                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.9">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M14.857 17H18a2 2 0 0 0 2-2v-1.382a1 1 0 0 0-.553-.894l-.79-.395A2 2 0 0 1 17.5 10.54V9a5.5 5.5 0 1 0-11 0v1.54a2 2 0 0 1-1.157 1.789l-.79.395A1 1 0 0 0 4 13.618V15a2 2 0 0 0 2 2h3.143m5.714 0a3 3 0 0 1-5.714 0m5.714 0H9.143" />
                </svg>

                <span
                    data-admin-notification-badge
                    class="{{ $adminUnreadNotificationCount > 0 ? '' : 'hidden' }} absolute -right-1 -top-1 inline-flex min-h-5 min-w-5 items-center justify-center rounded-full bg-rose-600 px-1.5 text-xs font-bold text-white"
                >
                    {{ $adminUnreadNotificationCount > 99 ? '99+' : $adminUnreadNotificationCount }}
                </span>
            </button>

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
