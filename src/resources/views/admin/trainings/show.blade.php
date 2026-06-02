@extends('layouts.admin')

@section('content')
@php
    $scoreLabels = $training->scoreLabels();

    $questionBody = collect(preg_split("/\r\n|\n|\r/", $training->question_body ?? ''))
        ->map(fn ($line) => trim($line))
        ->implode(PHP_EOL);

    $answerBody = collect(preg_split("/\r\n|\n|\r/", $training->answer_body ?? ''))
        ->map(fn ($line) => trim($line))
        ->implode(PHP_EOL);

    $diaryBody = collect(preg_split("/\r\n|\n|\r/", $training->diary_body ?? ''))
        ->map(fn ($line) => trim($line))
        ->implode(PHP_EOL);
@endphp

<div class="max-w-5xl mx-auto p-6">
    <div class="mb-6">
        <h1 class="text-2xl font-bold">
            {{ $training->typeLabel() }}
        </h1>
        <p class="text-sm text-gray-500">
            {{ $training->training_date->format('Y-m-d') }}
        </p>
    </div>

    @if (session('success'))
        <div class="mb-4 p-3 bg-green-100 text-green-800 rounded">
            {{ session('success') }}
        </div>
    @endif

    @if (session('error'))
        <div class="mb-4 p-3 bg-red-100 text-red-800 rounded">
            {{ session('error') }}
        </div>
    @endif

    <div class="grid md:grid-cols-2 gap-6">
        <div class="bg-white rounded shadow p-5">
            <h2 class="text-lg font-bold mb-4">入力内容</h2>

            @if ($training->isAiQuestionTraining())
                <div class="space-y-5">
                    <div>
                        <h3 class="font-bold text-blue-700">問題タイトル</h3>
                        <p>{{ trim($training->question_title) }}</p>
                    </div>

                    <div>
                        <h3 class="font-bold text-blue-700">問題本文</h3>
                        <div class="whitespace-pre-wrap border rounded p-4 bg-gray-50 leading-relaxed">{{ $questionBody }}</div>
                    </div>

                    <div>
                        <h3 class="font-bold text-blue-700">回答</h3>
                        <div class="whitespace-pre-wrap border rounded p-4 leading-relaxed">{{ $answerBody }}</div>
                    </div>
                </div>
            @elseif ($training->isDiary())
                <div class="whitespace-pre-wrap leading-relaxed">{{ $diaryBody }}</div>
            @elseif ($training->isChallenge())
                <div class="space-y-4">
                    <div>
                        <h3 class="font-bold">今日チャレンジしたこと</h3>
                        <p class="whitespace-pre-wrap">{{ trim($training->challenged_thing) }}</p>
                    </div>
                    <div>
                        <h3 class="font-bold">できたこと</h3>
                        <p class="whitespace-pre-wrap">{{ trim($training->completed_thing) }}</p>
                    </div>
                    <div>
                        <h3 class="font-bold">難しかったこと</h3>
                        <p class="whitespace-pre-wrap">{{ trim($training->difficult_thing) }}</p>
                    </div>
                    <div>
                        <h3 class="font-bold">次に改善したいこと</h3>
                        <p class="whitespace-pre-wrap">{{ trim($training->next_improvement) }}</p>
                    </div>
                </div>
            @else
                <p class="text-gray-500">入力内容がありません。</p>
            @endif
        </div>

        <div class="bg-white rounded shadow p-5">
            <h2 class="text-lg font-bold mb-4">採点結果</h2>

            <div class="mb-5">
                <div class="text-4xl font-bold">
                    {{ $training->total_score ?? '-' }}{{ $training->total_score !== null ? '点' : '' }}
                </div>
                <div class="text-sm text-gray-500">総合点</div>
            </div>

            <div class="space-y-2">
                <div>{{ $scoreLabels['readability_score'] }}：{{ $training->readability_score ?? '-' }} / 25</div>
                <div>{{ $scoreLabels['specificity_score'] }}：{{ $training->specificity_score ?? '-' }} / 25</div>
                <div>{{ $scoreLabels['structure_score'] }}：{{ $training->structure_score ?? '-' }} / 25</div>
                <div>{{ $scoreLabels['expression_score'] }}：{{ $training->expression_score ?? '-' }} / 25</div>
            </div>

            <hr class="my-5">

            <div class="space-y-4">
                <div>
                    <h3 class="font-bold">良い点</h3>
                    <p class="whitespace-pre-wrap">{{ $training->good_point ?: '未採点です。' }}</p>
                </div>

                <div>
                    <h3 class="font-bold">改善点</h3>
                    <p class="whitespace-pre-wrap">{{ $training->improvement_point ?: '未採点です。' }}</p>
                </div>

                <div>
                    <h3 class="font-bold">次回の課題</h3>
                    <p class="whitespace-pre-wrap">{{ $training->next_task ?: '未採点です。' }}</p>
                </div>
            </div>
        </div>
    </div>

    <div class="mt-6 flex flex-wrap gap-2">
        <a href="{{ route('admin.trainings.index') }}" class="px-5 py-2 border rounded">
            一覧に戻る
        </a>

        <a href="{{ route('admin.trainings.diary.create') }}" class="px-5 py-2 bg-blue-600 text-white rounded">
            日記を書く
        </a>

        <a href="{{ route('admin.trainings.challenge.create') }}" class="px-5 py-2 bg-green-600 text-white rounded">
            今日のチャレンジ
        </a>

        <a href="{{ route('admin.trainings.summary.create') }}" class="px-5 py-2 bg-purple-600 text-white rounded">
            要約力
        </a>

        <a href="{{ route('admin.trainings.verbalization.create') }}" class="px-5 py-2 bg-indigo-600 text-white rounded">
            言語化力
        </a>

        <a href="{{ route('admin.trainings.abstraction.create') }}"  class="px-5 py-2 bg-pink-600 text-white rounded">
            抽象化力
        </a>

        <a href="{{ route('admin.trainings.concretization.create') }}" class="px-5 py-2 bg-orange-600 text-white rounded">
            具体化力
        </a>
    </div>
</div>
@endsection
