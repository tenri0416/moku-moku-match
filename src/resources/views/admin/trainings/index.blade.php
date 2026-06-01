@extends('layouts.admin')

@section('content')
<div class="max-w-6xl mx-auto p-6">
    <div class="flex items-center justify-between mb-6">
        <div>
            <h1 class="text-2xl font-bold">トレーニング一覧</h1>
            <p class="text-sm text-gray-500">日記トレーニングと今日のチャレンジを日付ごとに確認できます。</p>
        </div>

        <div class="flex gap-2">
            <a href="{{ route('admin.trainings.diary.create') }}" class="px-4 py-2 bg-blue-600 text-white rounded">
                日記を書く
            </a>
            <a href="{{ route('admin.trainings.challenge.create') }}" class="px-4 py-2 bg-green-600 text-white rounded">
                今日のチャレンジ
            </a>
        </div>
    </div>

    @if (session('success'))
        <div class="mb-4 p-3 bg-green-100 text-green-800 rounded">
            {{ session('success') }}
        </div>
    @endif

    <div class="mb-4 flex gap-2">
        <a href="{{ route('admin.trainings.index') }}" class="px-3 py-1 border rounded">
            全て
        </a>
        <a href="{{ route('admin.trainings.index', ['type' => 'diary']) }}" class="px-3 py-1 border rounded">
            日記
        </a>
        <a href="{{ route('admin.trainings.index', ['type' => 'challenge']) }}" class="px-3 py-1 border rounded">
            今日のチャレンジ
        </a>
    </div>

    <div class="bg-white rounded shadow overflow-hidden">
        <table class="w-full border-collapse">
            <thead class="bg-gray-100">
                <tr>
                    <th class="p-3 text-left">日付</th>
                    <th class="p-3 text-left">種類</th>
                    <th class="p-3 text-left">総合点</th>
                    <th class="p-3 text-left">読みやすさ</th>
                    <th class="p-3 text-left">具体性</th>
                    <th class="p-3 text-left">構成</th>
                    <th class="p-3 text-left">表現力</th>
                    <th class="p-3 text-left">詳細</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($trainings as $training)
                    <tr class="border-t">
                        <td class="p-3">{{ $training->training_date->format('Y-m-d') }}</td>
                        <td class="p-3">
                            {{ $training->isDiary() ? '日記' : '今日のチャレンジ' }}
                        </td>
                        <td class="p-3 font-bold">{{ $training->total_score }}点</td>
                        <td class="p-3">{{ $training->readability_score }} / 25</td>
                        <td class="p-3">{{ $training->specificity_score }} / 25</td>
                        <td class="p-3">{{ $training->structure_score }} / 25</td>
                        <td class="p-3">{{ $training->expression_score }} / 25</td>
                        <td class="p-3">
                            <a href="{{ route('admin.trainings.show', $training) }}" class="text-blue-600 underline">
                                詳細
                            </a>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="8" class="p-6 text-center text-gray-500">
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
