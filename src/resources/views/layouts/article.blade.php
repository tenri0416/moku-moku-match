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
            function gtag(){dataLayer.push(arguments);}
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

<body class="bg-slate-50 text-slate-900 antialiased">
    <header class="border-b border-slate-200 bg-white">
        <div class="mx-auto flex max-w-5xl items-center justify-between px-4 py-4 sm:px-6 lg:px-8">
            <a href="{{ route('articles.index') }}" class="font-black tracking-tight text-slate-900">
                MokuMoku Match Magazine
            </a>

            <nav class="flex items-center gap-4 text-sm font-bold text-slate-600">
                <a href="{{ route('articles.index') }}" class="hover:text-indigo-600">
                    記事一覧
                </a>

                <a href="{{ route('home') }}" class="hover:text-indigo-600">
                    MokuMoku Matchへ
                </a>
            </nav>
        </div>
    </header>

    <main>
        @yield('content')
    </main>

    <footer class="border-t border-slate-200 bg-white">
        <div class="mx-auto max-w-5xl px-4 py-8 text-sm text-slate-500 sm:px-6 lg:px-8">
            <p>
                © {{ date('Y') }} MokuMoku Match
            </p>
        </div>
    </footer>

    @stack('scripts')
</body>
</html>
