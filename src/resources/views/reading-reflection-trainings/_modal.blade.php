@php
    use App\Models\UserReadingReflectionTraining;

    $authUser = auth()->user();

    $readingReflectionAllowedEmails = collect(config('services.reading_reflection.allowed_emails', []))
        ->map(fn ($email) => strtolower(trim($email)))
        ->filter();

    $canUseReadingReflection = $authUser
        && $readingReflectionAllowedEmails->contains(strtolower($authUser->email));

    $todayReadingReflection = null;

    if ($canUseReadingReflection) {
        $todayReadingReflection = UserReadingReflectionTraining::query()
            ->where('user_id', $authUser->id)
            ->whereDate('read_on', now()->toDateString())
            ->first();
    }
@endphp

@if ($canUseReadingReflection)
    <div class="rounded-3xl border border-amber-100 bg-gradient-to-br from-amber-50 via-white to-orange-50 p-4 shadow-sm sm:p-5">
        <div class="flex items-start justify-between gap-3">
            <div>
                <p class="text-xs font-bold text-amber-600">限定トレーニング</p>
                <h2 class="mt-1 text-lg font-bold text-slate-900">
                    10分読書の振り返り
                </h2>
                <p class="mt-2 text-sm leading-6 text-slate-600">
                    寝る前の読書で感じたこと、学んだこと、自分なりの解釈を短く残せます。
                </p>
            </div>

            <div class="flex h-11 w-11 shrink-0 items-center justify-center rounded-2xl bg-amber-100 text-xl">
                📚
            </div>
        </div>

        @if ($todayReadingReflection)
            <div class="mt-4 rounded-2xl border border-emerald-100 bg-emerald-50 px-4 py-3 text-sm text-emerald-700">
                今日の読書振り返りは保存済みです。もう一度保存すると今日の内容を更新します。
            </div>
        @endif

        <div class="mt-4 flex flex-col gap-3 sm:flex-row sm:items-center">
            <button
                type="button"
                data-reading-reflection-open
                class="w-full rounded-2xl bg-slate-900 px-5 py-3 text-center text-sm font-bold text-white shadow-sm transition hover:bg-slate-700 sm:w-auto"
            >
                読書を振り返る
            </button>

            <a
                href="{{ route('reading-reflections.index') }}"
                class="w-full rounded-2xl border border-slate-200 bg-white px-5 py-3 text-center text-sm font-bold text-slate-700 transition hover:bg-slate-50 sm:w-auto"
            >
                履歴を見る
            </a>
        </div>
    </div>

    <div
        data-reading-reflection-modal
        class="fixed inset-0 z-50 hidden items-end justify-center bg-slate-950/50 px-3 pb-0 sm:items-center sm:p-6"
    >
        <div class="w-full max-w-lg rounded-t-3xl bg-white p-5 shadow-2xl sm:rounded-3xl sm:p-6">
            <div class="flex items-start justify-between gap-4">
                <div>
                    <p class="text-xs font-bold text-amber-600">10分読書</p>
                    <h3 class="mt-1 text-xl font-bold text-slate-900">
                        今日の読書を振り返る
                    </h3>
                    <p class="mt-2 text-sm leading-6 text-slate-600">
                        正解を書く必要はありません。自分の言葉で、感じたことを残してください。
                    </p>
                </div>

                <button
                    type="button"
                    data-reading-reflection-close
                    class="flex h-10 w-10 shrink-0 items-center justify-center rounded-full bg-slate-100 text-xl text-slate-500"
                    aria-label="閉じる"
                >
                    ×
                </button>
            </div>

            <form
                method="POST"
                action="{{ route('reading-reflections.store') }}"
                class="mt-5 space-y-4"
                data-reading-reflection-form
            >
                @csrf

                <div>
                    <label for="reading_read_on" class="text-sm font-bold text-slate-700">
                        読書日
                    </label>
                    <input
                        id="reading_read_on"
                        type="date"
                        name="read_on"
                        value="{{ old('read_on', now()->toDateString()) }}"
                        max="{{ now()->toDateString() }}"
                        class="mt-2 w-full rounded-2xl border border-slate-200 px-4 py-3 text-base outline-none focus:border-amber-400 focus:ring-2 focus:ring-amber-100"
                    >
                    @error('read_on')
                        <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="reading_book_title" class="text-sm font-bold text-slate-700">
                        本のタイトル
                        <span class="font-normal text-slate-400">任意</span>
                    </label>
                    <input
                        id="reading_book_title"
                        type="text"
                        name="book_title"
                        value="{{ old('book_title', $todayReadingReflection?->book_title) }}"
                        placeholder="例：嫌われる勇気、リーダブルコード など"
                        class="mt-2 w-full rounded-2xl border border-slate-200 px-4 py-3 text-base outline-none focus:border-amber-400 focus:ring-2 focus:ring-amber-100"
                    >
                    @error('book_title')
                        <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                    @enderror
                </div>

                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label for="reading_minutes" class="text-sm font-bold text-slate-700">
                            読書時間
                        </label>
                        <input
                            id="reading_minutes"
                            type="number"
                            name="read_minutes"
                            value="{{ old('read_minutes', $todayReadingReflection?->read_minutes ?? 10) }}"
                            min="1"
                            max="240"
                            class="mt-2 w-full rounded-2xl border border-slate-200 px-4 py-3 text-base outline-none focus:border-amber-400 focus:ring-2 focus:ring-amber-100"
                        >
                        @error('read_minutes')
                            <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="reading_mood" class="text-sm font-bold text-slate-700">
                            感覚
                        </label>
                        <select
                            id="reading_mood"
                            name="mood"
                            class="mt-2 w-full rounded-2xl border border-slate-200 px-4 py-3 text-base outline-none focus:border-amber-400 focus:ring-2 focus:ring-amber-100"
                        >
                            @php
                                $selectedMood = old('mood', $todayReadingReflection?->mood);
                            @endphp
                            <option value="">選択なし</option>
                            <option value="good" @selected($selectedMood === 'good')>よく理解できた</option>
                            <option value="normal" @selected($selectedMood === 'normal')>ふつう</option>
                            <option value="difficult" @selected($selectedMood === 'difficult')>少し難しかった</option>
                        </select>
                        @error('mood')
                            <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <div>
                    <label for="reading_reflection_text" class="text-sm font-bold text-slate-700">
                        自分なりの解釈・感想
                    </label>
                    <textarea
                        id="reading_reflection_text"
                        name="reflection_text"
                        rows="7"
                        placeholder="例：今日読んだ内容は、完璧を目指すよりも小さく続けることが大事だと感じました。自分の開発でも、最初から全部作ろうとせず、まず1つ動くものを作る意識を持ちたいです。"
                        class="mt-2 w-full resize-none rounded-2xl border border-slate-200 px-4 py-3 text-base leading-7 outline-none focus:border-amber-400 focus:ring-2 focus:ring-amber-100"
                    >{{ old('reflection_text', $todayReadingReflection?->reflection_text) }}</textarea>
                    @error('reflection_text')
                        <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                    @enderror
                </div>

                <div class="flex gap-3 pt-2">
                    <button
                        type="button"
                        data-reading-reflection-close
                        class="w-1/3 rounded-2xl border border-slate-200 bg-white px-4 py-3 text-sm font-bold text-slate-600"
                    >
                        閉じる
                    </button>

                    <button
                        type="submit"
                        data-reading-reflection-submit
                        class="w-2/3 rounded-2xl bg-amber-500 px-4 py-3 text-sm font-bold text-white shadow-sm transition hover:bg-amber-600"
                    >
                        保存する
                    </button>
                </div>
            </form>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const openButton = document.querySelector('[data-reading-reflection-open]');
            const modal = document.querySelector('[data-reading-reflection-modal]');
            const closeButtons = document.querySelectorAll('[data-reading-reflection-close]');
            const form = document.querySelector('[data-reading-reflection-form]');
            const submitButton = document.querySelector('[data-reading-reflection-submit]');

            if (!openButton || !modal) {
                return;
            }

            const openModal = () => {
                modal.classList.remove('hidden');
                modal.classList.add('flex');
                document.body.classList.add('overflow-hidden');

                const textarea = document.querySelector('#reading_reflection_text');
                if (textarea) {
                    setTimeout(() => textarea.focus(), 100);
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
                    submitButton.textContent = '保存中...';
                    submitButton.classList.add('opacity-70');
                });
            }

            @if ($errors->has('read_on') || $errors->has('book_title') || $errors->has('read_minutes') || $errors->has('mood') || $errors->has('reflection_text'))
                openModal();
            @endif
        });
    </script>
@endif
