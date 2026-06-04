document.addEventListener('DOMContentLoaded', () => {
  const root = document.querySelector('[data-message-index-polling]');

  if (!root) {
      return;
  }

  const latestUrl = root.dataset.latestUrl;

  if (!latestUrl) {
      return;
  }

  let isFetching = false;

  function escapeHtml(value) {
      return String(value ?? '')
          .replaceAll('&', '&amp;')
          .replaceAll('<', '&lt;')
          .replaceAll('>', '&gt;')
          .replaceAll('"', '&quot;')
          .replaceAll("'", '&#039;');
  }

  function unreadBadgeHtml(unreadCount) {
      if (!unreadCount || Number(unreadCount) <= 0) {
          return '';
      }

      const count = Number(unreadCount) > 99 ? '99+' : String(unreadCount);

      return `
          <span class="inline-flex min-w-[26px] items-center justify-center rounded-full bg-rose-500 px-2 py-1 text-xs font-black text-white">
              ${escapeHtml(count)}
          </span>
      `;
  }

  function itemHtml(item) {
      const minePrefix = item.is_mine ? 'あなた: ' : '';

      return `
          <a
              href="${escapeHtml(item.show_url)}"
              data-message-partner-id="${escapeHtml(item.partner_id)}"
              class="block rounded-2xl border border-slate-200 bg-white p-4 shadow-sm transition hover:-translate-y-0.5 hover:border-indigo-200 hover:shadow-md"
          >
              <div class="flex items-center gap-4">
                  <div class="h-14 w-14 shrink-0 overflow-hidden rounded-full bg-slate-100">
                      <img
                          src="${escapeHtml(item.avatar_url)}"
                          alt="${escapeHtml(item.display_name)}のプロフィール画像"
                          class="h-full w-full object-cover"
                      >
                  </div>

                  <div class="min-w-0 flex-1">
                      <div class="flex items-start justify-between gap-3">
                          <div class="min-w-0">
                              <p class="truncate text-base font-black text-slate-900">
                                  ${escapeHtml(item.display_name)}
                              </p>

                              <p class="mt-1 truncate text-xs font-bold text-indigo-600">
                                  ${escapeHtml(item.job_type)}
                              </p>
                          </div>

                          <div class="shrink-0 text-right">
                              <p class="text-xs font-bold text-slate-400">
                                  ${escapeHtml(item.pc_time || item.sp_time)}
                              </p>

                              <div class="mt-2">
                                  ${unreadBadgeHtml(item.unread_count)}
                              </div>
                          </div>
                      </div>

                      <p class="mt-3 line-clamp-2 text-sm font-bold leading-relaxed text-slate-600">
                          ${escapeHtml(minePrefix + item.last_body)}
                      </p>
                  </div>
              </div>
          </a>
      `;
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

          if (!items.length) {
              return;
          }

          root.innerHTML = '';

          items.forEach((item) => {
              root.insertAdjacentHTML('beforeend', itemHtml(item));
          });
      } catch (error) {
          console.error('メッセージ一覧ポーリングに失敗しました。', error);
      } finally {
          isFetching = false;
      }
  }

  fetchLatestIndex();

  window.setInterval(fetchLatestIndex, 5000);
});
