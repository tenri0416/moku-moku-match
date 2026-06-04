<!DOCTYPE html>
<html lang="ja">
@include('layouts.head')

<body class="min-h-screen bg-slate-50 text-slate-900 antialiased" @auth @if (Route::has('header.status'))
    data-header-realtime data-latest-url="{{ route('header.status') }}" @endif @endauth>
    @php
    $headerNotifications = collect();
    $headerGeneralNotifications = collect();
    $headerArticleNotifications = collect();
    $unreadNotificationCount = 0;
    $unreadMessageCount = 0;

    if (auth()->check()) {
    $headerNotifications = auth()->user()
    ->notifications()
    ->latest()
    ->take(20)
    ->get();

    // 通知タブ：メッセージ通知など
    $headerGeneralNotifications = $headerNotifications->filter(function ($notification) {
    $type = $notification->data['type'] ?? 'general';

    return $type !== 'article'
    && $type !== 'message';
    });

    // お知らせタブ：記事通知
    $headerArticleNotifications = $headerNotifications->filter(function ($notification) {
    return ($notification->data['type'] ?? 'general') === 'article';
    });

    $unreadNotificationCount = auth()->user()
    ->unreadNotifications()
    ->where(function ($query) {
    $query->whereNull('data->type')
    ->orWhere('data->type', '!=', 'message');
    })
    ->count();

    $unreadMessageCount = method_exists(auth()->user(), 'receivedMessages')
    ? auth()->user()->receivedMessages()->whereNull('read_at')->count()
    : 0;
    }
    @endphp

    @include('layouts.header')

    <main class="pb-[96px] md:pb-0">
        <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
            @include('components.flash-message')
        </div>

        @yield('content')
    </main>


    @include('components.mobile-footer-nav')
    @include('layouts.notifications.modal')
    @include('layouts.notifications.script')
    @include('components.ai-loading-modal')
</body>

</html>
