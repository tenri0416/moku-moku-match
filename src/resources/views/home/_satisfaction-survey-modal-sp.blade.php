<div
    id="satisfaction-survey-modal-sp"
    class="fixed inset-0 z-[9998] flex items-center justify-center overflow-y-auto bg-slate-950/60 px-4 py-5 md:hidden"
    aria-hidden="false"
>
    <div
        class="relative my-auto w-full max-w-[430px] rounded-[26px] bg-white px-5 py-6 shadow-2xl"
        role="dialog"
        aria-modal="true"
    >
        <button
            type="button"
            data-satisfaction-survey-close
            class="absolute right-4 top-4 flex h-10 w-10 items-center justify-center rounded-full bg-slate-100 text-2xl font-bold leading-none text-slate-500 transition hover:bg-slate-200 hover:text-slate-700"
            aria-label="アンケートを閉じる"
        >
            ×
        </button>

        <form method="POST" action="{{ route('satisfaction-surveys.store') }}">
            @csrf

            <div class="pr-10 text-center">
                <h2 class="text-xl font-extrabold leading-relaxed text-slate-900">
                    MokuMoku Matchの<br>
                    使い心地を教えてください
                </h2>
            </div>

            <p class="mt-3 text-center text-sm font-medium leading-relaxed text-slate-500">
                サービス改善のため、かんたんなアンケートに<br>
                ご協力ください。1分ほどで回答できます。
            </p>

            <div class="mt-6 rounded-2xl border border-slate-200 bg-white p-4 shadow-sm">
                <div class="flex items-start gap-3">
                    <span class="flex h-7 w-7 shrink-0 items-center justify-center rounded-full bg-emerald-500 text-sm font-bold text-white">
                        1
                    </span>
                    <div>
                        <h3 class="text-lg font-extrabold text-slate-900">満足度</h3>
                        <p class="mt-1 text-sm font-medium text-slate-500">現在の満足度を教えてください</p>
                    </div>
                </div>

                <input type="hidden" name="satisfaction" id="satisfaction-survey-rating-sp" value="">

                <div class="mt-5 flex justify-center gap-2" data-satisfaction-rating-group="sp">
                    @for ($i = 1; $i <= 5; $i++)
                        <button
                            type="button"
                            data-satisfaction-rating="{{ $i }}"
                            class="text-4xl leading-none text-slate-300 transition hover:scale-110 hover:text-amber-400"
                            aria-label="{{ $i }}点"
                        >
                            ★
                        </button>
                    @endfor
                </div>

                <div class="mt-4 text-center">
                    <span
                        id="satisfaction-survey-rating-label-sp"
                        class="hidden rounded-full bg-blue-50 px-4 py-1.5 text-sm font-bold text-blue-600"
                    ></span>
                </div>

                @error('satisfaction')
                    <p class="mt-3 text-center text-sm font-bold text-red-500">{{ $message }}</p>
                @enderror
            </div>

            <div class="mt-4 rounded-2xl border border-slate-200 bg-white p-4 shadow-sm">
                <div class="flex items-start gap-3">
                    <span class="flex h-7 w-7 shrink-0 items-center justify-center rounded-full bg-emerald-500 text-sm font-bold text-white">
                        2
                    </span>
                    <div>
                        <h3 class="text-lg font-extrabold text-slate-900">改善してほしい点</h3>
                        <p class="mt-1 text-sm font-medium leading-relaxed text-slate-500">
                            使いにくい点や、改善してほしいことがあれば教えてください
                        </p>
                    </div>
                </div>

                <textarea
                    name="improvement_text"
                    id="satisfaction-survey-improvement-sp"
                    rows="4"
                    maxlength="500"
                    class="mt-4 w-full rounded-2xl border border-slate-200 bg-white px-4 py-3 text-sm font-medium text-slate-700 outline-none transition placeholder:text-slate-400 focus:border-blue-400 focus:ring-4 focus:ring-blue-100"
                    placeholder="例：検索条件をもっと使いやすくしてほしい"
                >{{ old('improvement_text') }}</textarea>

                <div class="mt-2 text-right text-sm font-medium text-slate-400">
                    <span id="satisfaction-survey-count-sp">0</span> / 500
                </div>

                @error('improvement_text')
                    <p class="mt-2 text-sm font-bold text-red-500">{{ $message }}</p>
                @enderror
            </div>

            <p class="mt-4 text-center text-xs font-medium leading-relaxed text-slate-500">
                ※ ご回答はサービス改善の参考にのみ利用します。
            </p>

            <div class="mt-5 space-y-3">
                <button
                    type="submit"
                    class="flex h-13 w-full items-center justify-center rounded-2xl bg-blue-600 px-6 py-4 text-base font-bold text-white shadow-lg shadow-blue-600/20 transition hover:bg-blue-700"
                >
                    送信する
                </button>

                <button
                    type="button"
                    data-satisfaction-survey-close
                    class="flex h-13 w-full items-center justify-center rounded-2xl border border-blue-200 bg-white px-6 py-4 text-base font-bold text-blue-600 transition hover:bg-blue-50"
                >
                    あとで回答する
                </button>
            </div>
        </form>

        <form method="POST" action="{{ route('satisfaction-surveys.skip') }}" class="mt-4 text-center">
            @csrf
            <button
                type="submit"
                class="rounded-xl px-5 py-2 text-sm font-bold text-blue-600 transition hover:bg-blue-50"
            >
                今月は回答しない
            </button>
        </form>
    </div>
</div>
