@php
    $exceptionMessage = isset($exception) ? $exception->getMessage() : null;

    $displayMessage = filled($exceptionMessage) && $exceptionMessage !== 'Forbidden'
        ? $exceptionMessage
        : 'このページを表示する権限がないか、ログイン中のアカウントでは操作できません。';

    $myPageUrl = auth()->check() && Route::has('mypage')
        ? route('mypage')
        : url('/');
@endphp

@include('errors._layout', [
    'code' => '403',
    'title' => 'このページを表示できません',
    'message' => $displayMessage,
    'detail' => '募集、メッセージ、トレーニング結果などは、本人または関係するユーザーだけが確認できる場合があります。',
    'illustration' => '🔒',
    'primaryLabel' => 'トップページへ戻る',
    'primaryUrl' => url('/'),
    'secondaryLabel' => 'マイページへ戻る',
    'secondaryUrl' => $myPageUrl,
])
