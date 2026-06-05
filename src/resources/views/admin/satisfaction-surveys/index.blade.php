@extends('layouts.admin')

@section('title', '満足度調査アンケート一覧')

@section('content')
<div class="min-h-screen bg-slate-50">
    <div class="mx-auto w-full max-w-7xl px-3 py-6 sm:px-6 sm:py-8 lg:px-8">
        <div class="mb-6">
            <h1 class="break-words text-2xl font-bold leading-tight text-slate-900 sm:text-3xl">
                満足度調査アンケート一覧
            </h1>
            <p class="mt-2 text-sm leading-6 text-slate-500">
                ユーザーからの満足度と改善要望を確認できます。
            </p>
        </div>

        <div class="mb-6 grid grid-cols-1 gap-3 sm:grid-cols-2 lg:grid-cols-4 lg:gap-4">
            <div class="rounded-2xl bg-white p-4 shadow-sm ring-1 ring-slate-200 sm:p-5">
                <p class="text-xs font-bold text-slate-500 sm:text-sm">総件数</p>
                <p class="mt-2 text-2xl font-extrabold text-slate-900 sm:text-3xl">{{ number_format($totalCount) }}</p>
            </div>

            <div class="rounded-2xl bg-white p-4 shadow-sm ring-1 ring-slate-200 sm:p-5">
                <p class="text-xs font-bold text-slate-500 sm:text-sm">回答済み</p>
                <p class="mt-2 text-2xl font-extrabold text-blue-600 sm:text-3xl">{{ number_format($answeredCount) }}</p>
            </div>

            <div class="rounded-2xl bg-white p-4 shadow-sm ring-1 ring-slate-200 sm:p-5">
                <p class="text-xs font-bold text-slate-500 sm:text-sm">今月は回答しない</p>
                <p class="mt-2 text-2xl font-extrabold text-slate-700 sm:text-3xl">{{ number_format($skippedCount) }}</p>
            </div>

            <div class="rounded-2xl bg-white p-4 shadow-sm ring-1 ring-slate-200 sm:p-5">
                <p class="text-xs font-bold text-slate-500 sm:text-sm">平均満足度</p>
                <p class="mt-2 text-2xl font-extrabold text-amber-500 sm:text-3xl">
                    {{ $averageSatisfaction ? number_format($averageSatisfaction, 1) : '-' }}
                </p>
            </div>
        </div>

        <form
            method="GET"
            action="{{ route('admin.satisfaction-surveys.index') }}"
            class="mb-6 rounded-2xl bg-white p-4 shadow-sm ring-1 ring-slate-200 sm:p-5"
        >
            <div class="grid grid-cols-1 gap-4 md:grid-cols-3">
                <div>
                    <label class="mb-1 block text-sm font-bold text-slate-700">状態</label>
                    <select name="status" class="w-full rounded-xl border-slate-300 text-sm">
                        <option value="">すべて</option>
                        <option value="answered" @selected(request('status') === 'answered')>回答済み</option>
                        <option value="skipped" @selected(request('status') === 'skipped')>今月は回答しない</option>
                    </select>
                </div>

                <div>
                    <label class="mb-1 block text-sm font-bold text-slate-700">満足度</label>
                    <select name="satisfaction" class="w-full rounded-xl border-slate-300 text-sm">
                        <option value="">すべて</option>
                        @for ($i = 5; $i >= 1; $i--)
                            <option value="{{ $i }}" @selected((string) request('satisfaction') === (string) $i)>
                                {{ $i }}：{{ match ($i) {
                                    5 => 'とても満足',
                                    4 => '満足',
                                    3 => '普通',
                                    2 => 'やや不満',
                                    1 => '不満',
                                    default => '',
                                } }}
                            </option>
                        @endfor
                    </select>
                </div>

                <div class="flex flex-col gap-3 md:flex-row md:items-end">
                    <button
                        type="submit"
                        class="w-full rounded-xl bg-blue-600 px-5 py-3 text-sm font-bold text-white hover:bg-blue-700 md:w-auto md:py-2.5"
                    >
                        検索
                    </button>

                    <a
                        href="{{ route('admin.satisfaction-surveys.index') }}"
                        class="flex w-full items-center justify-center rounded-xl border border-slate-300 px-5 py-3 text-sm font-bold text-slate-700 hover:bg-slate-50 md:w-auto md:py-2.5"
                    >
                        クリア
                    </a>
                </div>
            </div>
        </form>

        {{-- Mobile Cards --}}
        <div class="space-y-3 md:hidden">
            @forelse ($surveys as $survey)
                <article class="rounded-2xl bg-white p-4 shadow-sm ring-1 ring-slate-200">
                    <div class="flex items-start justify-between gap-3">
                        <div class="min-w-0">
                            <p class="text-xs font-bold text-slate-500">ID: {{ $survey->id }}</p>
                            <h2 class="mt-1 break-words text-base font-bold leading-6 text-slate-900">
                                {{ $survey->user?->name ?? '退会済みユーザー' }}
                            </h2>
                            <p class="mt-1 break-all text-xs leading-5 text-slate-500">
                                {{ $survey->user?->email ?? '-' }}
                            </p>
                        </div>

                        <div class="shrink-0">
                            @if ($survey->status === \App\Models\UserSatisfactionSurvey::STATUS_ANSWERED)
                                <span class="rounded-full bg-blue-50 px-2.5 py-1 text-[11px] font-bold text-blue-600">
                                    {{ $survey->status_label }}
                                </span>
                            @else
                                <span class="rounded-full bg-slate-100 px-2.5 py-1 text-[11px] font-bold text-slate-700">
                                    {{ $survey->status_label }}
                                </span>
                            @endif
                        </div>
                    </div>

                    <div class="mt-4 space-y-3 rounded-xl bg-slate-50 p-3 text-sm text-slate-700">
                        <div>
                            <p class="text-xs font-bold text-slate-500">満足度</p>
                            <div class="mt-1">
                                @if ($survey->satisfaction)
                                    <span class="text-amber-400">
                                        {{ str_repeat('★', $survey->satisfaction) }}
                                    </span>
                                    <span class="text-slate-300">
                                        {{ str_repeat('★', 5 - $survey->satisfaction) }}
                                    </span>
                                    <span class="ml-1 text-xs text-slate-500">
                                        {{ $survey->satisfaction_label }}
                                    </span>
                                @else
                                    -
                                @endif
                            </div>
                        </div>

                        <div>
                            <p class="text-xs font-bold text-slate-500">改善要望</p>
                            <p class="mt-1 break-words leading-6">
                                {{ \Illuminate\Support\Str::limit($survey->improvement_text ?: '-', 80) }}
                            </p>
                        </div>

                        <div>
                            <p class="text-xs font-bold text-slate-500">回答日</p>
                            <p class="mt-1">{{ $survey->created_at?->format('Y/m/d H:i') }}</p>
                        </div>
                    </div>

                    <button
                        type="button"
                        class="mt-4 flex w-full items-center justify-center rounded-xl bg-blue-600 px-4 py-3 text-sm font-bold text-white hover:bg-blue-700"
                        data-survey-modal-open
                        data-survey-id="{{ $survey->id }}"
                        data-survey-user-name="{{ $survey->user?->name ?? '退会済みユーザー' }}"
                        data-survey-user-email="{{ $survey->user?->email ?? '-' }}"
                        data-survey-status="{{ $survey->status_label }}"
                        data-survey-satisfaction="{{ $survey->satisfaction ?? '' }}"
                        data-survey-satisfaction-label="{{ $survey->satisfaction_label }}"
                        data-survey-improvement="{{ e($survey->improvement_text ?: '入力なし') }}"
                        data-survey-created-at="{{ $survey->created_at?->format('Y/m/d H:i') }}"
                        data-survey-next-display-at="{{ $survey->next_display_at?->format('Y/m/d H:i') ?? '-' }}"
                    >
                        詳細を見る
                    </button>
                </article>
            @empty
                <div class="rounded-2xl bg-white px-4 py-10 text-center text-sm text-slate-500 shadow-sm ring-1 ring-slate-200">
                    アンケートはまだありません。
                </div>
            @endforelse
        </div>

        {{-- Desktop Table --}}
        <div class="hidden overflow-hidden rounded-2xl bg-white shadow-sm ring-1 ring-slate-200 md:block">
            <div class="overflow-x-auto">
                <table class="min-w-[1080px] divide-y divide-slate-200">
                    <thead class="bg-slate-50">
                        <tr>
                            <th class="px-4 py-3 text-left text-xs font-bold uppercase text-slate-500">ID</th>
                            <th class="px-4 py-3 text-left text-xs font-bold uppercase text-slate-500">ユーザー</th>
                            <th class="px-4 py-3 text-left text-xs font-bold uppercase text-slate-500">状態</th>
                            <th class="px-4 py-3 text-left text-xs font-bold uppercase text-slate-500">満足度</th>
                            <th class="px-4 py-3 text-left text-xs font-bold uppercase text-slate-500">改善要望</th>
                            <th class="px-4 py-3 text-left text-xs font-bold uppercase text-slate-500">回答日</th>
                            <th class="px-4 py-3 text-left text-xs font-bold uppercase text-slate-500">操作</th>
                        </tr>
                    </thead>

                    <tbody class="divide-y divide-slate-100">
                        @forelse ($surveys as $survey)
                            <tr>
                                <td class="px-4 py-3 text-sm text-slate-700">
                                    {{ $survey->id }}
                                </td>

                                <td class="px-4 py-3 text-sm text-slate-700">
                                    <div class="break-words font-bold text-slate-900">
                                        {{ $survey->user?->name ?? '退会済みユーザー' }}
                                    </div>
                                    <div class="break-all text-xs text-slate-500">
                                        {{ $survey->user?->email ?? '-' }}
                                    </div>
                                </td>

                                <td class="px-4 py-3 text-sm">
                                    @if ($survey->status === \App\Models\UserSatisfactionSurvey::STATUS_ANSWERED)
                                        <span class="rounded-full bg-blue-50 px-3 py-1 text-xs font-bold text-blue-600">
                                            {{ $survey->status_label }}
                                        </span>
                                    @else
                                        <span class="rounded-full bg-slate-100 px-3 py-1 text-xs font-bold text-slate-700">
                                            {{ $survey->status_label }}
                                        </span>
                                    @endif
                                </td>

                                <td class="px-4 py-3 text-sm text-slate-700">
                                    @if ($survey->satisfaction)
                                        <span class="text-amber-400">
                                            {{ str_repeat('★', $survey->satisfaction) }}
                                        </span>
                                        <span class="text-slate-300">
                                            {{ str_repeat('★', 5 - $survey->satisfaction) }}
                                        </span>
                                        <span class="ml-1 text-xs text-slate-500">
                                            {{ $survey->satisfaction_label }}
                                        </span>
                                    @else
                                        -
                                    @endif
                                </td>

                                <td class="max-w-md px-4 py-3 text-sm text-slate-700">
                                    <span class="break-words">
                                        {{ \Illuminate\Support\Str::limit($survey->improvement_text ?: '-', 60) }}
                                    </span>
                                </td>

                                <td class="px-4 py-3 text-sm text-slate-500">
                                    {{ $survey->created_at?->format('Y/m/d H:i') }}
                                </td>

                                <td class="px-4 py-3 text-sm">
                                    <button
                                        type="button"
                                        class="font-bold text-blue-600 hover:underline"
                                        data-survey-modal-open
                                        data-survey-id="{{ $survey->id }}"
                                        data-survey-user-name="{{ $survey->user?->name ?? '退会済みユーザー' }}"
                                        data-survey-user-email="{{ $survey->user?->email ?? '-' }}"
                                        data-survey-status="{{ $survey->status_label }}"
                                        data-survey-satisfaction="{{ $survey->satisfaction ?? '' }}"
                                        data-survey-satisfaction-label="{{ $survey->satisfaction_label }}"
                                        data-survey-improvement="{{ e($survey->improvement_text ?: '入力なし') }}"
                                        data-survey-created-at="{{ $survey->created_at?->format('Y/m/d H:i') }}"
                                        data-survey-next-display-at="{{ $survey->next_display_at?->format('Y/m/d H:i') ?? '-' }}"
                                    >
                                        詳細
                                    </button>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="px-4 py-10 text-center text-sm text-slate-500">
                                    アンケートはまだありません。
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <div class="mt-6 overflow-x-auto">
            {{ $surveys->links() }}
        </div>
    </div>

    {{-- 詳細モーダル --}}
    <div
        id="survey-detail-modal"
        class="fixed inset-0 z-[9999] hidden items-center justify-center bg-slate-950/60 px-3 py-4 sm:px-4 sm:py-6"
        aria-hidden="true"
    >
        <div
            class="relative max-h-[calc(100vh-2rem)] w-full max-w-2xl overflow-y-auto rounded-2xl bg-white p-4 shadow-2xl sm:p-6"
            role="dialog"
            aria-modal="true"
        >
            <button
                type="button"
                id="survey-detail-modal-close"
                class="absolute right-3 top-3 flex h-9 w-9 items-center justify-center rounded-full bg-slate-100 text-2xl font-bold leading-none text-slate-500 hover:bg-slate-200 hover:text-slate-700 sm:right-4 sm:top-4 sm:h-10 sm:w-10"
                aria-label="閉じる"
            >
                ×
            </button>

            <div class="pr-10 sm:pr-12">
                <h2 class="text-lg font-bold text-slate-900 sm:text-xl">アンケート詳細</h2>
                <p class="mt-1 text-sm leading-6 text-slate-500">
                    ユーザーの満足度と改善要望を確認できます。
                </p>
            </div>

            <div class="mt-5 grid grid-cols-1 gap-3 sm:mt-6 sm:grid-cols-2 sm:gap-4">
                <div class="rounded-2xl bg-slate-50 p-4 ring-1 ring-slate-200">
                    <p class="text-xs font-bold text-slate-500">ID</p>
                    <p id="modal-survey-id" class="mt-1 break-words text-base font-bold text-slate-900"></p>
                </div>

                <div class="rounded-2xl bg-slate-50 p-4 ring-1 ring-slate-200">
                    <p class="text-xs font-bold text-slate-500">状態</p>
                    <p id="modal-survey-status" class="mt-1 break-words text-base font-bold text-slate-900"></p>
                </div>

                <div class="rounded-2xl bg-slate-50 p-4 ring-1 ring-slate-200">
                    <p class="text-xs font-bold text-slate-500">ユーザー</p>
                    <p id="modal-survey-user-name" class="mt-1 break-words text-base font-bold text-slate-900"></p>
                    <p id="modal-survey-user-email" class="mt-1 break-all text-xs text-slate-500"></p>
                </div>

                <div class="rounded-2xl bg-slate-50 p-4 ring-1 ring-slate-200">
                    <p class="text-xs font-bold text-slate-500">回答日</p>
                    <p id="modal-survey-created-at" class="mt-1 break-words text-base font-bold text-slate-900"></p>
                </div>

                <div class="rounded-2xl bg-slate-50 p-4 ring-1 ring-slate-200 sm:col-span-2">
                    <p class="text-xs font-bold text-slate-500">満足度</p>
                    <div class="mt-2 flex flex-wrap items-center gap-2">
                        <span id="modal-survey-stars" class="text-xl"></span>
                        <span id="modal-survey-satisfaction-label" class="text-sm font-bold text-slate-600"></span>
                    </div>
                </div>

                <div class="rounded-2xl bg-slate-50 p-4 ring-1 ring-slate-200 sm:col-span-2">
                    <p class="text-xs font-bold text-slate-500">次回表示可能日時</p>
                    <p id="modal-survey-next-display-at" class="mt-1 break-words text-base font-bold text-slate-900"></p>
                </div>
            </div>

            <div class="mt-5">
                <p class="text-sm font-bold text-slate-700">改善してほしい点</p>
                <div
                    id="modal-survey-improvement"
                    class="mt-2 max-h-[220px] overflow-y-auto whitespace-pre-wrap break-words rounded-2xl bg-slate-50 p-4 text-sm leading-7 text-slate-800 ring-1 ring-slate-200 sm:max-h-[260px]"
                ></div>
            </div>

            <div class="mt-6 text-right">
                <button
                    type="button"
                    id="survey-detail-modal-close-bottom"
                    class="w-full rounded-xl bg-slate-900 px-5 py-3 text-sm font-bold text-white hover:bg-slate-700 sm:w-auto sm:py-2.5"
                >
                    閉じる
                </button>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const modal = document.getElementById('survey-detail-modal');
            const closeButton = document.getElementById('survey-detail-modal-close');
            const closeBottomButton = document.getElementById('survey-detail-modal-close-bottom');

            const setText = (id, value) => {
                const element = document.getElementById(id);

                if (!element) {
                    return;
                }

                element.textContent = value || '-';
            };

            const paintStars = (rating) => {
                const starsElement = document.getElementById('modal-survey-stars');
                const value = Number(rating);

                if (!starsElement) {
                    return;
                }

                if (!value) {
                    starsElement.innerHTML = '<span class="text-slate-400">-</span>';
                    return;
                }

                let html = '';

                for (let i = 1; i <= 5; i++) {
                    html += `<span class="${i <= value ? 'text-amber-400' : 'text-slate-300'}">★</span>`;
                }

                starsElement.innerHTML = html;
            };

            const openModal = (button) => {
                setText('modal-survey-id', button.dataset.surveyId);
                setText('modal-survey-status', button.dataset.surveyStatus);
                setText('modal-survey-user-name', button.dataset.surveyUserName);
                setText('modal-survey-user-email', button.dataset.surveyUserEmail);
                setText('modal-survey-created-at', button.dataset.surveyCreatedAt);
                setText('modal-survey-next-display-at', button.dataset.surveyNextDisplayAt);
                setText('modal-survey-satisfaction-label', button.dataset.surveySatisfactionLabel);
                setText('modal-survey-improvement', button.dataset.surveyImprovement);

                paintStars(button.dataset.surveySatisfaction);

                modal.classList.remove('hidden');
                modal.classList.add('flex');
                modal.setAttribute('aria-hidden', 'false');
                document.body.classList.add('overflow-hidden');
            };

            const closeModal = () => {
                modal.classList.add('hidden');
                modal.classList.remove('flex');
                modal.setAttribute('aria-hidden', 'true');
                document.body.classList.remove('overflow-hidden');
            };

            document.querySelectorAll('[data-survey-modal-open]').forEach((button) => {
                button.addEventListener('click', () => openModal(button));
            });

            closeButton?.addEventListener('click', closeModal);
            closeBottomButton?.addEventListener('click', closeModal);

            modal?.addEventListener('click', (event) => {
                if (event.target === modal) {
                    closeModal();
                }
            });

            document.addEventListener('keydown', (event) => {
                if (event.key === 'Escape' && !modal.classList.contains('hidden')) {
                    closeModal();
                }
            });
        });
    </script>
</div>
@endsection
