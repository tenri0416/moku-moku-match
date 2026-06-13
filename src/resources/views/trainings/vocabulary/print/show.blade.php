@extends('layouts.app')

@section('content')
<div class="min-h-screen bg-[#F8FAFF] px-3 pb-28 pt-5 text-[#071433] md:px-8 md:py-10">
    <div class="mx-auto max-w-[900px]">
        @if (session('success'))
            <div class="mb-5 rounded-2xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm font-bold text-emerald-700">
                {{ session('success') }}
            </div>
        @endif

        <section class="rounded-3xl bg-gradient-to-br from-[#1D66F3] to-[#0648D8] px-5 py-6 text-white shadow-lg md:p-8">
            <p class="text-sm font-black text-blue-50">作成完了</p>
            <h1 class="mt-2 text-3xl font-black md:text-4xl">印刷テストを作成しました</h1>
            <p class="mt-3 text-sm font-bold text-blue-50">
                問題PDFと解答PDFを別々にダウンロードできます。
            </p>
        </section>

        <div class="mt-5 grid gap-4 md:grid-cols-2">
            <a href="{{ route('trainings.vocabulary.print.questions', $printTest) }}"
                class="rounded-3xl border border-[#DDE6F5] bg-white p-6 shadow-sm transition hover:bg-[#F8FAFF]">
                <p class="text-sm font-black text-[#0D4FE8]">問題PDF</p>
                <h2 class="mt-2 text-2xl font-black">問題用紙をダウンロード</h2>
                <p class="mt-3 text-sm font-bold leading-7 text-[#64748B]">
                    表紙、問題用紙、解答用紙が含まれます。
                </p>
            </a>

            <a href="{{ route('trainings.vocabulary.print.answers', $printTest) }}"
                class="rounded-3xl border border-[#DDE6F5] bg-white p-6 shadow-sm transition hover:bg-[#F8FAFF]">
                <p class="text-sm font-black text-emerald-600">解答PDF</p>
                <h2 class="mt-2 text-2xl font-black">解答をダウンロード</h2>
                <p class="mt-3 text-sm font-bold leading-7 text-[#64748B]">
                    模範解答と採点基準が含まれます。
                </p>
            </a>
        </div>

        <section class="mt-5 rounded-3xl border border-[#DDE6F5] bg-white p-5 shadow-sm">
            <h2 class="text-xl font-black">テスト情報</h2>

            <div class="mt-4 grid gap-3 md:grid-cols-4">
                <div class="rounded-2xl bg-[#F8FAFF] p-4">
                    <p class="text-xs font-bold text-[#64748B]">問題数</p>
                    <p class="mt-1 text-2xl font-black">{{ $printTest->question_count }}問</p>
                </div>

                <div class="rounded-2xl bg-[#F8FAFF] p-4">
                    <p class="text-xs font-bold text-[#64748B]">制限時間</p>
                    <p class="mt-1 text-2xl font-black">{{ $printTest->time_limit_minutes }}分</p>
                </div>

                <div class="rounded-2xl bg-[#F8FAFF] p-4">
                    <p class="text-xs font-bold text-[#64748B]">合計点</p>
                    <p class="mt-1 text-2xl font-black">{{ $printTest->total_score }}点</p>
                </div>

                <div class="rounded-2xl bg-[#F8FAFF] p-4">
                    <p class="text-xs font-bold text-[#64748B]">作成日</p>
                    <p class="mt-1 text-base font-black">{{ $printTest->created_at->format('Y/m/d') }}</p>
                </div>
            </div>

            <div class="mt-5 space-y-3">
                @foreach ($printTest->questions as $question)
                    <div class="rounded-2xl border border-[#E8EDF6] px-4 py-3">
                        <p class="text-sm font-black text-[#0D4FE8]">
                            第{{ $question->question_number }}問 / {{ $question->question_type }} / {{ $question->point }}点
                        </p>
                        <p class="mt-2 text-sm font-bold leading-7 text-[#334155]">
                            {{ $question->question_body }}
                        </p>
                    </div>
                @endforeach
            </div>
        </section>

        <div class="mt-5 grid gap-3 md:grid-cols-2">
            <a href="{{ route('trainings.vocabulary.print.index') }}"
                class="flex h-[50px] items-center justify-center rounded-2xl bg-[#0D4FE8] text-sm font-black text-white">
                もう一度作成する
            </a>

            <a href="{{ route('trainings.vocabulary.index') }}"
                class="flex h-[50px] items-center justify-center rounded-2xl border-2 border-[#0D4FE8] bg-white text-sm font-black text-[#0D4FE8]">
                一覧に戻る
            </a>
        </div>
    </div>
</div>
@endsection
