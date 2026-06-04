const STORAGE_KEY = 'mokumoku_scroll_restore';

function currentPath() {
    return window.location.pathname;
}

function saveScrollPosition() {
    const data = {
        path: currentPath(),
        x: window.scrollX,
        y: window.scrollY,
        savedAt: Date.now(),
    };

    sessionStorage.setItem(STORAGE_KEY, JSON.stringify(data));
}

function restoreScrollPosition() {
    const raw = sessionStorage.getItem(STORAGE_KEY);

    if (!raw) {
        return;
    }

    try {
        const data = JSON.parse(raw);

        if (!data || data.path !== currentPath()) {
            sessionStorage.removeItem(STORAGE_KEY);
            return;
        }

        // 古すぎるスクロール情報は使わない
        const isExpired = Date.now() - Number(data.savedAt || 0) > 60 * 1000;

        if (isExpired) {
            sessionStorage.removeItem(STORAGE_KEY);
            return;
        }

        window.scrollTo({
            left: Number(data.x || 0),
            top: Number(data.y || 0),
            behavior: 'auto',
        });

        sessionStorage.removeItem(STORAGE_KEY);
    } catch (error) {
        sessionStorage.removeItem(STORAGE_KEY);
    }
}

document.addEventListener('DOMContentLoaded', () => {
    restoreScrollPosition();

    /**
     * GETフォーム送信時にスクロール位置を保存
     * 例：検索フォーム、絞り込みフォーム
     */
    document.querySelectorAll('form[method="GET"], form[method="get"]').forEach((form) => {
        form.addEventListener('submit', () => {
            const actionUrl = new URL(form.action || window.location.href, window.location.origin);

            // 同じ画面内のGET検索だけ対象
            if (actionUrl.pathname === currentPath()) {
                saveScrollPosition();
            }
        });
    });

    /**
     * 同じパスへのリンククリック時にスクロール位置を保存
     * 例：ページネーション、クイックフィルター、タブ切り替え
     */
    document.querySelectorAll('a[href]').forEach((link) => {
        link.addEventListener('click', () => {
            const href = link.getAttribute('href');

            if (!href || href.startsWith('#') || href.startsWith('javascript:')) {
                return;
            }

            const url = new URL(href, window.location.origin);

            // 別ドメインは対象外
            if (url.origin !== window.location.origin) {
                return;
            }

            // 同じ画面内の遷移だけ対象
            if (url.pathname === currentPath()) {
                saveScrollPosition();
            }
        });
    });
});
