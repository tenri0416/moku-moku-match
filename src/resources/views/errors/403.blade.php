@php
    $message = $exception->getMessage();

    $title = 'このページを表示できません';

    $defaultMessage = 'このページを表示する権限がないか、ログイン中のアカウントでは操作できません。';

    $displayMessage = filled($message) && $message !== 'Forbidden'
        ? $message
        : $defaultMessage;
@endphp

<x-dynamic-component
    component="errors.layout"
    code="403"
    title="{{ $title }}"
    message="{{ $displayMessage }}"
    detail="募集、メッセージ、トレーニング結果などは、本人または関係するユーザーだけが確認できる場合があります。"
    illustration="🔒"
    primaryLabel="トップページへ戻る"
    primaryUrl="{{ url('/') }}"
    secondaryLabel="マイページへ戻る"
    secondaryUrl="{{ route('mypage') }}"
/>
