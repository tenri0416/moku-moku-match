<!DOCTYPE html>
<html lang="ja">
@include('layouts.head')

<body
    class="min-h-screen bg-slate-50 text-slate-900 antialiased"
    @auth
        @if (Route::has('header.status'))
            data-header-realtime
            data-latest-url="{{ route('header.status') }}"
        @endif
    @endauth
>
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

            // 通知タブ：メッセージ通知以外
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

    @auth
        {{-- ログイン中はアプリ側ヘッダー --}}
        @include('layouts.header')
    @else
        {{-- 未ログイン時は記事用ヘッダー --}}
        <header class="sticky top-0 z-50 border-b border-[#E6DAC8] bg-[#FDFBF7]/95 backdrop-blur">
            <div class="mx-auto flex max-w-7xl items-center justify-between px-4 py-4 sm:px-6 lg:px-8">
                <a href="{{ route('articles.index') }}" class="flex items-center gap-3">
                    <div class="flex h-11 w-11 items-center justify-center rounded-full bg-[#0B1548] text-sm font-black text-white">
                        M
                    </div>

                    <div>
                        <p class="text-[15px] font-black leading-none text-[#0B1548]">
                            MokuMoku Match
                        </p>
                        <p class="mt-1 text-[11px] font-bold tracking-[0.18em] text-[#C9825D]">
                            MAGAZINE
                        </p>
                    </div>
                </a>

                {{-- PC用ナビ --}}
                <nav class="hidden items-center gap-8 text-sm font-black text-[#1F2933] lg:flex">
                    <a href="{{ route('articles.index') }}" class="relative py-3 transition hover:text-[#C9825D]">
                        記事
                        <span class="absolute -bottom-5 left-0 h-1 w-full bg-[#C9825D]"></span>
                    </a>

                    <a href="{{ route('home') }}" class="py-3 transition hover:text-[#C9825D]">
                        サービス
                    </a>

                    @if (Route::has('work-posts.index'))
                        <a href="{{ route('work-posts.index') }}" class="py-3 transition hover:text-[#C9825D]">
                            作業仲間を探す
                        </a>
                    @endif
                </nav>

                {{-- PC/SP共通 右側 --}}
                <div class="flex items-center gap-3">
                    <a
                        href="{{ route('articles.index') }}"
                        class="hidden rounded-full border border-[#D8CCB8] px-5 py-3 text-sm font-black text-[#0B1548] transition hover:border-[#C9825D] hover:text-[#C9825D] sm:inline-flex"
                    >
                        記事一覧
                    </a>

                    <a
                        href="{{ route('home') }}"
                        class="rounded-full bg-[#0B1548] px-5 py-3 text-sm font-black text-white shadow-[0_12px_30px_rgba(11,21,72,0.18)] transition hover:-translate-y-0.5 hover:bg-[#17215A]"
                    >
                        サービスへ
                    </a>
                </div>
            </div>
        </header>
    @endauth

    <main class="@auth pb-[96px] md:pb-0 @endauth">
        @auth
            <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
                @include('components.flash-message')
            </div>
        @endauth

        @yield('content')
    </main>

    @auth
        @include('components.mobile-footer-nav')
        @include('layouts.notifications.modal')
        @include('layouts.notifications.script')
        @include('components.ai-loading-modal')
    @endauth

    @stack('scripts')
</body>
</html>
