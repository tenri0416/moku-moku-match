<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta name="csrf-token" content="{{ csrf_token() }}">
  <meta name="google-adsense-account" content="ca-pub-5684202120084292">

  <title>@yield('title', '管理画面') - MokuMoku Match</title>

  @vite(['resources/css/app.css', 'resources/js/app.js'])

  @stack('styles')
</head>
