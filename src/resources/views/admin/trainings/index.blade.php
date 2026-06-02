@extends('layouts.admin')

@section('content')
<div class="max-w-7xl mx-auto p-6">
    <div class="flex flex-col gap-4 mb-6">
        <div>
            <h1 class="text-2xl font-bold">トレーニング一覧</h1>
            <p class="text-sm text-gray-500">
                日記、今日のチャレンジ、要約力、言語化力、抽象化力、具体化力を日付ごとに確認できます。
            </p>
        </div>

        <div class="flex flex-wrap gap-2">
            <a href="{{ route('admin.trainings.diary.create') }}" class="px-4 py-2 bg-blue-600 text-white rounded">
                日記を書く
            </a>

            <a href="{{ route('admin.trainings.challenge.create') }}" class="px-4 py-2 bg-green-600 text-white rounded">
                今日のチャレンジ
            </a>

            <a href="{{ route('admin.trainings.summary.create') }}" data-ai-question-loading class="px-4 py-2 bg-purple-600 text-white rounded">
                要約力
            </a>

            <a href="{{ route('admin.trainings.verbalization.create') }}" data-ai-question-loading class="px-4 py-2 bg-indigo-600 text-white rounded">
                言語化力
            </a>

            <a href="{{ route('admin.trainings.abstraction.create') }}" data-ai-question-loading class="px-4 py-2 bg-pink-600 text-white rounded">
                抽象化力
            </a>

            <a href="{{ route('admin.trainings.concretization.create') }}" data-ai-question-loading class="px-4 py-2 bg-orange-600 text-white rounded">
                具体化力
            </a>
        </div>
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

    <div class="mb-4 flex flex-wrap gap-2">
        <a href="{{ route('admin.trainings.index') }}"
           class="px-3 py-1 border rounded {{ request('type') === null ? 'bg-gray-800 text-white' : '' }}">
            全て
        </a>

        <a href="{{ route('admin.trainings.index', ['type' => 'diary']) }}"
           class="px-3 py-1 border rounded {{ request('type') === 'diary' ? 'bg-gray-800 text-white' : '' }}">
            日記
        </a>

        <a href="{{ route('admin.trainings.index', ['type' => 'challenge']) }}"
           class="px-3 py-1 border rounded {{ request('type') === 'challenge' ? 'bg-gray-800 text-white' : '' }}">
            今日のチャレンジ
        </a>

        <a href="{{ route('admin.trainings.index', ['type' => 'summary']) }}"
          data-ai-question-loading
           class="px-3 py-1 border rounded {{ request('type') === 'summary' ? 'bg-gray-800 text-white' : '' }}">
            要約力
        </a>

        <a href="{{ route('admin.trainings.index', ['type' => 'verbalization']) }}"
          data-ai-question-loading
           class="px-3 py-1 border rounded {{ request('type') === 'verbalization' ? 'bg-gray-800 text-white' : '' }}">
            言語化力
        </a>

        <a href="{{ route('admin.trainings.index', ['type' => 'abstraction']) }}"
          data-ai-question-loading
           class="px-3 py-1 border rounded {{ request('type') === 'abstraction' ? 'bg-gray-800 text-white' : '' }}">
            抽象化力
        </a>

        <a href="{{ route('admin.trainings.index', ['type' => 'concretization']) }}"
          data-ai-question-loading
           class="px-3 py-1 border rounded {{ request('type') === 'concretization' ? 'bg-gray-800 text-white' : '' }}">
            具体化力
        </a>
    </div>

    <div class="bg-white rounded shadow overflow-x-auto">
        <table class="w-full border-collapse text-sm">
            <thead class="bg-gray-100">
                <tr>
                    <th class="p-3 text-left whitespace-nowrap">日付</th>
                    <th class="p-3 text-left whitespace-nowrap">種類</th>
                    <th class="p-3 text-left whitespace-nowrap">問題 / 内容</th>
                    <th class="p-3 text-left whitespace-nowrap">状態</th>
                    <th class="p-3 text-left whitespace-nowrap">総合点</th>
                    <th class="p-3 text-left whitespace-nowrap">項目1</th>
                    <th class="p-3 text-left whitespace-nowrap">項目2</th>
                    <th class="p-3 text-left whitespace-nowrap">項目3</th>
                    <th class="p-3 text-left whitespace-nowrap">項目4</th>
                    <th class="p-3 text-left whitespace-nowrap">詳細</th>
                </tr>
            </thead>

            <tbody>
                @forelse ($trainings as $training)
                    @php
                        $scoreLabels = $training->scoreLabels();

                        $summaryText = $training->question_title
                            ?: ($training->isDiary()
                                ? Str::limit($training->diary_body, 40)
                                : ($training->challenged_thing
                                    ? Str::limit($training->challenged_thing, 40)
                                    : '-'));

                        $isAnswered = $training->isAnswered();
                    @endphp

                    <tr class="border-t">
                        <td class="p-3 whitespace-nowrap">
                            {{ $training->training_date->format('Y-m-d') }}
                        </td>

                        <td class="p-3 whitespace-nowrap">
                            {{ $training->typeLabel() }}
                        </td>

                        <td class="p-3 min-w-[220px]">
                            {{ $summaryText }}
                        </td>

                        <td class="p-3 whitespace-nowrap">
                            @if ($isAnswered)
                                <span class="px-2 py-1 text-xs bg-green-100 text-green-800 rounded">
                                    採点済み
                                </span>
                            @else
                                <span class="px-2 py-1 text-xs bg-yellow-100 text-yellow-800 rounded">
                                    未回答
                                </span>
                            @endif
                        </td>

                        <td class="p-3 font-bold whitespace-nowrap">
                            {{ $training->total_score ?? '-' }}{{ $training->total_score !== null ? '点' : '' }}
                        </td>

                        <td class="p-3 whitespace-nowrap">
                            <div class="text-xs text-gray-500">{{ $scoreLabels['readability_score'] }}</div>
                            <div>{{ $training->readability_score ?? '-' }} / 25</div>
                        </td>

                        <td class="p-3 whitespace-nowrap">
                            <div class="text-xs text-gray-500">{{ $scoreLabels['specificity_score'] }}</div>
                            <div>{{ $training->specificity_score ?? '-' }} / 25</div>
                        </td>

                        <td class="p-3 whitespace-nowrap">
                            <div class="text-xs text-gray-500">{{ $scoreLabels['structure_score'] }}</div>
                            <div>{{ $training->structure_score ?? '-' }} / 25</div>
                        </td>

                        <td class="p-3 whitespace-nowrap">
                            <div class="text-xs text-gray-500">{{ $scoreLabels['expression_score'] }}</div>
                            <div>{{ $training->expression_score ?? '-' }} / 25</div>
                        </td>

                        <td class="p-3 whitespace-nowrap">
                            @if ($training->isAiQuestionTraining() && ! $training->answer_body)
                                @php
                                    $continueRoute = match ($training->type) {
                                        'summary' => route('admin.trainings.summary.create'),
                                        'verbalization' => route('admin.trainings.verbalization.create'),
                                        'abstraction' => route('admin.trainings.abstraction.create'),
                                        'concretization' => route('admin.trainings.concretization.create'),
                                        default => route('admin.trainings.show', $training),
                                    };
                                @endphp

                                <a href="{{ $continueRoute }}" class="text-orange-600 underline">
                                    回答する
                                </a>
                            @else
                                <a href="{{ route('admin.trainings.show', $training) }}" class="text-blue-600 underline">
                                    詳細
                                </a>
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="10" class="p-6 text-center text-gray-500">
                            まだ記録がありません。
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="mt-4">
        {{ $trainings->links() }}
    </div>
</div>
@endsection
