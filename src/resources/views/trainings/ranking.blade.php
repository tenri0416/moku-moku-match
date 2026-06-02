@extends('layouts.app')

@section('content')
<div class="max-w-6xl mx-auto p-6">
    <div class="mb-6">
        <h1 class="text-2xl font-bold">トレーニングランキング</h1>
        <p class="text-sm text-gray-500">
            自己成長トレーニングのポイントランキングです。
        </p>
    </div>

    <div class="grid md:grid-cols-2 gap-6">
        <div class="bg-white rounded shadow p-5">
            <h2 class="text-xl font-bold mb-4">月間ランキング</h2>

            <table class="w-full text-sm">
                <thead class="bg-gray-100">
                    <tr>
                        <th class="p-2 text-left">順位</th>
                        <th class="p-2 text-left">ユーザー</th>
                        <th class="p-2 text-left">ポイント</th>
                        <th class="p-2 text-left">回数</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($monthlyRankings as $index => $ranking)
                        <tr class="border-t">
                            <td class="p-2">{{ $index + 1 }}位</td>
                            <td class="p-2">
                                <a href="{{ route('users.show', $ranking->user) }}" class="text-blue-600 hover:underline font-semibold">
                                    {{ $ranking->user->profile->display_name ?? $ranking->user->name }}
                                </a>
                            </td>
                            <td class="p-2 font-bold">{{ $ranking->total_points }} pt</td>
                            <td class="p-2">{{ $ranking->training_count }}回</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="p-4 text-center text-gray-500">
                                まだランキングデータがありません。
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="bg-white rounded shadow p-5">
            <h2 class="text-xl font-bold mb-4">総合ランキング</h2>

            <table class="w-full text-sm">
                <thead class="bg-gray-100">
                    <tr>
                        <th class="p-2 text-left">順位</th>
                        <th class="p-2 text-left">ユーザー</th>
                        <th class="p-2 text-left">ポイント</th>
                        <th class="p-2 text-left">回数</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($totalRankings as $index => $ranking)
                        <tr class="border-t">
                            <td class="p-2">{{ $index + 1 }}位</td>
                            <td class="p-2">
                                <a href="{{ route('users.show', $ranking->user) }}" class="text-blue-600 hover:underline font-semibold">
                                    {{ $ranking->user->profile->display_name ?? $ranking->user->name }}
                                </a>
                            </td>
                            <td class="p-2 font-bold">{{ $ranking->total_points }} pt</td>
                            <td class="p-2">{{ $ranking->training_count }}回</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="p-4 text-center text-gray-500">
                                まだランキングデータがありません。
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <div class="mt-6">
        <a href="{{ route('trainings.index') }}" class="px-5 py-2 border rounded">
            トレーニング一覧に戻る
        </a>
    </div>
</div>
@endsection
