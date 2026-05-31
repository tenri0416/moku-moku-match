import { Poller } from './poller';

document.addEventListener('DOMContentLoaded', () => {
    const root = document.querySelector('[data-message-polling]');

    if (!root) {
        return;
    }

    const list = root.querySelector('[data-message-list]');
    const form = root.querySelector('[data-message-form]');
    const textarea = root.querySelector('[data-message-body]');
    const latestUrl = root.dataset.latestUrl;
    const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');

    let latestMessageId = Number(root.dataset.latestMessageId || 0);

    if (!list || !latestUrl) {
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
        return escapeHtml(value).replace(/\n/g, '<br>');
    }

    function messageHtml(message) {
        const mineClass = message.is_mine
            ? 'justify-end'
            : 'justify-start';

        const bubbleClass = message.is_mine
            ? 'rounded-br-md bg-indigo-600 text-white'
            : 'rounded-bl-md bg-slate-100 text-slate-800';

        const metaClass = message.is_mine
            ? 'justify-end'
            : 'justify-start';

        const senderName = message.is_mine
            ? '自分'
            : escapeHtml(message.sender_name);

        return `
            <div class="flex ${mineClass}" data-message-id="${message.id}">
                <div class="max-w-[85%] sm:max-w-[70%]">
                    <div class="mb-1 flex items-center gap-2 ${metaClass}">
                        <span class="text-xs font-semibold text-slate-500">
                            ${senderName}
                        </span>

                        <span class="text-xs text-slate-400">
                            ${escapeHtml(message.created_at)}
                        </span>
                    </div>

                    <div class="rounded-2xl px-4 py-3 text-sm leading-7 shadow-sm ${bubbleClass}">
                        ${nl2br(message.body)}
                    </div>

                    ${message.is_mine ? `
                        <div class="mt-1 text-right text-xs text-slate-400">
                            未読
                        </div>
                    ` : ''}
                </div>
            </div>
        `;
    }

    function appendMessages(messages) {
        if (!messages || messages.length === 0) {
            return;
        }

        const emptyMessage = list.querySelector('[data-empty-message]');
        if (emptyMessage) {
            emptyMessage.remove();
        }

        messages.forEach((message) => {
            if (list.querySelector(`[data-message-id="${message.id}"]`)) {
                return;
            }

            list.insertAdjacentHTML('beforeend', messageHtml(message));
            latestMessageId = Math.max(latestMessageId, Number(message.id));
        });

        list.scrollIntoView({ block: 'end', behavior: 'smooth' });
    }

    async function fetchLatestMessages() {
        const url = new URL(latestUrl, window.location.origin);
        url.searchParams.set('after_id', latestMessageId);

        const response = await fetch(url.toString(), {
            method: 'GET',
            headers: {
                'Accept': 'application/json',
            },
        });

        if (!response.ok) {
            return;
        }

        const data = await response.json();
        appendMessages(data.messages);
    }

    async function sendMessage(event) {
        event.preventDefault();

        if (!form || !textarea) {
            return;
        }

        const body = textarea.value.trim();

        if (!body) {
            return;
        }

        const formData = new FormData(form);

        const response = await fetch(form.action, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': csrfToken,
                'Accept': 'application/json',
            },
            body: formData,
        });

        if (!response.ok) {
            return;
        }

        const data = await response.json();

        textarea.value = '';

        if (data.message) {
            appendMessages([data.message]);
        }

        await fetchLatestMessages();
    }

    if (form) {
        form.addEventListener('submit', sendMessage);
    }

    const poller = new Poller({
        interval: 5000,
        callback: fetchLatestMessages,
    });

    poller.start();
});
