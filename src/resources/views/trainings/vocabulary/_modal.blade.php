@php
    $authUser = auth()->user();

    /*
    |--------------------------------------------------------------------------
    | ボキャブラリー表示許可
    |--------------------------------------------------------------------------
    | 読書振り返りが表示されているユーザーと同じ条件で表示します。
    | さらに services.vocabulary.allowed_emails / services.allowed_emails も補助的に見ます。
    */
    $vocabularyAllowedEmails = collect()
        ->merge(config('services.reading_reflection.allowed_emails', []))
        ->merge(config('services.vocabulary.allowed_emails', []))
        ->merge(config('services.allowed_emails', []))
        ->map(fn ($email) => strtolower(trim((string) $email)))
        ->filter()
        ->unique()
        ->values();

    $loginEmail = strtolower(trim((string) ($authUser?->email ?? '')));

    $canUseVocabulary = $authUser
        && $loginEmail !== ''
        && $vocabularyAllowedEmails->contains($loginEmail);
@endphp

@if ($canUseVocabulary)
    <div class="mx-auto w-full max-w-[1440px] px-3 pt-4 md:px-8">
        <div class="rounded-3xl border border-lime-100 bg-gradient-to-br from-lime-50 via-white to-emerald-50 p-4 shadow-sm sm:p-5">
            <div class="flex items-start justify-between gap-3">
                <div>
                    <p class="text-xs font-bold text-emerald-600">
                        限定トレーニング
                    </p>

                    <h2 class="mt-1 text-lg font-bold text-slate-900">
                        ボキャブラリートレーニング
                    </h2>

                    <p class="mt-2 text-sm leading-6 text-slate-600">
                        読書で調べた言葉・意味・例文を登録し、あとからAI採点で復習できます。
                    </p>
                </div>

                <div class="flex h-11 w-11 shrink-0 items-center justify-center rounded-2xl bg-lime-100 text-xl">
                    📚
                </div>
            </div>

            <div class="mt-4 grid gap-3 sm:flex sm:items-center">
                <button
                    type="button"
                    data-vocabulary-open
                    class="w-full rounded-2xl bg-slate-900 px-5 py-3 text-center text-sm font-bold text-white shadow-sm transition hover:bg-slate-700 sm:w-auto"
                >
                    言葉を登録する
                </button>

                <a
                    href="{{ route('trainings.vocabulary.index') }}"
                    class="w-full rounded-2xl border border-slate-200 bg-white px-5 py-3 text-center text-sm font-bold text-slate-700 transition hover:bg-slate-50 sm:w-auto"
                >
                    一覧を見る
                </a>

                <a
                    href="{{ route('trainings.vocabulary.review') }}"
                    class="w-full rounded-2xl border border-emerald-200 bg-emerald-50 px-5 py-3 text-center text-sm font-bold text-emerald-700 transition hover:bg-emerald-100 sm:w-auto"
                >
                    復習する
                </a>
            </div>
        </div>
    </div>

    <div
        data-vocabulary-modal
        class="fixed inset-0 z-50 hidden items-end justify-center bg-slate-950/50 px-3 pb-0 sm:items-center sm:p-6"
    >
        <div class="max-h-[92vh] w-full max-w-lg overflow-y-auto rounded-t-3xl bg-white p-5 shadow-2xl sm:rounded-3xl sm:p-6">
            <div class="flex items-start justify-between gap-4">
                <div>
                    <p class="text-xs font-bold text-emerald-600">
                        ボキャブラリー
                    </p>

                    <h3 class="mt-1 text-xl font-bold text-slate-900">
                        言葉を登録する
                    </h3>

                    <p class="mt-2 text-sm leading-6 text-slate-600">
                        読書や学習で調べた言葉を、意味・例文と一緒に残してください。
                    </p>
                </div>

                <button
                    type="button"
                    data-vocabulary-close
                    class="flex h-10 w-10 shrink-0 items-center justify-center rounded-full bg-slate-100 text-xl text-slate-500"
                    aria-label="閉じる"
                >
                    ×
                </button>
            </div>

            <form
                method="POST"
                action="{{ route('trainings.vocabulary.store') }}"
                class="mt-5 space-y-4"
                data-vocabulary-form
            >
                @csrf

                <input type="hidden" name="vocabulary_modal" value="1">

                <div>
                    <label for="vocabulary_word" class="text-sm font-bold text-slate-700">
                        言葉
                    </label>

                    <input
                        id="vocabulary_word"
                        type="text"
                        name="word"
                        value="{{ old('word') }}"
                        placeholder="例：示唆"
                        class="mt-2 w-full rounded-2xl border border-slate-200 px-4 py-3 text-base outline-none focus:border-emerald-400 focus:ring-2 focus:ring-emerald-100"
                    >

                    @error('word')
                        <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="vocabulary_meaning" class="text-sm font-bold text-slate-700">
                        意味
                    </label>

                    <textarea
                        id="vocabulary_meaning"
                        name="meaning"
                        rows="4"
                        placeholder="例：物事を直接言わず、それとなく気づかせること。"
                        class="mt-2 w-full resize-none rounded-2xl border border-slate-200 px-4 py-3 text-base leading-7 outline-none focus:border-emerald-400 focus:ring-2 focus:ring-emerald-100"
                    >{{ old('meaning') }}</textarea>

                    @error('meaning')
                        <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="vocabulary_example_sentence" class="text-sm font-bold text-slate-700">
                        例文
                    </label>

                    <textarea
                        id="vocabulary_example_sentence"
                        name="example_sentence"
                        rows="4"
                        placeholder="例：この結果は、今後の学習方法を見直す必要があることを示唆している。"
                        class="mt-2 w-full resize-none rounded-2xl border border-slate-200 px-4 py-3 text-base leading-7 outline-none focus:border-emerald-400 focus:ring-2 focus:ring-emerald-100"
                    >{{ old('example_sentence') }}</textarea>

                    @error('example_sentence')
                        <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                    @enderror
                </div>

                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label for="vocabulary_category" class="text-sm font-bold text-slate-700">
                            カテゴリー
                            <span class="font-normal text-slate-400">任意</span>
                        </label>

                        <input
                            id="vocabulary_category"
                            type="text"
                            name="category"
                            value="{{ old('category') }}"
                            placeholder="読書"
                            class="mt-2 w-full rounded-2xl border border-slate-200 px-4 py-3 text-base outline-none focus:border-emerald-400 focus:ring-2 focus:ring-emerald-100"
                        >

                        @error('category')
                            <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="vocabulary_importance" class="text-sm font-bold text-slate-700">
                            重要度
                        </label>

                        <select
                            id="vocabulary_importance"
                            name="importance"
                            class="mt-2 w-full rounded-2xl border border-slate-200 px-4 py-3 text-base outline-none focus:border-emerald-400 focus:ring-2 focus:ring-emerald-100"
                        >
                            @for ($i = 1; $i <= 5; $i++)
                                <option value="{{ $i }}" @selected((int) old('importance', 3) === $i)>
                                    {{ $i }}
                                </option>
                            @endfor
                        </select>

                        @error('importance')
                            <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <div>
                    <label for="vocabulary_source" class="text-sm font-bold text-slate-700">
                        出典
                        <span class="font-normal text-slate-400">任意</span>
                    </label>

                    <input
                        id="vocabulary_source"
                        type="text"
                        name="source"
                        value="{{ old('source') }}"
                        placeholder="例：本のタイトル、記事名など"
                        class="mt-2 w-full rounded-2xl border border-slate-200 px-4 py-3 text-base outline-none focus:border-emerald-400 focus:ring-2 focus:ring-emerald-100"
                    >

                    @error('source')
                        <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="vocabulary_memo" class="text-sm font-bold text-slate-700">
                        メモ
                        <span class="font-normal text-slate-400">任意</span>
                    </label>

                    <textarea
                        id="vocabulary_memo"
                        name="memo"
                        rows="3"
                        placeholder="覚えたい理由や補足メモ"
                        class="mt-2 w-full resize-none rounded-2xl border border-slate-200 px-4 py-3 text-base leading-7 outline-none focus:border-emerald-400 focus:ring-2 focus:ring-emerald-100"
                    >{{ old('memo') }}</textarea>

                    @error('memo')
                        <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                    @enderror
                </div>

                <label class="flex items-center gap-3 rounded-2xl bg-slate-50 px-4 py-3">
                    <input
                        type="checkbox"
                        name="is_review_target"
                        value="1"
                        class="h-5 w-5 rounded border-slate-300"
                        @checked(old('is_review_target', true))
                    >

                    <span class="text-sm font-bold text-slate-700">
                        復習対象にする
                    </span>
                </label>

                <div class="flex gap-3 pt-2">
                    <button
                        type="button"
                        data-vocabulary-close
                        class="w-1/3 rounded-2xl border border-slate-200 bg-white px-4 py-3 text-sm font-bold text-slate-600"
                    >
                        閉じる
                    </button>

                    <button
                        type="submit"
                        data-vocabulary-submit
                        class="w-2/3 rounded-2xl bg-emerald-500 px-4 py-3 text-sm font-bold text-white shadow-sm transition hover:bg-emerald-600"
                    >
                        登録する
                    </button>
                </div>
            </form>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const openButton = document.querySelector('[data-vocabulary-open]');
            const modal = document.querySelector('[data-vocabulary-modal]');
            const closeButtons = document.querySelectorAll('[data-vocabulary-close]');
            const form = document.querySelector('[data-vocabulary-form]');
            const submitButton = document.querySelector('[data-vocabulary-submit]');

            if (!openButton || !modal) {
                return;
            }

            const openModal = () => {
                modal.classList.remove('hidden');
                modal.classList.add('flex');
                document.body.classList.add('overflow-hidden');

                const input = document.querySelector('#vocabulary_word');

                if (input) {
                    setTimeout(() => input.focus(), 100);
                }
            };

            const closeModal = () => {
                modal.classList.add('hidden');
                modal.classList.remove('flex');
                document.body.classList.remove('overflow-hidden');
            };

            openButton.addEventListener('click', openModal);

            closeButtons.forEach((button) => {
                button.addEventListener('click', closeModal);
            });

            modal.addEventListener('click', (event) => {
                if (event.target === modal) {
                    closeModal();
                }
            });

            document.addEventListener('keydown', (event) => {
                if (event.key === 'Escape') {
                    closeModal();
                }
            });

            if (form && submitButton) {
                form.addEventListener('submit', () => {
                    submitButton.disabled = true;
                    submitButton.textContent = '登録中...';
                    submitButton.classList.add('opacity-70');
                });
            }

            @if (old('vocabulary_modal') || $errors->has('word') || $errors->has('meaning') || $errors->has('example_sentence') || $errors->has('category') || $errors->has('importance') || $errors->has('source') || $errors->has('memo'))
                openModal();
            @endif
        });
    </script>
@endif
