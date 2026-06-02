{{-- AI処理中モーダル --}}
<div
    id="ai-loading-modal"
    class="fixed inset-0 z-[9999] hidden items-center justify-center bg-slate-900/60 px-4 backdrop-blur-sm"
    aria-hidden="true"
>
    <div class="relative w-full max-w-md rounded-[28px] bg-white p-6 shadow-2xl ring-1 ring-slate-200">
        {{-- 閉じるボタン --}}
        <button
            type="button"
            id="ai-loading-close-button"
            class="absolute right-4 top-4 flex h-9 w-9 items-center justify-center rounded-full bg-slate-100 text-slate-500 transition hover:bg-slate-200 hover:text-slate-800"
            aria-label="閉じる"
        >
            ✕
        </button>

        <div class="text-center">
            <div class="mx-auto flex h-16 w-16 items-center justify-center rounded-full bg-emerald-50">
                <div class="h-9 w-9 animate-spin rounded-full border-4 border-emerald-200 border-t-emerald-600"></div>
            </div>

            <p class="mt-5 text-sm font-black tracking-widest text-emerald-600">
                AI TRAINING
            </p>

            <h2 id="ai-loading-title" class="mt-2 text-2xl font-black text-slate-900">
                AIが準備しています
            </h2>

            <p id="ai-loading-message" class="mt-3 text-sm font-semibold leading-7 text-slate-600">
                内容を確認しています。完了まで少しだけお待ちください。
            </p>

            <div class="mt-6">
                <div class="mb-2 flex items-center justify-between">
                    <span id="ai-loading-step" class="text-xs font-bold text-slate-500">
                        処理を開始しています
                    </span>

                    <span id="ai-loading-percent" class="text-sm font-black text-emerald-600">
                        0%
                    </span>
                </div>

                <div class="h-3 overflow-hidden rounded-full bg-slate-100">
                    <div
                        id="ai-loading-bar"
                        class="h-full w-0 rounded-full bg-emerald-500 transition-all duration-500"
                    ></div>
                </div>
            </div>

            <div class="mt-5 rounded-2xl bg-slate-50 p-4 text-left">
                <p class="text-xs font-bold text-slate-500">
                    お願い
                </p>
                <p class="mt-1 text-sm font-semibold leading-6 text-slate-600">
                    AIが問題作成・採点を行っています。画面を閉じたり、戻るボタンを押さずにお待ちください。
                </p>
            </div>

            <p class="mt-3 text-xs font-semibold leading-5 text-slate-400">
                閉じるボタンを押すと表示だけ閉じます。処理中の場合は、再送信せずに少し待ってください。
            </p>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const modal = document.getElementById('ai-loading-modal');
    const closeButton = document.getElementById('ai-loading-close-button');
    const title = document.getElementById('ai-loading-title');
    const message = document.getElementById('ai-loading-message');
    const stepText = document.getElementById('ai-loading-step');
    const percentText = document.getElementById('ai-loading-percent');
    const bar = document.getElementById('ai-loading-bar');

    if (!modal || !title || !message || !stepText || !percentText || !bar) {
        return;
    }

    let progressInterval = null;

    function resetProgress() {
        if (progressInterval) {
            clearInterval(progressInterval);
            progressInterval = null;
        }

        stepText.textContent = '処理を開始しています';
        percentText.textContent = '0%';
        bar.style.width = '0%';
    }

    function showAiLoadingModal(loadingType) {
        resetProgress();

        let percent = 0;

        if (loadingType === 'question') {
            title.textContent = 'AIが問題を作成しています';
            message.textContent = 'あなたの成長につながる問題を準備しています。少しだけお待ちください。';
        } else if (loadingType === 'score') {
            title.textContent = 'AIが採点しています';
            message.textContent = '回答を読み取り、点数・良い点・改善点・次回の課題を作成しています。';
        } else {
            title.textContent = 'AIが処理しています';
            message.textContent = '内容を確認しています。完了まで少しだけお待ちください。';
        }

        stepText.textContent = 'AIに接続しています';
        percentText.textContent = '0%';
        bar.style.width = '0%';

        modal.classList.remove('hidden');
        modal.classList.add('flex');
        modal.setAttribute('aria-hidden', 'false');

        progressInterval = setInterval(function () {
            if (percent < 30) {
                percent += Math.floor(Math.random() * 8) + 4;
                stepText.textContent = '入力内容を確認しています';
            } else if (percent < 65) {
                percent += Math.floor(Math.random() * 6) + 3;
                stepText.textContent = loadingType === 'question'
                    ? '問題文を作成しています'
                    : 'AIが回答を採点しています';
            } else if (percent < 88) {
                percent += Math.floor(Math.random() * 4) + 2;
                stepText.textContent = loadingType === 'question'
                    ? '出題内容を整えています'
                    : 'アドバイスを作成しています';
            } else if (percent < 95) {
                percent += 1;
                stepText.textContent = 'もう少しで完了します';
            }

            if (percent > 95) {
                percent = 95;
            }

            percentText.textContent = percent + '%';
            bar.style.width = percent + '%';
        }, 700);
    }

    function hideAiLoadingModal() {
        resetProgress();

        modal.classList.add('hidden');
        modal.classList.remove('flex');
        modal.setAttribute('aria-hidden', 'true');

        // ブラウザバック後にボタンが無効のまま残る対策
        document.querySelectorAll('button[type="submit"]').forEach(function (button) {
            if (button.dataset.originalText) {
                button.textContent = button.dataset.originalText;
            }

            button.disabled = false;
            button.classList.remove('opacity-60', 'cursor-not-allowed');
        });
    }

    // ✕ボタンで閉じる
    if (closeButton) {
        closeButton.addEventListener('click', function () {
            hideAiLoadingModal();
        });
    }

    // Escキーでも閉じる
    document.addEventListener('keydown', function (event) {
        if (event.key === 'Escape') {
            hideAiLoadingModal();
        }
    });

    // ブラウザバック・履歴復元時にモーダルを必ず閉じる
    window.addEventListener('pageshow', function () {
        hideAiLoadingModal();
    });

    // 通常フォーム送信時
    const forms = document.querySelectorAll('form[data-ai-loading="true"]');

    forms.forEach(function (form) {
        form.addEventListener('submit', function () {
            const loadingType = form.dataset.aiLoadingType || 'score';

            showAiLoadingModal(loadingType);

            const submitButton = form.querySelector('button[type="submit"]');

            if (submitButton) {
                submitButton.disabled = true;
                submitButton.dataset.originalText = submitButton.textContent;
                submitButton.textContent = 'AI処理中...';
                submitButton.classList.add('opacity-60', 'cursor-not-allowed');
            }
        });
    });

    // リンククリック時
    const loadingLinks = document.querySelectorAll('a[data-ai-loading-link="true"]');

    loadingLinks.forEach(function (link) {
        link.addEventListener('click', function () {
            const loadingType = link.dataset.aiLoadingType || 'question';

            showAiLoadingModal(loadingType);
        });
    });
});
</script>
