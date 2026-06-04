import { Poller } from './poller';

document.addEventListener('DOMContentLoaded', () => {
    const root = document.querySelector('[data-message-polling]');

    if (!root) {
        return;
    }

    const lists = root.querySelectorAll('[data-message-list]');
    const forms = root.querySelectorAll('[data-message-form]');
    const latestUrl = root.dataset.latestUrl;
    const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');

    let latestMessageId = Number(root.dataset.latestMessageId || 0);

    if (!lists.length || !latestUrl) {
        return;
    }

    function escapeHtml(value) {
        return String(value ?? '')
            .replaceAll('&', '&amp;')
            .replaceAll('<', '&lt;')
            .replaceAll('>', '&gt;')
            .replaceAll('"', '&quot;')
            .replaceAll("'", '&#039;');
    }

    function nl2br(value) {
        return escapeHtml(value).replace(/\r?\n/g, '<br>');
    }

    function hasMessage(list, messageId) {
        const items = list.querySelectorAll('[data-message-id]');

        for (const item of items) {
            if (String(item.dataset.messageId) === String(messageId)) {
                return true;
            }
        }

        return false;
    }

    function isSpList(list) {
        return list.closest('.md\\:hidden') !== null;
    }

    function buildSpMessageHtml(message) {
        const isMine = Boolean(message.is_mine);
        const senderName = message.sender_name || 'ユーザー';
        const avatarUrl = message.sender_avatar_url || '/images/default-avatar.png';
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

    function buildPcMessageHtml(message) {
        const isMine = Boolean(message.is_mine);
        const senderName = message.sender_name || 'ユーザー';
        const avatarUrl = message.sender_avatar_url || '/images/default-avatar.png';
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

    function appendMessages(messages) {
        if (!messages || messages.length === 0) {
            return;
        }

        lists.forEach((list) => {
            const emptyMessage = list.querySelector('[data-empty-message]');

            if (emptyMessage) {
                emptyMessage.remove();
            }

            messages.forEach((message) => {
                if (hasMessage(list, message.id)) {
                    return;
                }

                const html = isSpList(list)
                    ? buildSpMessageHtml(message)
                    : buildPcMessageHtml(message);

                list.insertAdjacentHTML('beforeend', html);
                latestMessageId = Math.max(latestMessageId, Number(message.id));
            });

            list.scrollTop = list.scrollHeight;
        });

        root.dataset.latestMessageId = String(latestMessageId);
    }

    async function fetchLatestMessages() {
        const separator = latestUrl.includes('?') ? '&' : '?';
        const requestUrl = latestUrl
            + separator
            + 'after_id=' + encodeURIComponent(latestMessageId)
            + '&_=' + Date.now();

        const response = await fetch(requestUrl, {
            method: 'GET',
            headers: {
                'Accept': 'application/json',
                'X-Requested-With': 'XMLHttpRequest',
                'Cache-Control': 'no-cache',
            },
            credentials: 'same-origin',
            cache: 'no-store',
        });

        if (!response.ok) {
            console.error('新着メッセージ取得エラー:', response.status);
            return;
        }

        const data = await response.json();
        appendMessages(data.messages || []);
    }

    function resetFormButton(form) {
        const button = form.querySelector('[data-message-submit]');

        if (!button) {
            return;
        }

        button.disabled = false;
        button.textContent = button.dataset.defaultText || '送信する';
        form.dataset.submitting = '0';
    }

    async function sendMessage(event) {
        event.preventDefault();

        const form = event.currentTarget;
        const textarea = form.querySelector('[data-message-body]');
        const button = form.querySelector('[data-message-submit]');

        if (!textarea || !button) {
            return;
        }

        const body = textarea.value.trim();

        if (!body) {
            resetFormButton(form);
            return;
        }

        if (form.dataset.submitting === '1') {
            return;
        }

        form.dataset.submitting = '1';
        button.dataset.defaultText = button.textContent.trim() || '送信する';
        button.disabled = true;
        button.textContent = '送信中...';

        try {
            const formData = new FormData(form);

            const response = await fetch(form.action, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': csrfToken,
                    'Accept': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest',
                },
                body: formData,
                credentials: 'same-origin',
            });

            if (!response.ok) {
                console.error('メッセージ送信エラー:', response.status);
                return;
            }

            const data = await response.json();

            textarea.value = '';

            if (data.message) {
                appendMessages([data.message]);
            }

            await fetchLatestMessages();
        } catch (error) {
            console.error('メッセージ送信に失敗しました。', error);
        } finally {
            resetFormButton(form);
        }
    }

    lists.forEach((list) => {
        list.scrollTop = list.scrollHeight;
    });

    forms.forEach((form) => {
        form.addEventListener('submit', sendMessage);

        window.addEventListener('pageshow', () => {
            resetFormButton(form);
        });
    });

    const poller = new Poller({
        interval: 3000,
        callback: fetchLatestMessages,
    });

    poller.start();
});
