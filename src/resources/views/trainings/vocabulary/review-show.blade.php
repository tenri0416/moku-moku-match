@extends('layouts.app')

@section('content')
@php
    $word = $review->vocabularyWord;

    $scoreRows = [
        ['label' => '意味の正確さ', 'score' => $review->meaning_score, 'icon' => '📘'],
        ['label' => '説明のわかりやすさ', 'score' => $review->explanation_score, 'icon' => '💬'],
        ['label' => '使い方の自然さ', 'score' => $review->usage_score, 'icon' => '✏️'],
        ['label' => '記憶への定着度', 'score' => $review->retention_score, 'icon' => '🧠'],
    ];
@endphp

<div class="min-h-screen bg-[#F8FAFF] px-3 pb-28 pt-4 text-[#071433] md:px-8 md:py-10">
    <div class="mx-auto max-w-[1000px]">
        @if (session('success'))
            <div class="mb-5 rounded-2xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm font-bold text-emerald-700">
                {{ session('success') }}
            </div>
        @endif

        <section class="mb-4 rounded-[18px] bg-gradient-to-br from-[#1D66F3] to-[#0648D8] px-4 py-4 text-white shadow-[0_10px_22px_rgba(13,79,232,0.24)] md:p-7">
            <div class="flex items-center justify-between gap-3">
                <div>
                    <p class="text-sm font-black text-blue-50">採点結果</p>
                    <h1 class="mt-1 text-2xl font-black md:text-4xl">{{ $word->word }}</h1>
                </div>

                <span class="rounded-full bg-white/20 px-3 py-1 text-xs font-black">
                    +{{ $review->earned_points }}pt
                </span>
            </div>

            <div class="mt-4 rounded-2xl bg-white/12 px-4 py-4 text-center">
                <span class="text-5xl font-black md:text-7xl">{{ $review->total_score ?? '-' }}</span>
                <span class="text-xl font-black">点</span>
            </div>
        </section>

        <div class="grid gap-4 md:grid-cols-[1fr_360px]">
            <main class="space-y-4">
                <section class="rounded-2xl border border-[#DDE6F5] bg-white p-4">
                    <h2 class="text-lg font-black text-[#071433]">問題</h2>
                    <p class="mt-2 text-sm font-bold text-[#0D4FE8]">{{ $review->question_type }}</p>
                    <p class="mt-2 whitespace-pre-wrap text-sm font-bold leading-7 text-[#334155]">{{ $review->question_body }}</p>
                </section>

                <section class="rounded-2xl border border-[#DDE6F5] bg-white p-4">
                    <h2 class="text-lg font-black text-[#071433]">あなたの回答</h2>
                    <p class="mt-2 whitespace-pre-wrap text-sm font-bold leading-7 text-[#334155]">{{ $review->answer_body }}</p>
                </section>

                <section class="rounded-2xl border border-emerald-200 bg-emerald-50 p-4">
                    <h2 class="text-lg font-black text-emerald-800">正しい意味の補足</h2>
                    <p class="mt-2 whitespace-pre-wrap text-sm font-bold leading-7 text-[#334155]">{{ $review->correct_meaning }}</p>
                </section>

                <section class="rounded-2xl border border-[#DDE6F5] bg-white p-4">
                    <h2 class="text-lg font-black text-[#071433]">登録情報</h2>

                    <p class="mt-3 text-xs font-black text-[#0D4FE8]">登録した意味</p>
                    <p class="mt-1 whitespace-pre-wrap text-sm font-bold leading-7 text-[#334155]">{{ $word->meaning }}</p>

                    <p class="mt-4 text-xs font-black text-[#0D4FE8]">登録した例文</p>
                    <p class="mt-1 whitespace-pre-wrap text-sm font-bold leading-7 text-[#334155]">{{ $word->example_sentence }}</p>
                </section>
            </main>

            <aside class="space-y-4">
                <section class="rounded-2xl border border-[#DDE6F5] bg-white p-4">
                    <h2 class="text-lg font-black text-[#071433]">評価</h2>

                    <div class="mt-4 space-y-4">
                        @foreach ($scoreRows as $row)
                            @php
                                $score = $row['score'];
                                $width = $score !== null ? min(100, max(0, ($score / 25) * 100)) : 0;
                            @endphp

                            <div>
                                <div class="mb-1 flex justify-between text-sm font-black">
                                    <span>{{ $row['icon'] }} {{ $row['label'] }}</span>
                                    <span class="text-[#0D4FE8]">{{ $score ?? '-' }}/25</span>
                                </div>
                                <div class="h-2.5 overflow-hidden rounded-full bg-slate-200">
                                    <div class="h-full rounded-full bg-[#0D4FE8]" style="width: {{ $width }}%;"></div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </section>

                <section class="rounded-2xl border border-emerald-200 bg-emerald-50 p-4">
                    <h2 class="text-base font-black text-emerald-700">良い点</h2>
                    <p class="mt-2 whitespace-pre-wrap text-sm font-bold leading-7">{{ $review->good_point }}</p>
                </section>

                <section class="rounded-2xl border border-amber-200 bg-amber-50 p-4">
                    <h2 class="text-base font-black text-amber-700">改善点</h2>
                    <p class="mt-2 whitespace-pre-wrap text-sm font-bold leading-7">{{ $review->improvement_point }}</p>
                </section>

                <section class="rounded-2xl border border-blue-200 bg-blue-50 p-4">
                    <h2 class="text-base font-black text-[#0D4FE8]">次回の課題</h2>
                    <p class="mt-2 whitespace-pre-wrap text-sm font-bold leading-7">{{ $review->next_task }}</p>
                </section>
            </aside>
        </div>

        <div class="mt-5 grid gap-3 md:grid-cols-3">
            <a href="{{ route('trainings.vocabulary.index') }}"
                class="flex h-[50px] items-center justify-center rounded-xl border-2 border-[#0D4FE8] bg-white text-sm font-black text-[#0D4FE8]">
                一覧へ
            </a>

            <a href="{{ route('trainings.vocabulary.review') }}"
                class="flex h-[50px] items-center justify-center rounded-xl bg-[#0D4FE8] text-sm font-black text-white">
                次の復習へ
            </a>

            <a href="{{ route('trainings.index') }}"
                class="flex h-[50px] items-center justify-center rounded-xl border border-[#DDE6F5] bg-white text-sm font-black text-[#071433]">
                トレーニング一覧へ
            </a>
        </div>
    </div>
</div>
@endsection
