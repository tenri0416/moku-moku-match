@extends('layouts.app')

@section('title', 'メッセージ')

@section('content')
@php
    $latestMessageId = $messages->max('id') ?? 0;

    $partner = $user;
    $partnerProfile = $partner?->profile;

    $avatarPath = $partnerProfile?->avatar_path;
    $partnerAvatarUrl = $avatarPath
        ? asset('storage/' . ltrim($avatarPath, '/'))
        : asset('images/default-avatar.png');

    $partnerDisplayName = $partnerProfile?->display_name ?? $partner?->name ?? 'ユーザー';
    $partnerJobType = $partnerProfile?->job_type ?? '職種未設定';

    $workPostTitle = $workPost->title ?? 'メッセージ';
@endphp

<div
    data-message-polling
    data-latest-url="{{ route('messages.latest', [$workPost, $user]) }}"
    data-latest-message-id="{{ $latestMessageId }}"
>
    @include('messages.show_sp')
    @include('messages.show_pc')
</div>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const messageLists = document.querySelectorAll('[data-message-list]');
    const forms = document.querySelectorAll('[data-message-form]');

    messageLists.forEach(function (messageList) {
        messageList.scrollTop = messageList.scrollHeight;
    });

    forms.forEach(function (form) {
        const button = form.querySelector('[data-message-submit]');
        const textarea = form.querySelector('[data-message-body]');

        form.addEventListener('submit', function () {
            if (button) {
                button.disabled = true;
                button.textContent = '送信中...';
            }

            if (textarea) {
                setTimeout(function () {
                    textarea.value = '';
                }, 100);
            }
        });
    });
});
</script>
@endsection
