@php
    $message = $exception->getMessage();

    $displayMessage = filled($message) && $message !== 'Not Found'
        ? $message
        : 'お探しのページは見つかりませんでした。URLが間違っているか、ページが削除された可能性があります。';
@endphp

<x-dynamic-component
    component="errors.layout"
    code="404"
    title="ページが見つかりません"
    message="{{ $displayMessage }}"
    detail="募集や記事は、公開終了・削除・URL変更により表示できない場合があります。"
    illustration="🧭"
    primaryLabel="トップページへ戻る"
    primaryUrl="{{ url('/') }}"
    secondaryLabel="マイページへ戻る"
    secondaryUrl="{{ route('mypage') }}"
/>
