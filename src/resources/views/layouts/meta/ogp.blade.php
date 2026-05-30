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
