@extends('layouts.app')

@section('content')
<div class="min-h-screen bg-[#F8FAFF] px-3 pb-28 pt-5 text-[#071433] md:px-8 md:py-10">
    <div class="mx-auto max-w-[760px] rounded-3xl bg-white p-5 shadow-sm ring-1 ring-[#DDE6F5] md:p-8">
        <a href="{{ route('trainings.vocabulary.index') }}" class="text-sm font-black text-[#0D4FE8]">
            ‹ 一覧に戻る
        </a>

        <div class="mt-5 rounded-3xl bg-gradient-to-br from-[#1D66F3] to-[#0648D8] px-5 py-5 text-white">
            <p class="text-sm font-black text-blue-50">ボキャブラリー復習</p>
            <h1 class="mt-2 text-3xl font-black">{{ $word->word }}</h1>
            <p class="mt-3 rounded-full bg-white/15 px-4 py-2 text-center text-sm font-black">
                {{ $questionType }}
            </p>
        </div>

        <form method="POST" action="{{ route('trainings.vocabulary.review.store') }}" class="mt-5 space-y-5" data-ai-loading="true" data-ai-loading-type="score">
            @csrf

            <input type="hidden" name="vocabulary_word_id" value="{{ $word->id }}">
            <input type="hidden" name="question_type" value="{{ $questionType }}">
            <input type="hidden" name="question_body" value="{{ $questionBody }}">

            <section class="rounded-2xl border border-[#DDE6F5] bg-[#F8FAFF] px-4 py-4">
                <p class="text-sm font-black text-[#0D4FE8]">問題</p>
                <p class="mt-2 text-base font-bold leading-8 text-[#1B2540]">
                    {{ $questionBody }}
                </p>
            </section>

            <section>
                <label class="text-sm font-black text-[#071433]">あなたの回答</label>
                <textarea
                    name="answer_body"
                    rows="7"
                    class="mt-2 w-full rounded-2xl border border-[#CBD7EA] px-4 py-3 text-base font-bold leading-8 outline-none focus:border-[#0D4FE8]"
                    placeholder="自分の言葉で回答してください。"
                >{{ old('answer_body') }}</textarea>

                @error('answer_body')
                    <p class="mt-2 rounded-xl bg-red-50 px-3 py-2 text-sm font-bold text-red-600">
                        {{ $message }}
                    </p>
                @enderror
            </section>

            <details class="rounded-2xl border border-[#DDE6F5] bg-white">
                <summary class="cursor-pointer px-4 py-4 text-sm font-black text-[#0D4FE8]">
                    登録した意味・例文を見る
                </summary>

                <div class="border-t border-[#E8EDF6] px-4 py-4">
                    <p class="text-xs font-black text-[#0D4FE8]">登録した意味</p>
                    <p class="mt-2 text-sm font-bold leading-7 text-[#334155]">{{ $word->meaning }}</p>

                    <p class="mt-4 text-xs font-black text-[#0D4FE8]">登録した例文</p>
                    <p class="mt-2 text-sm font-bold leading-7 text-[#334155]">{{ $word->example_sentence }}</p>
                </div>
            </details>

            <button
                type="submit"
                class="flex h-[56px] w-full items-center justify-center rounded-2xl bg-[#0D4FE8] text-base font-black text-white shadow-[0_10px_18px_rgba(13,79,232,0.25)]"
            >
                回答してAI採点する
            </button>
        </form>
    </div>
</div>
@endsection
