@extends('layouts.admin')

@section('title', 'ログ一覧')

@section('content')
<div class="py-8">
    <div class="mx-auto max-w-7xl px-4">
        <div class="mb-6">
            <h1 class="text-2xl font-bold text-slate-900">
                ログ一覧
            </h1>
            <p class="mt-2 text-sm text-slate-600">
                エラーログと通常ログを切り替えて確認できます。
            </p>
        </div>

        <form method="GET" action="{{ route('admin.logs.index') }}" class="mb-6 rounded-xl bg-white p-5 shadow-sm ring-1 ring-slate-200">
            <div class="grid gap-4 md:grid-cols-3">
                <div>
                    <label for="type" class="block text-sm font-bold text-slate-700">
                        ログ種別
                    </label>

                    <select
                        id="type"
                        name="type"
                        class="mt-2 w-full rounded-lg border border-slate-300 px-4 py-2 text-sm"
                    >
                        @foreach ($logTypes as $logTypeValue => $logTypeLabel)
                            <option value="{{ $logTypeValue }}" @selected($type === $logTypeValue)>
                                {{ $logTypeLabel }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label for="date" class="block text-sm font-bold text-slate-700">
                        日付
                    </label>

                    <input
                        type="date"
                        id="date"
                        name="date"
                        value="{{ $date }}"
                        class="mt-2 w-full rounded-lg border border-slate-300 px-4 py-2 text-sm"
                    >
                </div>

                <div class="flex items-end gap-3">
                    <button
                        type="submit"
                        class="inline-flex h-10 items-center justify-center rounded-lg bg-slate-900 px-5 text-sm font-bold text-white hover:bg-slate-700"
                    >
                        検索
                    </button>

                    <a
                        href="{{ route('admin.logs.index') }}"
                        class="inline-flex h-10 items-center justify-center rounded-lg border border-slate-300 bg-white px-5 text-sm font-bold text-slate-700 hover:bg-slate-50"
                    >
                        リセット
                    </a>
                </div>
            </div>
        </form>

        <div class="mb-4 grid gap-4 md:grid-cols-2">
            <a
                href="{{ route('admin.logs.index', ['type' => 'error', 'date' => $date]) }}"
                class="rounded-xl border p-4 shadow-sm transition
                    {{ $type === 'error' ? 'border-rose-200 bg-rose-50' : 'border-slate-200 bg-white hover:bg-slate-50' }}"
            >
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-bold {{ $type === 'error' ? 'text-rose-700' : 'text-slate-700' }}">
                            エラーログ
                        </p>
                        <p class="mt-1 text-xs text-slate-500">
                            error / critical / alert / emergency など
                        </p>
                    </div>
                    <span class="rounded-full px-3 py-1 text-xs font-bold
                        {{ $type === 'error' ? 'bg-rose-600 text-white' : 'bg-slate-100 text-slate-600' }}">
                        error
                    </span>
                </div>
            </a>

            <a
                href="{{ route('admin.logs.index', ['type' => 'laravel', 'date' => $date]) }}"
                class="rounded-xl border p-4 shadow-sm transition
                    {{ $type === 'laravel' ? 'border-indigo-200 bg-indigo-50' : 'border-slate-200 bg-white hover:bg-slate-50' }}"
            >
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-bold {{ $type === 'laravel' ? 'text-indigo-700' : 'text-slate-700' }}">
                            通常ログ
                        </p>
                        <p class="mt-1 text-xs text-slate-500">
                            API動作ログ・画面アクセスログ・info / warning など
                        </p>
                    </div>
                    <span class="rounded-full px-3 py-1 text-xs font-bold
                        {{ $type === 'laravel' ? 'bg-indigo-600 text-white' : 'bg-slate-100 text-slate-600' }}">
                        laravel
                    </span>
                </div>
            </a>
        </div>

        <div class="overflow-hidden rounded-xl bg-white shadow-sm ring-1 ring-slate-200">
            <table class="min-w-full divide-y divide-slate-200">
                <thead class="bg-slate-100">
                    <tr>
                        <th class="px-4 py-3 text-left text-sm font-bold text-slate-700">
                            種別
                        </th>
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
                            <td class="px-4 py-3 text-sm">
                                @if ($logFile['type'] === 'error')
                                    <span class="inline-flex rounded-full bg-rose-100 px-3 py-1 text-xs font-bold text-rose-700">
                                        {{ $logFile['type_label'] }}
                                    </span>
                                @else
                                    <span class="inline-flex rounded-full bg-indigo-100 px-3 py-1 text-xs font-bold text-indigo-700">
                                        {{ $logFile['type_label'] }}
                                    </span>
                                @endif
                            </td>

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
                                    href="{{ route('admin.logs.show', [
                                        'file' => $logFile['name'],
                                        'type' => $type,
                                        'date' => $date,
                                    ]) }}"
                                    class="font-bold text-indigo-600 hover:text-indigo-800"
                                >
                                    表示
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-4 py-8 text-center text-sm text-slate-500">
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
