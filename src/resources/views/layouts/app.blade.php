<!DOCTYPE html>
<html lang="ja">
@include('layouts.head')

<body class="min-h-screen bg-slate-50 text-slate-900 antialiased">
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
            return ($notification->data['type'] ?? 'general') !== 'article';
        });

        // お知らせタブ：記事通知
        $headerArticleNotifications = $headerNotifications->filter(function ($notification) {
            return ($notification->data['type'] ?? 'general') === 'article';
        });

        $unreadNotificationCount = auth()->user()
            ->unreadNotifications()
            ->count();

        $unreadMessageCount = method_exists(auth()->user(), 'receivedMessages')
            ? auth()->user()->receivedMessages()->whereNull('read_at')->count()
            : 0;
    }
@endphp

@include('layouts.header')

<main>
    <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
        @include('components.flash-message')
    </div>

    @yield('content')
</main>
@include('components.mobile-footer-nav')
@include('layouts.notifications.modal')
@include('layouts.notifications.script')
</body>
</html>
