@extends('layouts.app')

@section('title', 'DBテーブル一覧')

@section('content')
<div class="min-h-screen bg-slate-50 py-8">
    <div class="mx-auto max-w-6xl px-4">
        <div class="mb-6">
            <h1 class="text-2xl font-bold text-slate-900">
                DBテーブル一覧
            </h1>
            <p class="mt-2 text-sm text-slate-600">
                管理者用の閲覧専用画面です。編集・削除はできません。
            </p>
        </div>

        <div class="overflow-hidden rounded-xl bg-white shadow-sm ring-1 ring-slate-200">
            <table class="min-w-full divide-y divide-slate-200">
                <thead class="bg-slate-100">
                    <tr>
                        <th class="px-4 py-3 text-left text-sm font-bold text-slate-700">
                            テーブル名
                        </th>
                        <th class="px-4 py-3 text-left text-sm font-bold text-slate-700">
                            件数
                        </th>
                        <th class="px-4 py-3 text-left text-sm font-bold text-slate-700">
                            操作
                        </th>
                    </tr>
                </thead>

                <tbody class="divide-y divide-slate-200">
                    @foreach ($tables as $table)
                        <tr>
                            <td class="px-4 py-3 text-sm font-semibold text-slate-900">
                                {{ $table }}
                            </td>
                            <td class="px-4 py-3 text-sm text-slate-600">
                                {{ number_format($tableCounts[$table] ?? 0) }} 件
                            </td>
                            <td class="px-4 py-3 text-sm">
                                <a
                                    href="{{ route('admin.database.show', $table) }}"
                                    class="font-bold text-indigo-600 hover:text-indigo-800"
                                >
                                    表示
                                </a>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <div class="mt-6">
            <a
                href="{{ route('admin.dashboard') }}"
                class="text-sm font-bold text-slate-600 hover:text-slate-900"
            >
                管理者ダッシュボードへ戻る
            </a>
        </div>
    </div>
</div>
@endsection
