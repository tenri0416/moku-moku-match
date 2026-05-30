@auth
    @if (Route::has('notifications.index'))
        <div id="notificationModal" class="fixed inset-0 z-[100] hidden">
            <div
                data-close-notification-modal
                class="absolute inset-0 bg-slate-900/20"
            ></div>

            <div class="relative mx-auto mt-16 w-[calc(100%-2rem)] max-w-[680px] overflow-hidden rounded-3xl bg-white shadow-2xl ring-1 ring-slate-200">
                <button
                    type="button"
                    data-close-notification-modal
                    class="absolute right-4 top-4 z-10 inline-flex h-9 w-9 items-center justify-center rounded-full text-slate-400 transition hover:bg-slate-100 hover:text-slate-700"
                    aria-label="閉じる"
                >
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M6 18 18 6M6 6l12 12" />
                    </svg>
                </button>

                <div class="grid grid-cols-2 border-b border-slate-200">
                    <button
                        type="button"
                        data-notification-tab-button="general"
                        class="notification-tab-button border-b-4 border-indigo-700 px-4 py-5 text-center text-xl font-black text-slate-900 transition"
                    >
                        通知
                    </button>

                    <button
                        type="button"
                        data-notification-tab-button="article"
                        class="notification-tab-button border-b-4 border-transparent px-4 py-5 text-center text-xl font-black text-slate-400 transition"
                    >
                        お知らせ
                    </button>
                </div>

                @include('layouts.notifications.tab-general')
                @include('layouts.notifications.tab-article')
            </div>
        </div>
    @endif
@endauth
