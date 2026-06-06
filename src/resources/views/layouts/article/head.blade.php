<head>
    <meta charset="UTF-8">

    <title>@yield('title', 'YomuWorks | 読む、働く、暮らすを整えるメディア')</title>

    <meta
        name="description"
        content="@yield('description', 'YomuWorksは、技術、個人開発、暮らし、働き方、MokuMoku Matchの活用方法を届ける読みものメディアです。')"
    >

    <meta name="viewport" content="width=device-width, initial-scale=1">

    <meta property="og:site_name" content="YomuWorks">
    <meta property="og:type" content="@yield('og_type', 'website')">
    <meta property="og:title" content="@yield('og_title', trim($__env->yieldContent('title', 'YomuWorks')))">
    <meta property="og:description" content="@yield('og_description', trim($__env->yieldContent('description', '読む、働く、暮らすを整えるメディアです。')))">
    <meta property="og:url" content="{{ url()->current() }}">
    <meta property="og:image" content="@yield('og_image', asset('images/articles/pc-top.png'))">
    
    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:title" content="@yield('og_title', trim($__env->yieldContent('title', 'YomuWorks')))">
    <meta name="twitter:description" content="@yield('og_description', trim($__env->yieldContent('description', '読む、働く、暮らすを整えるメディアです。')))">
    <meta name="twitter:image" content="@yield('og_image', asset('images/articles/pc-top.png'))">

    <link rel="canonical" href="{{ url()->current() }}">
    <link rel="icon" href="{{ asset('favicon.ico') }}">

    {{-- 記事サイト専用CSS --}}
    <link rel="stylesheet" href="{{ asset('css/article-pc.css') }}?v={{ filemtime(public_path('css/article-pc.css')) }}">
    <link rel="stylesheet" href="{{ asset('css/article-sp.css') }}?v={{ filemtime(public_path('css/article-sp.css')) }}">

    @stack('styles')
</head>
