@guest
    <div
        id="guest-onboarding-modal"
        class="fixed inset-0 z-[9999] hidden items-center justify-center bg-slate-950/55 px-4 py-5"
        aria-hidden="true"
    >
        <div
            class="w-full max-w-[960px] overflow-hidden rounded-[28px] bg-white shadow-2xl md:max-w-[980px]"
            role="dialog"
            aria-modal="true"
        >
            {{-- 画像エリア --}}
            <div class="bg-white px-3 pt-4 md:px-6 md:pt-6">
                <img
                    id="guest-onboarding-image"
                    src="{{ asset('images/steps/step1_pc.png') }}?v=20260604"
                    alt="MokuMoku Matchの使い方紹介"
                    class="mx-auto block max-h-[72vh] w-full rounded-2xl object-contain md:max-h-[74vh]"
                >
            </div>

            {{-- ボタンエリア --}}
            <div class="bg-white px-5 pb-5 pt-4 md:px-8 md:pb-6">
                {{-- step1 / step2 用 --}}
                <div id="guest-onboarding-normal-actions" class="flex items-center justify-between gap-3">
                    <button
                        type="button"
                        id="guest-onboarding-back"
                        class="hidden h-12 min-w-[112px] rounded-2xl border border-blue-200 bg-white px-6 text-base font-bold text-blue-600 transition hover:bg-blue-50"
                    >
                        戻る
                    </button>

                    <button
                        type="button"
                        id="guest-onboarding-skip"
                        class="h-12 min-w-[140px] rounded-2xl bg-white px-4 text-sm font-bold text-blue-600 transition hover:bg-blue-50 md:px-6 md:text-base"
                    >
                        今後表示しない
                    </button>

                    <button
                        type="button"
                        id="guest-onboarding-next"
                        class="ml-auto h-12 min-w-[140px] rounded-2xl bg-blue-600 px-6 text-base font-bold text-white shadow-lg shadow-blue-600/20 transition hover:bg-blue-700 md:min-w-[260px] md:px-8"
                    >
                        次へ
                    </button>
                </div>

                {{-- step3 用 --}}
                <div id="guest-onboarding-final-actions" class="hidden">
                    <div class="grid grid-cols-1 gap-3 md:grid-cols-[1fr_2fr]">
                        <a
                            href="{{ route('login') }}"
                            class="flex h-12 items-center justify-center rounded-2xl border border-blue-200 bg-white px-6 text-base font-bold text-blue-600 transition hover:bg-blue-50"
                        >
                            ログイン
                        </a>

                        <a
                            href="{{ route('register') }}"
                            class="flex h-12 items-center justify-center rounded-2xl bg-blue-600 px-6 text-base font-bold text-white shadow-lg shadow-blue-600/20 transition hover:bg-blue-700"
                        >
                            無料で登録する
                        </a>
                    </div>

                    <button
                        type="button"
                        id="guest-onboarding-later"
                        class="mx-auto mt-3 block rounded-xl px-5 py-2 text-sm font-bold text-blue-600 transition hover:bg-blue-50"
                    >
                        あとで見る
                    </button>
                </div>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const modal = document.getElementById('guest-onboarding-modal');
            const image = document.getElementById('guest-onboarding-image');

            const backButton = document.getElementById('guest-onboarding-back');
            const skipButton = document.getElementById('guest-onboarding-skip');
            const nextButton = document.getElementById('guest-onboarding-next');
            const laterButton = document.getElementById('guest-onboarding-later');

            const normalActions = document.getElementById('guest-onboarding-normal-actions');
            const finalActions = document.getElementById('guest-onboarding-final-actions');

            if (!modal || !image) {
                return;
            }

            const storageKey = 'mokumoku_intro_modal_seen';

            const steps = [
                {
                    pc: '{{ asset('images/steps/step1_pc.png') }}?v=20260604',
                    sp: '{{ asset('images/steps/step1_sp.png') }}?v=20260604',
                    alt: 'MokuMoku Matchでは募集から作業・勉強仲間を見つけられます',
                },
                {
                    pc: '{{ asset('images/steps/step2_pc.png') }}?v=20260604',
                    sp: '{{ asset('images/steps/step2_sp.png') }}?v=20260604',
                    alt: 'MokuMoku Matchではトレーニングで文章力と思考力を伸ばせます',
                },
                {
                    pc: '{{ asset('images/steps/step3_pc.png') }}?v=20260604',
                    sp: '{{ asset('images/steps/step3_sp.png') }}?v=20260604',
                    alt: 'MokuMoku Matchは無料登録すると募集作成、参加申請、メッセージ、履歴保存が使えます',
                },
            ];

            let currentStep = 0;

            const isMobile = () => window.matchMedia('(max-width: 767px)').matches;

            const updateModal = () => {
                const step = steps[currentStep];

                image.src = isMobile() ? step.sp : step.pc;
                image.alt = step.alt;

                if (currentStep === 0) {
                    backButton.classList.add('hidden');
                    skipButton.classList.remove('hidden');
                    normalActions.classList.remove('hidden');
                    finalActions.classList.add('hidden');
                    return;
                }

                if (currentStep === 1) {
                    backButton.classList.remove('hidden');
                    skipButton.classList.add('hidden');
                    normalActions.classList.remove('hidden');
                    finalActions.classList.add('hidden');
                    return;
                }

                normalActions.classList.add('hidden');
                finalActions.classList.remove('hidden');
            };

            const markAsSeen = () => {
                localStorage.setItem(storageKey, 'true');
            };

            const openModal = () => {
                modal.classList.remove('hidden');
                modal.classList.add('flex');
                modal.setAttribute('aria-hidden', 'false');
                document.body.classList.add('overflow-hidden');

                currentStep = 0;
                updateModal();
            };

            const hideModal = () => {
                modal.classList.add('hidden');
                modal.classList.remove('flex');
                modal.setAttribute('aria-hidden', 'true');
                document.body.classList.remove('overflow-hidden');
            };

            /**
             * 今後表示しない
             *
             * localStorageに保存するため、同じブラウザでは次回以降表示しない。
             */
            const closeModalAndMarkAsSeen = () => {
                markAsSeen();
                hideModal();
            };

            /**
             * あとで見る
             *
             * localStorageには保存しない。
             * そのため、次回ホーム画面へアクセスしたときに再表示される。
             */
            const closeModalOnly = () => {
                hideModal();
            };

            if (localStorage.getItem(storageKey) !== 'true') {
                openModal();
            }

            nextButton?.addEventListener('click', () => {
                if (currentStep < steps.length - 1) {
                    currentStep += 1;
                    updateModal();
                }
            });

            backButton?.addEventListener('click', () => {
                if (currentStep > 0) {
                    currentStep -= 1;
                    updateModal();
                }
            });

            /*
             * 「今後表示しない」は、表示済みとして保存する。
             */
            skipButton?.addEventListener('click', closeModalAndMarkAsSeen);

            /*
             * 「あとで見る」は、今回は閉じるだけ。
             * 次回ホームアクセス時にまた表示する。
             */
            laterButton?.addEventListener('click', closeModalOnly);

            /*
             * 背景クリックは「あとで見る」と同じ扱い。
             * 誤って背景を押しただけで今後表示されなくなるのを防ぐ。
             */
            modal.addEventListener('click', (event) => {
                if (event.target === modal) {
                    closeModalOnly();
                }
            });

            /*
             * Escキーも「あとで見る」と同じ扱い。
             */
            document.addEventListener('keydown', (event) => {
                if (event.key === 'Escape' && !modal.classList.contains('hidden')) {
                    closeModalOnly();
                }
            });

            /*
             * PC/SPの画面幅が変わった場合、現在のステップのまま画像だけ切り替える。
             */
            window.addEventListener('resize', updateModal);
        });
    </script>
@endguest
