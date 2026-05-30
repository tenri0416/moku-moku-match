@php
    $headerArticleNotifications = $headerArticleNotifications ?? collect();
@endphp

<div
    id="notificationTab-article"
    data-notification-tab-panel="article"
    class="hidden h-[420px] overflow-y-auto px-5 py-5"
>
    @forelse ($headerArticleNotifications as $notification)
        <a
            href="{{ route('notifications.show', $notification->id) }}"
            class="mb-3 block rounded-2xl border px-4 py-4 transition hover:bg-slate-50
                {{ is_null($notification->read_at) ? 'border-indigo-200 bg-indigo-50/60' : 'border-slate-200 bg-white' }}"
        >
            <div class="flex items-start justify-between gap-4">
                <div class="min-w-0">
                    <div class="flex items-center gap-2">
                        <p class="text-sm font-black text-slate-900">
                            {{ $notification->data['title'] ?? 'お知らせ' }}
                        </p>

                        @if (is_null($notification->read_at))
                            <span class="inline-flex rounded-full bg-rose-600 px-2 py-0.5 text-[10px] font-bold text-white">
                                NEW
                            </span>
                        @endif
                    </div>

                    <p class="mt-2 text-sm leading-6 text-slate-600">
                        {{ $notification->data['body'] ?? '' }}
                    </p>
                </div>

                <p class="shrink-0 text-xs font-semibold text-slate-400">
                    {{ $notification->created_at->format('Y/m/d H:i') }}
                </p>
            </div>
        </a>
    @empty
        <div class="flex h-full items-center justify-center">
            <p class="text-xl font-medium text-slate-600">
                お知らせはありません
            </p>
        </div>
    @endforelse
</div>
