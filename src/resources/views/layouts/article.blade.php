<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <title>@yield('title', 'MokuMokuMatch | 一緒に作業できる仲間を探せるマッチングサービス')</title>

    <meta name="description" content="@yield('description', 'MokuMokuMatchは、フリーランスやリモートワーカーが一緒に黙々作業できる仲間を探せるマッチングサービスです。オンライン・オフラインで作業仲間を募集できます。')">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <meta property="og:site_name" content="MokuMokuMatch">
    <meta property="og:type" content="@yield('og_type', 'website')">
    <meta property="og:title" content="@yield('og_title', trim($__env->yieldContent('title', 'MokuMokuMatch')))">
    <meta property="og:description" content="@yield('og_description', trim($__env->yieldContent('description', '一緒に黙々作業できる仲間を探せるマッチングサービスです。')))">
    <meta property="og:url" content="{{ url()->current() }}">
    <meta property="og:image" content="{{ asset('images/ogp.png') }}">

    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:title" content="@yield('og_title', trim($__env->yieldContent('title', 'MokuMokuMatch')))">
    <meta name="twitter:description" content="@yield('og_description', trim($__env->yieldContent('description', '一緒に黙々作業できる仲間を探せるマッチングサービスです。')))">
    <meta name="twitter:image" content="{{ asset('images/ogp.png') }}">

    <meta name="google-site-verification" content="yDJmA1X0ZuNmPo5_GDEOTF1UZDA5K1MHTx9W84-AMqc" />

    @if (config('services.ga4.measurement_id') && app()->environment('production'))
        <!-- Google tag (gtag.js) -->
        <script async src="https://www.googletagmanager.com/gtag/js?id={{ config('services.ga4.measurement_id') }}"></script>
        <script>
            window.dataLayer = window.dataLayer || [];

            function gtag() {
                dataLayer.push(arguments);
            }

            gtag('js', new Date());
            gtag('config', '{{ config('services.ga4.measurement_id') }}');
        </script>
    @endif

    <link rel="icon" href="{{ asset('favicon.ico') }}">
    <link rel="icon" type="image/png" sizes="32x32" href="{{ asset('favicon-32x32.png') }}">
    <link rel="icon" type="image/png" sizes="16x16" href="{{ asset('favicon-16x16.png') }}">
    <link rel="apple-touch-icon" sizes="180x180" href="{{ asset('apple-touch-icon.png') }}">
    <link rel="manifest" href="{{ asset('site.webmanifest') }}">
    <meta name="theme-color" content="#0B1548">

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body class="min-h-screen bg-[#F7F3EA] text-[#1F2933] antialiased">
    <div class="border-t-4 border-[#C9825D] bg-white">
        <header class="border-b border-[#E8E0D2] bg-white">
            <div class="mx-auto flex max-w-7xl items-center justify-between px-4 py-5 sm:px-6 lg:px-8">
                <a href="{{ route('articles.index') }}" class="group flex items-center gap-4">
                    <div class="relative flex h-14 w-14 items-center justify-center rounded-full bg-[#0B1548] text-xl font-black text-white">
                        M
                        <span class="absolute -right-1 -top-1 h-4 w-4 rounded-full border-2 border-white bg-[#C9825D]"></span>
                    </div>

                    <div class="leading-none">
                        <p class="text-2xl font-black tracking-[0.08em] text-[#111827] sm:text-3xl">
                            MokuMokuMatch
                        </p>
                        <p class="mt-2 text-[11px] font-bold tracking-[0.28em] text-[#6F8FAF]">
                            REMOTE WORK MAGAZINE
                        </p>
                    </div>
                </a>

                <nav class="hidden items-center gap-8 text-sm font-black text-[#1F2933] lg:flex">
                    <a href="{{ route('articles.index') }}" class="relative py-3 transition hover:text-[#C9825D]">
                        記事
                        <span class="absolute -bottom-5 left-0 h-1 w-full bg-[#C9825D]"></span>
                    </a>

                    <a href="{{ route('home') }}" class="py-3 transition hover:text-[#C9825D]">
                        サービス
                    </a>

                    <a href="{{ route('home') }}" class="py-3 transition hover:text-[#C9825D]">
                        作業仲間を探す
                    </a>
                </nav>

                <div class="flex items-center gap-3">
                    <a href="{{ route('articles.index') }}"
                       class="hidden rounded-full border border-[#D8CCB8] px-5 py-3 text-sm font-black text-[#0B1548] transition hover:border-[#C9825D] hover:text-[#C9825D] sm:inline-flex">
                        記事一覧
                    </a>

                    <a href="{{ route('home') }}"
                       class="rounded-full bg-[#0B1548] px-5 py-3 text-sm font-black text-white shadow-[0_12px_30px_rgba(11,21,72,0.18)] transition hover:-translate-y-0.5 hover:bg-[#17215A]">
                        サービスへ
                    </a>

                    <div class="hidden h-12 w-12 items-center justify-center rounded-full bg-[#2E343B] text-white md:flex">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="m21 21-4.35-4.35m1.1-5.4a6.5 6.5 0 1 1-13 0 6.5 6.5 0 0 1 13 0Z" />
                        </svg>
                    </div>
                </div>
            </div>
        </header>

        <section class="border-b border-[#E8E0D2] bg-[#F3EFE6]">
            <div class="mx-auto max-w-7xl px-4 py-5 sm:px-6 lg:px-8">
                <div class="relative overflow-hidden border-2 border-[#D9A441] bg-white">
                    <div class="grid items-center gap-4 px-5 py-4 md:grid-cols-[1fr_220px] lg:grid-cols-[1fr_280px]">
                        <div>
                            <div class="mb-2 inline-flex items-center gap-2 bg-[#0B1548] px-3 py-1 text-xs font-black text-white">
                                特集
                            </div>

                            <p class="text-lg font-black leading-8 text-[#111827] sm:text-2xl">
                                一人で働く毎日を変える。<br class="sm:hidden">
                                フルリモート時代の作業仲間の見つけ方
                            </p>

                            <p class="mt-2 text-sm font-bold leading-7 text-[#5B6472]">
                                フリーランス・リモートワーカーの集中、継続、つながりをテーマにした記事を発信中。
                            </p>
                        </div>

                        <div class="hidden md:block">
                            <div class="relative h-28 overflow-hidden bg-[#0B1548]">
                                <img src="{{ asset('images/ogp.png') }}"
                                     alt="MokuMokuMatch"
                                     class="h-full w-full object-cover opacity-90">
                                <div class="absolute inset-0 bg-[#0B1548]/10"></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <section class="bg-white">
            <div class="mx-auto flex max-w-7xl flex-wrap items-center gap-2 px-4 py-4 sm:px-6 lg:px-8">
                <span class="mr-2 text-xs font-black tracking-[0.18em] text-[#C9825D]">
                    THEME
                </span>

                <span class="rounded-full bg-[#EEF3F7] px-3 py-1 text-xs font-bold text-[#34506A]">
                    フルリモート
                </span>

                <span class="rounded-full bg-[#EEF3F7] px-3 py-1 text-xs font-bold text-[#34506A]">
                    フリーランス
                </span>

                <span class="rounded-full bg-[#EEF3F7] px-3 py-1 text-xs font-bold text-[#34506A]">
                    作業仲間
                </span>

                <span class="rounded-full bg-[#EEF3F7] px-3 py-1 text-xs font-bold text-[#34506A]">
                    コワーキング
                </span>

                <span class="rounded-full bg-[#EEF3F7] px-3 py-1 text-xs font-bold text-[#34506A]">
                    集中環境
                </span>
            </div>
        </section>
    </div>

    <main class="mx-auto w-full max-w-7xl px-4 py-8 sm:px-6 lg:px-8">
        <div class="grid gap-8 lg:grid-cols-[minmax(0,1fr)_320px]">
            <div class="min-w-0 bg-white">
                <div class="border-t-4 border-[#0B1548]">
                    @yield('content')
                </div>
            </div>

            <aside class="hidden lg:block">
                <div class="sticky top-6 space-y-6">
                    <section class="border-t-4 border-[#C9825D] bg-white p-5 shadow-sm">
                        <h2 class="text-lg font-black text-[#111827]">
                            MokuMoku Match
                        </h2>

                        <p class="mt-3 text-sm font-bold leading-7 text-[#5B6472]">
                            一人で働く毎日に、ちょうどいいつながりを。
                            作業仲間を探せるフルリモート向けマッチングサービスです。
                        </p>

                        <a href="{{ route('home') }}"
                           class="mt-5 inline-flex w-full items-center justify-center bg-[#0B1548] px-4 py-3 text-sm font-black text-white transition hover:bg-[#17215A]">
                            サービスを見る
                        </a>
                    </section>

                    <section class="bg-white p-5 shadow-sm">
                        <div class="border-b-2 border-[#E8E0D2] pb-3">
                            <h2 class="text-lg font-black text-[#111827]">
                                注目テーマ
                            </h2>
                        </div>

                        <div class="mt-4 space-y-3">
                            <a href="{{ route('articles.index') }}" class="flex items-center gap-3 border-b border-[#EFE7DA] pb-3 transition hover:text-[#C9825D]">
                                <span class="flex h-8 w-8 shrink-0 items-center justify-center bg-[#C9BA84] text-sm font-black text-white">
                                    1
                                </span>
                                <span class="text-sm font-black leading-6">
                                    フルリモートで集中する方法
                                </span>
                            </a>

                            <a href="{{ route('articles.index') }}" class="flex items-center gap-3 border-b border-[#EFE7DA] pb-3 transition hover:text-[#C9825D]">
                                <span class="flex h-8 w-8 shrink-0 items-center justify-center bg-[#C9BA84] text-sm font-black text-white">
                                    2
                                </span>
                                <span class="text-sm font-black leading-6">
                                    作業仲間の見つけ方
                                </span>
                            </a>

                            <a href="{{ route('articles.index') }}" class="flex items-center gap-3 transition hover:text-[#C9825D]">
                                <span class="flex h-8 w-8 shrink-0 items-center justify-center bg-[#C9BA84] text-sm font-black text-white">
                                    3
                                </span>
                                <span class="text-sm font-black leading-6">
                                    一人作業を続けるコツ
                                </span>
                            </a>
                        </div>
                    </section>
                </div>
            </aside>
        </div>
    </main>

    <footer class="mt-8 border-t border-[#D8CCB8] bg-[#0B1548] text-white">
        <div class="mx-auto grid max-w-7xl gap-8 px-4 py-10 sm:px-6 lg:grid-cols-[1.4fr_1fr] lg:px-8">
            <div>
                <p class="text-2xl font-black tracking-[0.08em]">
                    MokuMoku Match
                </p>

                <p class="mt-2 text-xs font-bold tracking-[0.24em] text-[#BFA46A]">
                    REMOTE WORK MAGAZINE
                </p>

                <p class="mt-5 max-w-2xl text-sm font-bold leading-7 text-white/70">
                    MokuMokuMatchは、フリーランスやリモートワーカーが一緒に黙々作業できる仲間を探せるマッチングサービスです。
                    このメディアでは、働く場所・集中環境・作業仲間づくりに役立つ情報を発信しています。
                </p>
            </div>

            <div class="flex flex-col gap-3 text-sm font-black lg:items-end">
                <a href="{{ route('articles.index') }}" class="text-white/75 transition hover:text-white">
                    記事一覧
                </a>

                <a href="{{ route('home') }}" class="text-white/75 transition hover:text-white">
                    MokuMoku Matchへ
                </a>

                <a href="{{ route('home') }}"
                   class="mt-3 inline-flex w-fit items-center justify-center bg-white px-5 py-3 text-[#0B1548] transition hover:bg-[#F7F3EA]">
                    サービスを見る
                </a>
            </div>
        </div>

        <div class="border-t border-white/10">
            <div class="mx-auto max-w-7xl px-4 py-4 text-xs text-white/50 sm:px-6 lg:px-8">
                <p>© {{ date('Y') }} MokuMoku Match</p>
            </div>
        </div>
    </footer>

    @stack('scripts')
</body>
</html>
