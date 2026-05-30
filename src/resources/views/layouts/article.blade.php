<!DOCTYPE html>
<html lang="ja">
@include('layouts.article.head')

<body class="min-h-screen bg-[#F7F3EA] text-[#1F2933] antialiased">
    <div class="border-t-4 border-[#C9825D] bg-white">
        @include('layouts.article.header')
        @include('layouts.article.feature')
        @include('layouts.article.theme-tags')
    </div>

    @include('layouts.article.content')
    @include('layouts.article.footer')

    @stack('scripts')
</body>
</html>
