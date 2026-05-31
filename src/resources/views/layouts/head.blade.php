<head>
  <meta charset="UTF-8">
  <meta name="csrf-token" content="{{ csrf_token() }}">
  @include('layouts.meta.seo')
  @include('layouts.meta.ogp')
  @include('layouts.meta.ga4')
  @include('layouts.meta.favicon')

  @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
