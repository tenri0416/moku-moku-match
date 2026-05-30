@if (Route::has('notifications.index'))
    <button
        type="button"
        data-open-notification-modal
        class="relative ml-2 inline-flex flex-col items-center justify-center border-l border-slate-200 pl-4 pr-2 text-slate-700 transition hover:text-slate-900"
        aria-label="通知を開く"
    >
        <span class="relative inline-flex h-9 w-9 items-center justify-center rounded-xl transition hover:bg-slate-100">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-7 w-7" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                <path stroke-linecap="round" stroke-linejoin="round" d="M14.857 17H18a2 2 0 0 0 2-2v-1.382a1 1 0 0 0-.553-.894l-.79-.395A2 2 0 0 1 17.5 10.54V9a5.5 5.5 0 1 0-11 0v1.54a2 2 0 0 1-1.157 1.789l-.79.395A1 1 0 0 0 4 13.618V15a2 2 0 0 0 2 2h3.143m5.714 0a3 3 0 0 1-5.714 0m5.714 0H9.143" />
            </svg>

            @if ($unreadNotificationCount > 0)
                <span class="absolute -right-1 -top-1 inline-flex min-h-5 min-w-5 items-center justify-center rounded-full bg-rose-600 px-1.5 text-xs font-bold text-white">
                    {{ $unreadNotificationCount > 99 ? '99+' : $unreadNotificationCount }}
                </span>
            @endif
        </span>

        <span class="text-xs font-bold text-slate-700">
            通知
        </span>
    </button>
@endif
