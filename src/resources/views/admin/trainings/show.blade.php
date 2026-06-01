@extends('layouts.admin')

@section('content')
<div class="max-w-5xl mx-auto p-6">
    <div class="mb-6">
        <h1 class="text-2xl font-bold">
            {{ $training->isDiary() ? '日記トレーニング' : '今日のチャレンジ' }}
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

    <div class="grid md:grid-cols-2 gap-6">
        <div class="bg-white rounded shadow p-5">
            <h2 class="text-lg font-bold mb-4">入力内容</h2>

            @if ($training->isDiary())
                <div class="whitespace-pre-wrap leading-relaxed">{{ $training->diary_body }}</div>
            @else
                <div class="space-y-4">
                    <div>
                        <h3 class="font-bold">今日チャレンジしたこと</h3>
                        <p class="whitespace-pre-wrap">{{ $training->challenged_thing }}</p>
                    </div>
                    <div>
                        <h3 class="font-bold">できたこと</h3>
                        <p class="whitespace-pre-wrap">{{ $training->completed_thing }}</p>
                    </div>
                    <div>
                        <h3 class="font-bold">難しかったこと</h3>
                        <p class="whitespace-pre-wrap">{{ $training->difficult_thing }}</p>
                    </div>
                    <div>
                        <h3 class="font-bold">次に改善したいこと</h3>
                        <p class="whitespace-pre-wrap">{{ $training->next_improvement }}</p>
                    </div>
                </div>
            @endif
        </div>

        <div class="bg-white rounded shadow p-5">
            <h2 class="text-lg font-bold mb-4">採点結果</h2>

            <div class="mb-5">
                <div class="text-4xl font-bold">{{ $training->total_score }}点</div>
                <div class="text-sm text-gray-500">総合点</div>
            </div>

            <div class="space-y-2">
                <div>読みやすさ：{{ $training->readability_score }} / 25</div>
                <div>具体性：{{ $training->specificity_score }} / 25</div>
                <div>構成：{{ $training->structure_score }} / 25</div>
                <div>表現力：{{ $training->expression_score }} / 25</div>
            </div>

            <hr class="my-5">

            <div class="space-y-4">
                <div>
                    <h3 class="font-bold">良い点</h3>
                    <p class="whitespace-pre-wrap">{{ $training->good_point }}</p>
                </div>

                <div>
                    <h3 class="font-bold">改善点</h3>
                    <p class="whitespace-pre-wrap">{{ $training->improvement_point }}</p>
                </div>

                <div>
                    <h3 class="font-bold">次回の課題</h3>
                    <p class="whitespace-pre-wrap">{{ $training->next_task }}</p>
                </div>
            </div>
        </div>
    </div>

    <div class="mt-6 flex gap-2">
        <a href="{{ route('admin.trainings.index') }}" class="px-5 py-2 border rounded">
            一覧に戻る
        </a>
        <a href="{{ route('admin.trainings.diary.create') }}" class="px-5 py-2 bg-blue-600 text-white rounded">
            日記を書く
        </a>
        <a href="{{ route('admin.trainings.challenge.create') }}" class="px-5 py-2 bg-green-600 text-white rounded">
            今日のチャレンジ
        </a>
    </div>
</div>
@endsection
