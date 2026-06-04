document.addEventListener('DOMContentLoaded', () => {
    const root = document.querySelector('[data-message-index-polling]');

    if (!root) {
        return;
    }

    const latestUrl = root.dataset.latestUrl;

    if (!latestUrl) {
        return;
    }

    const spList = document.querySelector('[data-message-index-list="sp"]');
    const pcList = document.querySelector('[data-message-index-list="pc"]');
    const conversationCountElements = document.querySelectorAll('[data-message-index-conversation-count]');
    const totalUnreadCountElements = document.querySelectorAll('[data-message-index-total-unread-count]');
    const unreadStatusElements = document.querySelectorAll('[data-message-index-unread-status]');

    let isFetching = false;

    function escapeHtml(value) {
        return String(value ?? '')
            .replaceAll('&', '&amp;')
            .replaceAll('<', '&lt;')
            .replaceAll('>', '&gt;')
            .replaceAll('"', '&quot;')
            .replaceAll("'", '&#039;');
    }

    function formatUnreadCount(unreadCount) {
        const count = Number(unreadCount || 0);

        if (count <= 0) {
            return '';
        }

        return count > 99 ? '99+' : String(count);
    }

    function buildSpEmptyHtml() {
        return `
            <div class="rounded-[18px] border border-dashed border-[#CBD7EA] bg-white px-5 py-10 text-center shadow-[0_8px_22px_rgba(15,43,95,0.06)]">
                <div class="mx-auto flex h-16 w-16 items-center justify-center rounded-full bg-blue-50 text-[34px]">
                    💬
                </div>

                <h2 class="mt-4 text-[22px] font-black text-[#071433]">
                    まだメッセージはありません
                </h2>

                <p class="mt-2 text-[15px] font-bold leading-relaxed text-[#64748B]">
                    気になるユーザーにメッセージを送ってみましょう。
                </p>
            </div>
        `;
    }

    function buildPcEmptyHtml() {
        return `
            <div class="rounded-[16px] border border-dashed border-[#CBD7EA] bg-[#FBFCFF] px-6 py-12 text-center">
                <div class="mx-auto flex h-16 w-16 items-center justify-center rounded-full bg-white text-[34px] shadow-sm">
                    💬
                </div>

                <h3 class="mt-4 text-[22px] font-black text-[#071433]">
                    まだメッセージはありません
                </h3>

                <p class="mt-3 text-[15px] font-bold leading-relaxed text-[#64748B]">
                    ランキングやプロフィール画面から、気になるユーザーにメッセージを送ってみましょう。
                </p>
            </div>
        `;
    }

    function buildSpItemHtml(item) {
        const unreadCount = Number(item.unread_count || 0);
        const unreadText = formatUnreadCount(unreadCount);
        const prefix = item.is_mine ? 'あなた：' : `${item.display_name}：`;

        return `
            <a
                href="${escapeHtml(item.show_url)}"
                data-message-partner-id="${escapeHtml(item.partner_id)}"
                class="relative block overflow-hidden rounded-[18px] border border-[#DDE6F5] bg-white px-4 py-4 shadow-[0_8px_22px_rgba(15,43,95,0.06)]"
            >
                ${unreadCount > 0 ? '<span class="absolute left-4 top-5 h-3 w-3 rounded-full bg-[#0D4FE8]"></span>' : ''}

                <div class="flex items-center gap-4">
                    <div class="h-[82px] w-[82px] shrink-0 overflow-hidden rounded-full bg-blue-50">
                        <img
                            src="${escapeHtml(item.avatar_url)}"
                            alt="${escapeHtml(item.display_name)}のプロフィール画像"
                            class="h-full w-full object-cover"
                        >
                    </div>

                    <div class="min-w-0 flex-1">
                        <div class="flex items-start justify-between gap-3">
                            <div class="min-w-0">
                                <h2 class="truncate text-[27px] font-black leading-tight text-[#071433]">
                                    ${escapeHtml(item.display_name)}
                                </h2>

                                <p class="mt-1 truncate text-[19px] font-bold leading-tight text-[#0D4FE8]">
                                    ${escapeHtml(item.job_type)}
                                </p>
                            </div>

                            <p class="shrink-0 text-[18px] font-bold text-[#64748B]">
                                ${escapeHtml(item.sp_time || item.pc_time || '')}
                            </p>
                        </div>

                        <div class="mt-3 flex items-center gap-3">
                            <p class="min-w-0 flex-1 truncate text-[17px] font-bold leading-relaxed text-[#071433]">
                                ${escapeHtml(prefix + (item.last_body || ''))}
                            </p>

                            ${unreadCount > 0 ? `
                                <span class="flex h-[38px] min-w-[78px] shrink-0 items-center justify-center rounded-[12px] bg-[#0D4FE8] px-3 text-[18px] font-black text-white">
                                    未読 ${escapeHtml(unreadText)}
                                </span>
                            ` : ''}

                            <span class="shrink-0 text-[34px] leading-none text-[#8793A8]">
                                ›
                            </span>
                        </div>
                    </div>
                </div>
            </a>
        `;
    }

    function buildPcItemHtml(item) {
        const unreadCount = Number(item.unread_count || 0);
        const unreadText = formatUnreadCount(unreadCount);
        const prefixHtml = item.is_mine
            ? '<span class="font-black text-[#64748B]">あなた：</span>'
            : `<span class="font-black text-[#071433]">${escapeHtml(item.display_name)}：</span>`;

        return `
            <a
                href="${escapeHtml(item.show_url)}"
                data-message-partner-id="${escapeHtml(item.partner_id)}"
                class="block rounded-[16px] border border-[#DDE6F5] bg-white px-5 py-4 transition hover:border-[#8DB3FF] hover:bg-[#FBFCFF] hover:shadow-[0_10px_22px_rgba(15,43,95,0.08)]"
            >
                <div class="flex items-center gap-5">
                    <div class="h-[76px] w-[76px] shrink-0 overflow-hidden rounded-full bg-blue-50">
                        <img
                            src="${escapeHtml(item.avatar_url)}"
                            alt="${escapeHtml(item.display_name)}のプロフィール画像"
                            class="h-full w-full object-cover"
                        >
                    </div>

                    <div class="min-w-0 flex-1">
                        <div class="flex items-start justify-between gap-4">
                            <div class="min-w-0">
                                <h3 class="truncate text-[22px] font-black leading-tight text-[#071433]">
                                    ${escapeHtml(item.display_name)}
                                </h3>

                                <p class="mt-1 truncate text-[15px] font-bold text-[#46516B]">
                                    ${escapeHtml(item.job_type)}
                                </p>
                            </div>

                            <p class="shrink-0 text-[15px] font-bold text-[#64748B]">
                                ${escapeHtml(item.pc_time || item.sp_time || '')}
                            </p>
                        </div>

                        <div class="mt-3 flex items-center gap-3">
                            <p class="min-w-0 flex-1 truncate text-[15px] font-bold leading-relaxed text-[#46516B]">
                                ${prefixHtml}
                                ${escapeHtml(item.last_body || '')}
                            </p>

                            ${unreadCount > 0 ? `
                                <span class="flex h-[32px] min-w-[72px] shrink-0 items-center justify-center rounded-[10px] bg-[#0D4FE8] px-3 text-[14px] font-black text-white">
                                    未読 ${escapeHtml(unreadText)}
                                </span>
                            ` : ''}

                            <span class="shrink-0 text-[32px] leading-none text-[#8793A8]">
                                ›
                            </span>
                        </div>
                    </div>
                </div>
            </a>
        `;
    }

    function updateCounts(items, totalUnreadCount) {
        const conversationCount = items.length;

        conversationCountElements.forEach((element) => {
            element.textContent = String(conversationCount);
        });

        totalUnreadCountElements.forEach((element) => {
            element.textContent = String(totalUnreadCount);
        });

        unreadStatusElements.forEach((element) => {
            if (Number(totalUnreadCount || 0) > 0) {
                element.classList.remove('hidden');
            } else {
                element.classList.add('hidden');
            }
        });
    }

    function renderLists(items) {
        if (spList) {
            spList.innerHTML = items.length
                ? items.map(buildSpItemHtml).join('')
                : buildSpEmptyHtml();
        }

        if (pcList) {
            pcList.innerHTML = items.length
                ? items.map(buildPcItemHtml).join('')
                : buildPcEmptyHtml();
        }
    }

    async function fetchLatestIndex() {
        if (isFetching) {
            return;
        }

        isFetching = true;

        try {
            const separator = latestUrl.includes('?') ? '&' : '?';
            const requestUrl = latestUrl + separator + '_=' + Date.now();

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
                console.error('メッセージ一覧の取得に失敗しました:', response.status);
                return;
            }

            const data = await response.json();
            const items = data.items || [];
            const totalUnreadCount = Number(data.total_unread_count || 0);

            updateCounts(items, totalUnreadCount);
            renderLists(items);
        } catch (error) {
            console.error('メッセージ一覧ポーリングに失敗しました。', error);
        } finally {
            isFetching = false;
        }
    }

    fetchLatestIndex();

    window.setInterval(fetchLatestIndex, 5000);
});
