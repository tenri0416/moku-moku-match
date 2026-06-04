document.addEventListener('DOMContentLoaded', () => {
  const root = document.querySelector('[data-header-realtime]');

  if (!root) {
      return;
  }

  const latestUrl = root.dataset.latestUrl;

  if (!latestUrl) {
      return;
  }

  const notificationCountElements = document.querySelectorAll('[data-header-notification-count]');
  const messageCountElements = document.querySelectorAll('[data-header-message-count]');

  let isFetching = false;

  function formatCount(count) {
      const number = Number(count || 0);

      if (number <= 0) {
          return '';
      }

      return number > 99 ? '99+' : String(number);
  }

  function updateBadge(elements, count) {
      const text = formatCount(count);

      elements.forEach((element) => {
          if (!text) {
              element.textContent = '';
              element.classList.add('hidden');
              return;
          }

          element.textContent = text;
          element.classList.remove('hidden');
      });
  }

  async function fetchHeaderStatus() {
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
              console.error('ヘッダー情報の取得に失敗しました:', response.status);
              return;
          }

          const data = await response.json();

          updateBadge(notificationCountElements, data.notification_unread_count);
          updateBadge(messageCountElements, data.message_unread_count);
      } catch (error) {
          console.error('ヘッダーのリアルタイム更新に失敗しました。', error);
      } finally {
          isFetching = false;
      }
  }

  fetchHeaderStatus();

  window.setInterval(fetchHeaderStatus, 5000);
});
