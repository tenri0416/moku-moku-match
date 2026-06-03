@php
    $myPageUrl = auth()->check() && Route::has('mypage')
        ? route('mypage')
        : url('/');
@endphp

@include('errors._layout', [
    'code' => '503',
    'title' => 'ただいま準備中です',
    'message' => '現在、サービスが一時的に利用しづらい状態です。少し時間をおいてから再度お試しください。',
    'detail' => 'メンテナンス中、またはアクセス集中により一時的に表示できない可能性があります。',
    'illustration' => '🌙',
    'primaryLabel' => 'トップページへ戻る',
    'primaryUrl' => url('/'),
    'secondaryLabel' => 'マイページへ戻る',
    'secondaryUrl' => $myPageUrl,
])
