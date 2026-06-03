@php
    $exceptionMessage = isset($exception) ? $exception->getMessage() : null;

    $displayMessage = filled($exceptionMessage) && $exceptionMessage !== 'Not Found'
        ? $exceptionMessage
        : 'お探しのページは見つかりませんでした。URLが間違っているか、ページが削除された可能性があります。';

    $myPageUrl = auth()->check() && Route::has('mypage')
        ? route('mypage')
        : url('/');
@endphp

@include('errors._layout', [
    'code' => '404',
    'title' => 'ページが見つかりません',
    'message' => $displayMessage,
    'detail' => 'ページが削除された、またはURLが変更された可能性があります。',
    'illustration' => '🧭',
    'primaryLabel' => 'トップページへ戻る',
    'primaryUrl' => url('/'),
    'secondaryLabel' => 'マイページへ戻る',
    'secondaryUrl' => $myPageUrl,
])
