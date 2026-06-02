<!DOCTYPE html>
<html lang="ja">
@include('layouts.admin.head')

<body class="min-h-screen bg-slate-50 text-slate-900 antialiased">
    @include('layouts.admin.layout')
    {{-- AI処理中モーダル --}}
<div
id="aiLoadingModal"
class="fixed inset-0 z-50 hidden items-center justify-center bg-black bg-opacity-50"
>
<div class="w-full max-w-md rounded-lg bg-white p-6 shadow-lg">
    <div class="mb-4 text-center">
        <div class="mx-auto mb-4 h-12 w-12 animate-spin rounded-full border-4 border-blue-200 border-t-blue-600"></div>

        <h2 id="aiLoadingTitle" class="text-xl font-bold">
            AI処理中です
        </h2>

        <p id="aiLoadingMessage" class="mt-2 text-sm text-gray-600">
            少し時間がかかる場合があります。このままお待ちください。
        </p>
    </div>

    <div class="mb-3">
        <div class="mb-1 flex justify-between text-sm text-gray-600">
            <span>進行状況</span>
            <span id="aiLoadingPercent">0%</span>
        </div>

        <div class="h-3 w-full rounded-full bg-gray-200">
            <div
                id="aiLoadingBar"
                class="h-3 rounded-full bg-blue-600 transition-all duration-500"
                style="width: 0%;"
            ></div>
        </div>
    </div>

    <div class="text-center text-sm text-gray-500">
        経過時間：<span id="aiLoadingSeconds">0</span>秒
    </div>

    <p class="mt-4 text-center text-xs text-gray-400">
        ※ AIの応答状況により時間が前後します。
    </p>
</div>
</div>

<script>
function showAiLoadingModal(title, message) {
    const modal = document.getElementById('aiLoadingModal');
    const titleElement = document.getElementById('aiLoadingTitle');
    const messageElement = document.getElementById('aiLoadingMessage');
    const percentElement = document.getElementById('aiLoadingPercent');
    const barElement = document.getElementById('aiLoadingBar');
    const secondsElement = document.getElementById('aiLoadingSeconds');

    if (!modal || !titleElement || !messageElement || !percentElement || !barElement || !secondsElement) {
        return;
    }

    titleElement.textContent = title || 'AI処理中です';
    messageElement.textContent = message || '少し時間がかかる場合があります。このままお待ちください。';

    let seconds = 0;
    let percent = 0;

    percentElement.textContent = '0%';
    barElement.style.width = '0%';
    secondsElement.textContent = '0';

    modal.classList.remove('hidden');
    modal.classList.add('flex');

    const timer = setInterval(function () {
        seconds++;
        secondsElement.textContent = seconds;

        // 本当の進行率ではなく、待ち時間を分かりやすくするための疑似進行率
        if (percent < 90) {
            if (seconds <= 5) {
                percent += 8;
            } else if (seconds <= 15) {
                percent += 4;
            } else {
                percent += 1;
            }
        }

        if (percent > 90) {
            percent = 90;
        }

        percentElement.textContent = percent + '%';
        barElement.style.width = percent + '%';
    }, 1000);

    // ページ遷移後は自動的に消えるため、clearIntervalは基本不要
    window.aiLoadingTimer = timer;
}

document.addEventListener('DOMContentLoaded', function () {
    const aiQuestionLinks = document.querySelectorAll('[data-ai-question-loading]');
    const aiScoringForms = document.querySelectorAll('[data-ai-scoring-form]');

    aiQuestionLinks.forEach(function (link) {
        link.addEventListener('click', function () {
            showAiLoadingModal(
                'AIが問題を作成中です',
                '本日のトレーニング問題をAIが作成しています。10〜30秒ほどかかる場合があります。'
            );
        });
    });

    aiScoringForms.forEach(function (form) {
        form.addEventListener('submit', function () {
            const submitButton = form.querySelector('button[type="submit"]');

            if (submitButton) {
                submitButton.disabled = true;
                submitButton.textContent = 'AI採点中...';
            }

            showAiLoadingModal(
                'AIが採点中です',
                'あなたの回答をAIが採点しています。完了までこのままお待ちください。'
            );
        });
    });
});
</script>

    @stack('scripts')
</body>
</html>
