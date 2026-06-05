<head>
  <meta charset="UTF-8">
  <title>@yield('title', 'MokuMoku Match | 一緒に作業できる仲間を探せるマッチングサービス')</title>

  <meta name="description" content="@yield('description', 'MokuMoku Matchは、フリーランスやリモートワーカーが一緒に黙々作業できる仲間を探せるマッチングサービスです。オンライン・オフラインで作業仲間を募集できます。')">
  <meta name="viewport" content="width=device-width, initial-scale=1">

  <meta property="og:site_name" content="MokuMoku Match">
  <meta property="og:type" content="@yield('og_type', 'website')">
  <meta property="og:title" content="@yield('og_title', trim($__env->yieldContent('title', 'MokuMoku Match')))">
  <meta property="og:description" content="@yield('og_description', trim($__env->yieldContent('description', '一緒に黙々作業できる仲間を探せるマッチングサービスです。')))">
  <meta property="og:url" content="{{ url()->current() }}">
  <meta property="og:image" content="{{ asset('images/ogp.png') }}">

  <meta name="twitter:card" content="summary_large_image">
  <meta name="twitter:title" content="@yield('og_title', trim($__env->yieldContent('title', 'MokuMoku Match')))">
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
  @stack('styles')
</head>
