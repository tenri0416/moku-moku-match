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
    const pollingRoot = document.querySelector('[data-message-polling]');
    const messageLists = document.querySelectorAll('[data-message-list]');
    const forms = document.querySelectorAll('[data-message-form]');

    /**
     * 初期表示時に一番下までスクロール
     */
    messageLists.forEach(function (messageList) {
        messageList.scrollTop = messageList.scrollHeight;
    });

    /**
     * HTMLエスケープ
     */
    function escapeHtml(value) {
        return String(value ?? '')
            .replaceAll('&', '&amp;')
            .replaceAll('<', '&lt;')
            .replaceAll('>', '&gt;')
            .replaceAll('"', '&quot;')
            .replaceAll("'", '&#039;');
    }

    /**
     * 改行をbrに変換
     */
    function nl2br(value) {
        return escapeHtml(value).replace(/\r?\n/g, '<br>');
    }

    /**
     * スマホ側のメッセージHTML
     */
    function buildSpMessageHtml(message) {
        const isMine = Boolean(message.is_mine);
        const senderName = message.sender_name || 'ユーザー';
        const avatarUrl = message.sender_avatar_url || '{{ asset('images/default-avatar.png') }}';
        const time = message.created_time || '';
        const readLabel = message.read_label || '未読';

        const avatarHtml = isMine
            ? ''
            : `
                <div class="h-9 w-9 shrink-0 overflow-hidden rounded-full bg-blue-50">
                    <img
                        src="${escapeHtml(avatarUrl)}"
                        alt="${escapeHtml(senderName)}のプロフィール画像"
                        class="h-full w-full object-cover"
                    >
                </div>
            `;

        const readLabelHtml = isMine
            ? `
                <div class="mt-1 text-right text-[11px] font-bold text-[#94A3B8]">
                    ${escapeHtml(readLabel)}
                </div>
            `
            : '';

        return `
            <div class="flex ${isMine ? 'justify-end' : 'justify-start'}" data-message-id="${escapeHtml(message.id)}">
                <div class="flex max-w-[86%] gap-2 ${isMine ? 'flex-row-reverse' : ''}">
                    ${avatarHtml}

                    <div>
                        <div class="mb-1 flex items-center gap-2 ${isMine ? 'justify-end' : 'justify-start'}">
                            <span class="text-[11px] font-bold text-[#64748B]">
                                ${isMine ? 'あなた' : escapeHtml(senderName)}
                            </span>

                            <span class="text-[11px] font-bold text-[#94A3B8]">
                                ${escapeHtml(time)}
                            </span>
                        </div>

                        <div class="${isMine ? 'rounded-br-[6px] bg-[#0D4FE8] text-white' : 'rounded-bl-[6px] bg-[#F1F5F9] text-[#071433]'} rounded-[18px] px-4 py-3 text-[15px] font-bold leading-7 shadow-sm">
                            ${nl2br(message.body)}
                        </div>

                        ${readLabelHtml}
                    </div>
                </div>
            </div>
        `;
    }

    /**
     * PC側のメッセージHTML
     */
    function buildPcMessageHtml(message) {
        const isMine = Boolean(message.is_mine);
        const senderName = message.sender_name || 'ユーザー';
        const avatarUrl = message.sender_avatar_url || '{{ asset('images/default-avatar.png') }}';
        const time = message.created_at || '';
        const readLabel = message.read_label || '未読';

        const avatarHtml = isMine
            ? ''
            : `
                <div class="h-11 w-11 shrink-0 overflow-hidden rounded-full bg-blue-50">
                    <img
                        src="${escapeHtml(avatarUrl)}"
                        alt="${escapeHtml(senderName)}のプロフィール画像"
                        class="h-full w-full object-cover"
                    >
                </div>
            `;

        const readLabelHtml = isMine
            ? `
                <div class="mt-1 text-right text-[12px] font-bold text-[#94A3B8]">
                    ${escapeHtml(readLabel)}
                </div>
            `
            : '';

        return `
            <div class="flex ${isMine ? 'justify-end' : 'justify-start'}" data-message-id="${escapeHtml(message.id)}">
                <div class="flex max-w-[72%] gap-3 ${isMine ? 'flex-row-reverse' : ''}">
                    ${avatarHtml}

                    <div>
                        <div class="mb-1 flex items-center gap-2 ${isMine ? 'justify-end' : 'justify-start'}">
                            <span class="text-[12px] font-bold text-[#64748B]">
                                ${isMine ? 'あなた' : escapeHtml(senderName)}
                            </span>

                            <span class="text-[12px] font-bold text-[#94A3B8]">
                                ${escapeHtml(time)}
                            </span>
                        </div>

                        <div class="${isMine ? 'rounded-br-[6px] bg-[#0D4FE8] text-white' : 'rounded-bl-[6px] bg-[#F1F5F9] text-[#071433]'} rounded-[18px] px-5 py-3 text-[15px] font-bold leading-7 shadow-sm">
                            ${nl2br(message.body)}
                        </div>

                        ${readLabelHtml}
                    </div>
                </div>
            </div>
        `;
    }

    /**
     * メッセージ一覧がSPかPCか判定
     */
    function isSpMessageList(messageList) {
        return messageList.closest('.md\\:hidden') !== null;
    }

    /**
     * メッセージを画面に追加
     */
    function appendMessage(message) {
        messageLists.forEach(function (messageList) {
            const messageId = String(message.id);

            // 同じメッセージを重複表示しない
            if (messageList.querySelector('[data-message-id="' + CSS.escape(messageId) + '"]')) {
                return;
            }

            const emptyMessage = messageList.querySelector('[data-empty-message]');
            if (emptyMessage) {
                emptyMessage.remove();
            }

            const html = isSpMessageList(messageList)
                ? buildSpMessageHtml(message)
                : buildPcMessageHtml(message);

            messageList.insertAdjacentHTML('beforeend', html);
            messageList.scrollTop = messageList.scrollHeight;
        });
    }

    /**
     * 新着メッセージ取得
     */
    async function fetchLatestMessages() {
        if (!pollingRoot) {
            return;
        }

        const latestUrl = pollingRoot.dataset.latestUrl;
        let latestMessageId = Number(pollingRoot.dataset.latestMessageId || 0);

        if (!latestUrl) {
            return;
        }

        try {
            const url = new URL(latestUrl, window.location.origin);
            url.searchParams.set('after_id', latestMessageId);

            const response = await fetch(url.toString(), {
                method: 'GET',
                headers: {
                    'Accept': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest',
                },
                credentials: 'same-origin',
            });

            if (!response.ok) {
                return;
            }

            const data = await response.json();
            const messages = data.messages || [];

            if (messages.length === 0) {
                return;
            }

            messages.forEach(function (message) {
                appendMessage(message);
                latestMessageId = Math.max(latestMessageId, Number(message.id));
            });

            pollingRoot.dataset.latestMessageId = String(latestMessageId);
        } catch (error) {
            console.error('新着メッセージの取得に失敗しました。', error);
        }
    }

    /**
     * 3秒ごとに新着確認
     */
    if (pollingRoot) {
        setInterval(fetchLatestMessages, 3000);
    }

    /**
     * 送信時の二重送信防止
     */
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
