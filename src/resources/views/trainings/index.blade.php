@extends('layouts.app')

@section('content')
<div class="max-w-7xl mx-auto p-6">
    <div class="mb-6">
        <h1 class="text-2xl font-bold">自己成長トレーニング</h1>
        <p class="text-sm text-gray-500">
            日記、今日のチャレンジ、要約力、言語化力、抽象化力、具体化力を日々トレーニングできます。
        </p>
        <p class="mt-2 font-bold text-blue-700">
            現在の総ポイント：{{ $myTotalPoints }} pt
        </p>
        <div class="mt-4 rounded-2xl border border-slate-200 bg-white p-4 shadow-sm">
            <h2 class="text-sm font-bold text-slate-900">
                ポイント付与ルール
            </h2>
        
            <div class="mt-3 grid gap-2 text-sm font-semibold text-slate-600 sm:grid-cols-2 lg:grid-cols-3">
                <div class="rounded-xl bg-slate-50 px-4 py-3">
                    100点：<span class="font-black text-indigo-600">10ポイント</span>
                </div>
        
                <div class="rounded-xl bg-slate-50 px-4 py-3">
                    90点〜99点：<span class="font-black text-indigo-600">9ポイント</span>
                </div>
        
                <div class="rounded-xl bg-slate-50 px-4 py-3">
                    80点〜89点：<span class="font-black text-indigo-600">8ポイント</span>
                </div>
        
                <div class="rounded-xl bg-slate-50 px-4 py-3">
                    70点〜79点：<span class="font-black text-indigo-600">7ポイント</span>
                </div>
        
                <div class="rounded-xl bg-slate-50 px-4 py-3">
                    60点〜69点：<span class="font-black text-indigo-600">6ポイント</span>
                </div>
        
                <div class="rounded-xl bg-slate-50 px-4 py-3">
                    0点〜59点：<span class="font-black text-indigo-600">1ポイント</span>
                </div>
            </div>
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

    <div class="grid md:grid-cols-3 gap-4 mb-8">
        <a href="{{ route('trainings.diary.create') }}"
           class="block p-5 bg-white rounded shadow border {{ $todayStatuses['diary'] ? 'opacity-50 pointer-events-none' : '' }}">
            <h2 class="font-bold text-lg">日記トレーニング</h2>
            <p class="text-sm text-gray-500">今日の出来事・感情・理由・学びを書く</p>
            <p class="mt-3 text-sm font-bold">{{ $todayStatuses['diary'] ? '本日実施済み' : '10pt〜' }}</p>
        </a>

        <a href="{{ route('trainings.challenge.create') }}"
           class="block p-5 bg-white rounded shadow border {{ $todayStatuses['challenge'] ? 'opacity-50 pointer-events-none' : '' }}">
            <h2 class="font-bold text-lg">今日のチャレンジ</h2>
            <p class="text-sm text-gray-500">挑戦・できたこと・改善点を整理する</p>
            <p class="mt-3 text-sm font-bold">{{ $todayStatuses['challenge'] ? '本日実施済み' : '10pt〜' }}</p>
        </a>

        <a href="{{ route('trainings.summary.create') }}"
           class="block p-5 bg-white rounded shadow border {{ $todayStatuses['summary'] ? 'opacity-50 pointer-events-none' : '' }}">
            <h2 class="font-bold text-lg">要約力</h2>
            <p class="text-sm text-gray-500">文章を短くまとめる力を鍛える</p>
            <p class="mt-3 text-sm font-bold">{{ $todayStatuses['summary'] ? '本日実施済み' : '15pt〜' }}</p>
        </a>

        <a href="{{ route('trainings.verbalization.create') }}"
           class="block p-5 bg-white rounded shadow border {{ $todayStatuses['verbalization'] ? 'opacity-50 pointer-events-none' : '' }}">
            <h2 class="font-bold text-lg">言語化力</h2>
            <p class="text-sm text-gray-500">考えや理由を言葉にする</p>
            <p class="mt-3 text-sm font-bold">{{ $todayStatuses['verbalization'] ? '本日実施済み' : '15pt〜' }}</p>
        </a>

        <a href="{{ route('trainings.abstraction.create') }}"
           class="block p-5 bg-white rounded shadow border {{ $todayStatuses['abstraction'] ? 'opacity-50 pointer-events-none' : '' }}">
            <h2 class="font-bold text-lg">抽象化力</h2>
            <p class="text-sm text-gray-500">具体例から本質を見つける</p>
            <p class="mt-3 text-sm font-bold">{{ $todayStatuses['abstraction'] ? '本日実施済み' : '15pt〜' }}</p>
        </a>

        <a href="{{ route('trainings.concretization.create') }}"
           class="block p-5 bg-white rounded shadow border {{ $todayStatuses['concretization'] ? 'opacity-50 pointer-events-none' : '' }}">
            <h2 class="font-bold text-lg">具体化力</h2>
            <p class="text-sm text-gray-500">抽象的な考えを行動に落とし込む</p>
            <p class="mt-3 text-sm font-bold">{{ $todayStatuses['concretization'] ? '本日実施済み' : '15pt〜' }}</p>
        </a>
    </div>

    <div class="mb-4">
        <a href="{{ route('trainings.ranking') }}" class="px-4 py-2 bg-yellow-500 text-white rounded">
            ランキングを見る
        </a>
    </div>

    <div class="bg-white rounded shadow overflow-x-auto">
        <table class="w-full text-sm">
            <thead class="bg-gray-100">
                <tr>
                    <th class="p-3 text-left">日付</th>
                    <th class="p-3 text-left">種類</th>
                    <th class="p-3 text-left">内容</th>
                    <th class="p-3 text-left">状態</th>
                    <th class="p-3 text-left">総合点</th>
                    <th class="p-3 text-left">ポイント</th>
                    <th class="p-3 text-left">詳細</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($trainings as $training)
                    <tr class="border-t">
                        <td class="p-3 whitespace-nowrap">
                            {{ $training['training_date']->format('Y-m-d') }}
                        </td>
                        <td class="p-3 whitespace-nowrap">
                            {{ $training['type_label'] }}
                        </td>
                        <td class="p-3">
                            {{ \Illuminate\Support\Str::limit($training['title'], 50) }}
                        </td>
                        <td class="p-3 whitespace-nowrap">
                            @if ($training['is_answered'])
                                <span class="px-2 py-1 bg-green-100 text-green-800 rounded text-xs">採点済み</span>
                            @else
                                <span class="px-2 py-1 bg-yellow-100 text-yellow-800 rounded text-xs">未回答</span>
                            @endif
                        </td>
                        <td class="p-3 whitespace-nowrap">
                            {{ $training['total_score'] ?? '-' }}{{ $training['total_score'] !== null ? '点' : '' }}
                        </td>
                        <td class="p-3 whitespace-nowrap">
                            {{ $training['earned_points'] }} pt
                        </td>
                        <td class="p-3 whitespace-nowrap">
                            <a href="{{ route('trainings.show', ['type' => $training['type'], 'id' => $training['id']]) }}"
                               class="text-blue-600 underline">
                                詳細
                            </a>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="p-6 text-center text-gray-500">
                            まだトレーニング履歴がありません。
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
