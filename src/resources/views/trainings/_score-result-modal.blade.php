@php
    $modalTotalScore = $training->total_score;
    $modalEarnedPoints = $training->earned_points ?? 0;

    $modalGoodPoint = trim($training->good_point ?? '');
    $modalImprovementPoint = trim($training->improvement_point ?? '');
    $modalNextTask = trim($training->next_task ?? '');

    $scoreTitle = 'よくできました！';
    $scoreMessage = '今日も一歩、成長しました！';

    if ($modalTotalScore !== null) {
        if ($modalTotalScore >= 90) {
            $scoreTitle = 'すばらしいです！';
            $scoreMessage = 'とても完成度の高い内容でした！';
        } elseif ($modalTotalScore >= 80) {
            $scoreTitle = 'よくできました！';
            $scoreMessage = '具体的に書けていて、とても読みやすいです！';
        } elseif ($modalTotalScore >= 70) {
            $scoreTitle = 'いい感じです！';
            $scoreMessage = 'しっかり考えて書けています！';
        } elseif ($modalTotalScore >= 60) {
            $scoreTitle = 'ナイス挑戦です！';
            $scoreMessage = '続けることで、さらに伸びていきます！';
        } else {
            $scoreTitle = '挑戦できました！';
            $scoreMessage = 'まず取り組めたことが大きな一歩です！';
        }
    }

    $shouldShowScoreModal = session('show_score_modal') || session('success');
@endphp

