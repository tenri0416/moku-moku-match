@extends('layouts.admin')

@section('title', 'Laravelログ一覧')

@section('content')
<div class="py-8">
    <div class="mx-auto max-w-7xl px-4">
        <div class="mb-6">
            <h1 class="text-2xl font-bold text-slate-900">
                Laravelログ一覧
            </h1>
            <p class="mt-2 text-sm text-slate-600">
                日別のLaravelログを確認できます。
            </p>
        </div>

        <form method="GET" action="{{ route('admin.logs.index') }}" class="mb-6 rounded-xl bg-white p-5 shadow-sm ring-1 ring-slate-200">
            <label for="date" class="block text-sm font-bold text-slate-700">
                日付で検索
            </label>

            <div class="mt-2 flex flex-col gap-3 sm:flex-row">
                <input
                    type="date"
                    id="date"
                    name="date"
                    value="{{ $date }}"
                    class="rounded-lg border border-slate-300 px-4 py-2 text-sm"
                >

                <button
                    type="submit"
                    class="rounded-lg bg-slate-900 px-5 py-2 text-sm font-bold text-white hover:bg-slate-700"
                >
                    検索
                </button>

                <a
                    href="{{ route('admin.logs.index') }}"
                    class="inline-flex items-center justify-center rounded-lg border border-slate-300 bg-white px-5 py-2 text-sm font-bold text-slate-700 hover:bg-slate-50"
                >
                    リセット
                </a>
            </div>
        </form>

        <div class="overflow-hidden rounded-xl bg-white shadow-sm ring-1 ring-slate-200">
            <table class="min-w-full divide-y divide-slate-200">
                <thead class="bg-slate-100">
                    <tr>
                        <th class="px-4 py-3 text-left text-sm font-bold text-slate-700">
                            ファイル名
                        </th>
                        <th class="px-4 py-3 text-left text-sm font-bold text-slate-700">
                            日付
                        </th>
                        <th class="px-4 py-3 text-left text-sm font-bold text-slate-700">
                            サイズ
                        </th>
                        <th class="px-4 py-3 text-left text-sm font-bold text-slate-700">
                            更新日時
                        </th>
                        <th class="px-4 py-3 text-left text-sm font-bold text-slate-700">
                            操作
                        </th>
                    </tr>
                </thead>

                <tbody class="divide-y divide-slate-200">
                    @forelse ($logFiles as $logFile)
                        <tr>
                            <td class="px-4 py-3 text-sm font-semibold text-slate-900">
                                {{ $logFile['name'] }}
                            </td>
                            <td class="px-4 py-3 text-sm text-slate-600">
                                {{ $logFile['date'] ?? '-' }}
                            </td>
                            <td class="px-4 py-3 text-sm text-slate-600">
                                {{ $logFile['size'] }}
                            </td>
                            <td class="px-4 py-3 text-sm text-slate-600">
                                {{ $logFile['updated_at'] }}
                            </td>
                            <td class="px-4 py-3 text-sm">
                                <a
                                    href="{{ route('admin.logs.show', $logFile['name']) }}"
                                    class="font-bold text-indigo-600 hover:text-indigo-800"
                                >
                                    表示
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-4 py-8 text-center text-sm text-slate-500">
                                条件に一致するログファイルがありません。
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="mt-6">
            <a href="{{ route('admin.dashboard') }}" class="text-sm font-bold text-slate-600 hover:text-slate-900">
                管理者ダッシュボードへ戻る
            </a>
        </div>
    </div>
</div>
@endsection
