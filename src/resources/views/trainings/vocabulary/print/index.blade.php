@extends('layouts.app')

@section('content')
<div class="min-h-screen bg-[#F8FAFF] px-3 pb-28 pt-5 text-[#071433] md:px-8 md:py-10">
    <div class="mx-auto max-w-[1000px]">
        @if (session('success'))
            <div class="mb-5 rounded-2xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm font-bold text-emerald-700">
                {{ session('success') }}
            </div>
        @endif

        @if (session('error'))
            <div class="mb-5 rounded-2xl border border-red-200 bg-red-50 px-4 py-3 text-sm font-bold text-red-700">
                {{ session('error') }}
            </div>
        @endif

        <section class="mb-5 rounded-3xl bg-gradient-to-br from-[#1D66F3] to-[#0648D8] px-5 py-6 text-white shadow-lg md:p-8">
            <p class="text-sm font-black text-blue-50">日本語検定風</p>
            <h1 class="mt-2 text-3xl font-black md:text-4xl">ボキャブラリー印刷テスト</h1>
            <p class="mt-3 text-sm font-bold leading-7 text-blue-50 md:text-base">
                登録済みの言葉から、問題PDFと解答PDFを作成します。紙に印刷して、シャーペンや鉛筆で解けます。
            </p>
        </section>

        <div class="grid gap-5 md:grid-cols-[1fr_320px]">
            <main class="rounded-3xl border border-[#DDE6F5] bg-white p-5 shadow-sm md:p-7">
                <form method="POST" action="{{ route('trainings.vocabulary.print.store') }}" class="space-y-6" data-ai-loading="true">
                    @csrf

                    <div>
                        <label class="text-sm font-black text-[#071433]">問題数</label>
                        <select name="question_count" id="question_count" class="mt-2 w-full rounded-2xl border border-[#CBD7EA] px-4 py-3 text-base font-bold outline-none focus:border-[#0D4FE8]">
                            @foreach ([5, 10, 20, 30] as $count)
                                <option value="{{ $count }}" @selected((int) old('question_count', 10) === $count)>
                                    {{ $count }}問
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label class="text-sm font-black text-[#071433]">出題対象</label>
                        <select name="target_filter" class="mt-2 w-full rounded-2xl border border-[#CBD7EA] px-4 py-3 text-base font-bold outline-none focus:border-[#0D4FE8]">
                            <option value="review_target" @selected(old('target_filter', 'review_target') === 'review_target')>復習対象のみ</option>
                            <option value="weak" @selected(old('target_filter') === 'weak')>苦手のみ</option>
                            <option value="not_reviewed" @selected(old('target_filter') === 'not_reviewed')>未復習のみ</option>
                            <option value="all" @selected(old('target_filter') === 'all')>すべて</option>
                        </select>
                    </div>

                    <div class="grid gap-4 md:grid-cols-2">
                        <div>
                            <label class="text-sm font-black text-[#071433]">カテゴリー指定</label>
                            <select name="category" class="mt-2 w-full rounded-2xl border border-[#CBD7EA] px-4 py-3 text-base font-bold outline-none focus:border-[#0D4FE8]">
                                <option value="">指定なし</option>
                                @foreach ($categories as $category)
                                    <option value="{{ $category }}" @selected(old('category') === $category)>
                                        {{ $category }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div>
                            <label class="text-sm font-black text-[#071433]">重要度指定</label>
                            <select name="importance" class="mt-2 w-full rounded-2xl border border-[#CBD7EA] px-4 py-3 text-base font-bold outline-none focus:border-[#0D4FE8]">
                                <option value="">指定なし</option>
                                @for ($i = 1; $i <= 5; $i++)
                                    <option value="{{ $i }}" @selected((string) old('importance') === (string) $i)>
                                        重要度{{ $i }}
                                    </option>
                                @endfor
                            </select>
                        </div>
                    </div>

                    <div>
                        <label class="text-sm font-black text-[#071433]">問題形式</label>
                        <div class="mt-3 grid gap-3 md:grid-cols-2">
                            @foreach ($questionTypes as $type)
                                @php
                                    $defaultChecked = in_array($type, ['意味問題', '例文問題', '使い方説明問題', '類義語問題', '反対語問題', '選択問題'], true);
                                    $oldTypes = old('question_types');
                                    $checked = is_array($oldTypes) ? in_array($type, $oldTypes, true) : $defaultChecked;
                                @endphp
                                <label class="flex items-center gap-3 rounded-2xl bg-[#F8FAFF] px-4 py-3">
                                    <input type="checkbox" name="question_types[]" value="{{ $type }}" class="h-5 w-5" @checked($checked)>
                                    <span class="text-sm font-black text-[#071433]">{{ $type }}</span>
                                </label>
                            @endforeach
                        </div>
                    </div>

                    <div>
                        <label class="text-sm font-black text-[#071433]">制限時間</label>
                        <div class="mt-2 flex items-center gap-3">
                            <input
                                type="number"
                                name="time_limit_minutes"
                                id="time_limit_minutes"
                                value="{{ old('time_limit_minutes', 10) }}"
                                min="1"
                                max="180"
                                class="w-full rounded-2xl border border-[#CBD7EA] px-4 py-3 text-base font-bold outline-none focus:border-[#0D4FE8]"
                            >
                            <span class="shrink-0 text-sm font-black text-[#64748B]">分</span>
                        </div>
                    </div>

                    @if ($errors->any())
                        <div class="rounded-2xl border border-red-200 bg-red-50 px-4 py-3 text-sm font-bold text-red-700">
                            @foreach ($errors->all() as $error)
                                <p>{{ $error }}</p>
                            @endforeach
                        </div>
                    @endif

                    <button class="flex h-[56px] w-full items-center justify-center rounded-2xl bg-[#0D4FE8] text-base font-black text-white shadow-[0_10px_18px_rgba(13,79,232,0.25)]">
                        印刷テストを作成する
                    </button>
                </form>
            </main>

            <aside class="space-y-4">
                <section class="rounded-3xl border border-[#DDE6F5] bg-white p-5 shadow-sm">
                    <h2 class="text-lg font-black">登録単語</h2>
                    <p class="mt-2 text-4xl font-black text-[#0D4FE8]">{{ $totalWords }}</p>
                    <p class="mt-2 text-sm font-bold leading-7 text-[#64748B]">
                        ログイン中ユーザーの単語のみを出題対象にします。
                    </p>
                </section>

                <section class="rounded-3xl border border-[#DDE6F5] bg-white p-5 shadow-sm">
                    <h2 class="text-lg font-black">最近作成したテスト</h2>

                    <div class="mt-4 space-y-3">
                        @forelse ($printTests as $test)
                            <a href="{{ route('trainings.vocabulary.print.show', $test) }}" class="block rounded-2xl bg-[#F8FAFF] px-4 py-3">
                                <p class="text-sm font-black text-[#071433]">{{ $test->question_count }}問 / {{ $test->time_limit_minutes }}分</p>
                                <p class="mt-1 text-xs font-bold text-[#64748B]">{{ $test->created_at->format('Y/m/d H:i') }}</p>
                            </a>
                        @empty
                            <p class="text-sm font-bold text-[#64748B]">
                                まだ作成履歴がありません。
                            </p>
                        @endforelse
                    </div>
                </section>

                <a href="{{ route('trainings.vocabulary.index') }}"
                    class="flex h-[50px] items-center justify-center rounded-2xl border-2 border-[#0D4FE8] bg-white text-sm font-black text-[#0D4FE8]">
                    一覧に戻る
                </a>
            </aside>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', () => {
    const questionCount = document.querySelector('#question_count');
    const timeLimit = document.querySelector('#time_limit_minutes');

    if (!questionCount || !timeLimit) {
        return;
    }

    const defaultMinutes = {
        5: 5,
        10: 10,
        20: 20,
        30: 30,
    };

    questionCount.addEventListener('change', () => {
        const value = questionCount.value;
        if (defaultMinutes[value]) {
            timeLimit.value = defaultMinutes[value];
        }
    });
});
</script>
@endsection
