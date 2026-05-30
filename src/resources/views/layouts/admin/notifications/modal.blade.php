@php
    $adminUnreadNotifications = collect();

    if (auth('admin')->check()) {
        $adminUnreadNotifications = auth('admin')
            ->user()
            ->unreadNotifications()
            ->latest()
            ->take(20)
            ->get();
    }
@endphp

@if (auth('admin')->check())
    <div id="adminNotificationModal" class="fixed inset-0 z-[100] hidden">
        <div
            data-close-admin-notification-modal
            class="absolute inset-0 bg-slate-900/20"
        ></div>

        <div class="relative mx-auto mt-20 w-[calc(100%-2rem)] max-w-[560px] overflow-hidden rounded-3xl bg-white shadow-2xl ring-1 ring-slate-200">
            <button
                type="button"
                data-close-admin-notification-modal
                class="absolute right-4 top-4 z-10 inline-flex h-9 w-9 items-center justify-center rounded-full text-slate-400 transition hover:bg-slate-100 hover:text-slate-700"
                aria-label="閉じる"
            >
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M6 18 18 6M6 6l12 12" />
                </svg>
            </button>

            <div class="border-b border-slate-200 px-6 py-5">
                <p class="text-sm font-bold text-indigo-600">
                    ADMIN NOTIFICATION
                </p>

                <h2 class="mt-1 text-xl font-black text-slate-900">
                    管理者通知
                </h2>
            </div>

            <div id="adminNotificationModalBody" class="h-[360px] overflow-y-auto px-5 py-5">
                @forelse ($adminUnreadNotifications as $notification)
                    <button
                        type="button"
                        data-admin-notification-item
                        class="mb-3 block w-full rounded-2xl border border-indigo-200 bg-indigo-50/60 px-4 py-4 text-left transition hover:bg-indigo-50"
                    >
                        <div class="flex items-start justify-between gap-4">
                            <div class="min-w-0">
                                <div class="flex items-center gap-2">
                                    <p class="text-sm font-black text-slate-900">
                                        {{ $notification->data['title'] ?? '管理者通知' }}
                                    </p>

                                    <span class="inline-flex rounded-full bg-rose-600 px-2 py-0.5 text-[10px] font-bold text-white">
                                        NEW
                                    </span>
                                </div>

                                <p class="mt-2 text-sm leading-6 text-slate-600">
                                    {{ $notification->data['body'] ?? '' }}
                                </p>
                            </div>

                            <p class="shrink-0 text-xs font-semibold text-slate-400">
                                {{ $notification->created_at->format('Y/m/d H:i') }}
                            </p>
                        </div>
                    </button>
                @empty
                    <div class="flex h-full items-center justify-center">
                        <p class="text-lg font-bold text-slate-500">
                            未読通知はありません
                        </p>
                    </div>
                @endforelse
            </div>
        </div>
    </div>
@endif
