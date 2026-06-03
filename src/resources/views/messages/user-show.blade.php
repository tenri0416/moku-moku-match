@extends('layouts.app')

@section('title', 'メッセージ')

@section('content')
<div
    data-message-polling
    data-latest-url="{{ route('messages.users.latest', $user) }}"
    data-latest-message-id="{{ $latestMessageId ?? 0 }}"
>
    @include('messages.user-show_sp')
    @include('messages.user-show_pc')
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