@if ($shouldShowScoreModal && $modalTotalScore !== null)
    <div
        id="scoreResultModal"
        class="fixed inset-0 z-[9999] flex items-center justify-center bg-slate-950/55 px-4 py-6 backdrop-blur-sm"
        aria-modal="true"
        role="dialog"
    >
        <div class="relative max-h-[92vh] w-full max-w-md overflow-y-auto rounded-[2rem] bg-white px-5 pb-5 pt-8 shadow-2xl md:max-w-lg md:px-7 md:pb-7 md:pt-10">
            {{-- 閉じる --}}
            <button
                type="button"
                onclick="closeScoreResultModal()"
                class="absolute right-5 top-5 flex h-9 w-9 items-center justify-center rounded-full text-2xl leading-none text-slate-400 transition hover:bg-slate-100 hover:text-slate-600"
                aria-label="閉じる"
            >
                ×
            </button>

            {{-- 装飾 --}}
            <div class="pointer-events-none absolute left-6 top-9 text-lg text-pink-300">■</div>
            <div class="pointer-events-none absolute left-12 top-24 text-xl text-yellow-300">✦</div>
            <div class="pointer-events-none absolute right-16 top-12 text-lg text-blue-300">●</div>
            <div class="pointer-events-none absolute right-8 top-28 text-xl text-yellow-300">✦</div>
            <div class="pointer-events-none absolute left-8 top-48 text-xl text-yellow-300">✦</div>
            <div class="pointer-events-none absolute right-10 top-56 text-sm text-pink-300">■</div>

            {{-- トロフィー --}}
            <div class="mx-auto -mt-2 flex h-24 w-24 items-center justify-center rounded-full bg-gradient-to-b from-yellow-50 to-white text-6xl shadow-sm md:h-28 md:w-28 md:text-7xl">
                🏆
            </div>

            {{-- AI採点完了 --}}
            <div class="mt-2 text-center">
                <span class="inline-flex items-center rounded-full bg-blue-50 px-4 py-1 text-sm font-black text-blue-700">
                    ✦ AI採点完了 ✦
                </span>
            </div>

            {{-- 点数 --}}
            <div class="mt-3 text-center">
                <h2 class="text-3xl font-black tracking-wide text-slate-900 md:text-4xl">
                    {{ $scoreTitle }}
                </h2>

                <div class="mt-3 flex items-end justify-center gap-1">
                    <span class="bg-gradient-to-b from-blue-500 to-blue-700 bg-clip-text text-8xl font-black leading-none text-transparent md:text-9xl">
                        {{ $modalTotalScore }}
                    </span>
                    <span class="mb-3 text-2xl font-black text-slate-900 md:mb-4 md:text-3xl">
                        点
                    </span>
                </div>

                <div class="mt-3 inline-flex items-center gap-2 rounded-full bg-gradient-to-r from-yellow-300 to-yellow-400 px-6 py-2 text-lg font-black text-yellow-900 shadow-sm">
                    <span>⭐</span>
                    <span>+{{ $modalEarnedPoints }}pt 獲得</span>
                    <span>✦</span>
                </div>

                <p class="mt-3 text-sm font-black text-slate-700">
                    🔥 今日も成長しました！
                </p>
            </div>

            {{-- 褒めメッセージ --}}
            <div class="mt-5 flex items-center gap-3 rounded-3xl border border-blue-100 bg-blue-50 px-4 py-4">
                <div class="flex h-12 w-12 shrink-0 items-center justify-center rounded-full bg-white text-2xl shadow-sm">
                    😊
                </div>

                <p class="text-base font-black leading-relaxed text-blue-800">
                    {{ $scoreMessage }}
                </p>
            </div>

            {{-- 今回のよかった点 --}}
            <div class="mt-5 rounded-3xl bg-white p-4 shadow-[0_8px_24px_rgba(15,23,42,0.08)] ring-1 ring-slate-100">
                <div class="mb-2 flex items-center gap-2">
                    <span class="text-xl text-blue-600">★</span>
                    <h3 class="text-base font-black text-blue-700">
                        今回のよかった点
                    </h3>
                </div>

                <p class="whitespace-pre-wrap text-sm leading-7 text-slate-700">
                    {{ $modalGoodPoint ?: '最後まで取り組めていて、とても良いです。' }}
                </p>
            </div>

            {{-- 次回のコツ --}}
            <div class="mt-4 rounded-3xl bg-white p-4 shadow-[0_8px_24px_rgba(15,23,42,0.08)] ring-1 ring-slate-100">
                <div class="mb-2 flex items-center gap-2">
                    <span class="text-xl text-blue-600">💡</span>
                    <h3 class="text-base font-black text-blue-700">
                        次回のコツ
                    </h3>
                </div>

                <p class="whitespace-pre-wrap text-sm leading-7 text-slate-700">
                    {{ $modalImprovementPoint ?: ($modalNextTask ?: '次回は、理由や具体例を少し足すとさらに良くなります。') }}
                </p>
            </div>

            {{-- ボタン --}}
            <div class="mt-6 space-y-3">
                <button
                    type="button"
                    onclick="viewScoreResult()"
                    class="w-full rounded-2xl bg-blue-600 px-5 py-4 text-center text-base font-black text-white shadow-lg shadow-blue-200 transition hover:bg-blue-700 active:scale-[0.99]"
                >
                    ✨ 結果を見る
                </button>

                <button
                    type="button"
                    onclick="closeScoreResultModal()"
                    class="w-full rounded-2xl border border-slate-200 bg-slate-50 px-5 py-4 text-center text-base font-black text-blue-700 transition hover:bg-slate-100 active:scale-[0.99]"
                >
                    閉じる
                </button>
            </div>
        </div>
    </div>

    <script>
        function closeScoreResultModal() {
            const modal = document.getElementById('scoreResultModal');

            if (!modal) {
                return;
            }

            modal.classList.add('hidden');
        }

        function viewScoreResult() {
            closeScoreResultModal();

            const scoreSection = document.getElementById('scoreResultSection');

            if (scoreSection) {
                scoreSection.scrollIntoView({
                    behavior: 'smooth',
                    block: 'start'
                });
            }
        }

        document.addEventListener('keydown', function (event) {
            if (event.key === 'Escape') {
                closeScoreResultModal();
            }
        });
    </script>
@endif
