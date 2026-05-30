@extends('layouts.admin')

@section('title', $table . ' テーブル')

@section('content')
<div class="min-h-screen bg-slate-50 py-8">
    <div class="mx-auto max-w-7xl px-4">
        <div class="mb-6 flex flex-col gap-3 sm:flex-row sm:items-end sm:justify-between">
            <div>
                <h1 class="text-2xl font-bold text-slate-900">
                    {{ $table }} テーブル
                </h1>
                <p class="mt-2 text-sm text-slate-600">
                    閲覧専用です。最新順で表示しています。
                </p>
            </div>

            <a
                href="{{ route('admin.database.index') }}"
                class="inline-flex items-center justify-center rounded-lg border border-slate-300 bg-white px-4 py-2 text-sm font-bold text-slate-700 hover:bg-slate-100"
            >
                テーブル一覧へ戻る
            </a>
        </div>

        <div class="overflow-x-auto rounded-xl bg-white shadow-sm ring-1 ring-slate-200">
            <table class="min-w-full divide-y divide-slate-200">
                <thead class="bg-slate-100">
                    <tr>
                        @foreach ($columns as $column)
                            <th class="whitespace-nowrap px-4 py-3 text-left text-sm font-bold text-slate-700">
                                {{ $column }}
                            </th>
                        @endforeach
                    </tr>
                </thead>

                <tbody class="divide-y divide-slate-200">
                    @forelse ($rows as $row)
                        <tr>
                            @foreach ($columns as $column)
                                <td class="max-w-xs truncate whitespace-nowrap px-4 py-3 text-sm text-slate-700">
                                    @php
                                        $value = $row->{$column} ?? null;
                                    @endphp

                                    @if (is_null($value))
                                        <span class="text-slate-400">NULL</span>
                                    @elseif (is_string($value) && strlen($value) > 100)
                                        {{ Str::limit($value, 100) }}
                                    @else
                                        {{ $value }}
                                    @endif
                                </td>
                            @endforeach
                        </tr>
                    @empty
                        <tr>
                            <td colspan="{{ $columns->count() }}" class="px-4 py-8 text-center text-sm text-slate-500">
                                データがありません。
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="mt-6">
            {{ $rows->links() }}
        </div>
    </div>
</div>
@endsection
