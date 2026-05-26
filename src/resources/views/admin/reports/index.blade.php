@extends('layouts.admin')

@section('title', '通報一覧')

@section('content')
<div class="min-h-screen bg-slate-50">
    <div class="mx-auto max-w-7xl px-4 py-10 sm:px-6 lg:px-8">
        {{-- Header --}}
        <div class="mb-8 flex flex-col gap-4 sm:flex-row sm:items-end sm:justify-between">
            <div>
                <p class="text-sm font-bold text-rose-600">ADMIN REPORTS</p>

                <h1 class="mt-2 text-3xl font-bold text-slate-900">
                    通報一覧
                </h1>

                <p class="mt-2 text-slate-600">
                    ユーザーから届いた通報内容を確認し、対応状況を管理します。
                </p>
            </div>

            @if (Route::has('admin.reports.create'))
                <a
                    href="{{ route('admin.reports.create') }}"
                    class="inline-flex items-center justify-center rounded-xl bg-rose-600 px-5 py-3 text-sm font-bold text-white shadow-sm transition hover:bg-rose-700"
                >
                    通報作成
                </a>
            @endif
        </div>

        {{-- Summary --}}
        <div class="mb-8 grid gap-4 md:grid-cols-3">
            <div class="rounded-2xl bg-white p-5 shadow-sm ring-1 ring-slate-200">
                <p class="text-sm font-bold text-slate-500">総通報数</p>
                <p class="mt-2 text-3xl font-black text-slate-900">
                    {{ method_exists($reports, 'total') ? $reports->total() : $reports->count() }}
                </p>
            </div>

            <div class="rounded-2xl bg-white p-5 shadow-sm ring-1 ring-slate-200">
                <p class="text-sm font-bold text-slate-500">未対応</p>
                <p class="mt-2 text-3xl font-black text-rose-600">
                    {{ $reports->where('status', 1)->count() }}
                </p>
            </div>

            <div class="rounded-2xl bg-white p-5 shadow-sm ring-1 ring-slate-200">
                <p class="text-sm font-bold text-slate-500">対応中・対応済み</p>
                <p class="mt-2 text-3xl font-black text-indigo-600">
                    {{ $reports->whereIn('status', [2, 3])->count() }}
                </p>
            </div>
        </div>

        {{-- Table --}}
        <div class="overflow-hidden rounded-2xl bg-white shadow-sm ring-1 ring-slate-200">
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-slate-200">
                    <thead class="bg-slate-50">
                        <tr>
                            <th class="px-5 py-4 text-left text-xs font-bold uppercase tracking-wide text-slate-500">
                                ID
                            </th>
                            <th class="px-5 py-4 text-left text-xs font-bold uppercase tracking-wide text-slate-500">
                                通報者
                            </th>
                            <th class="px-5 py-4 text-left text-xs font-bold uppercase tracking-wide text-slate-500">
                                対象
                            </th>
                            <th class="px-5 py-4 text-left text-xs font-bold uppercase tracking-wide text-slate-500">
                                理由
                            </th>
                            <th class="px-5 py-4 text-left text-xs font-bold uppercase tracking-wide text-slate-500">
                                ステータス
                            </th>
                            <th class="px-5 py-4 text-left text-xs font-bold uppercase tracking-wide text-slate-500">
                                通報日時
                            </th>
                            <th class="px-5 py-4 text-right text-xs font-bold uppercase tracking-wide text-slate-500">
                                操作
                            </th>
                        </tr>
                    </thead>

                    <tbody class="divide-y divide-slate-200 bg-white">
                        @forelse ($reports as $report)
                            <tr class="hover:bg-slate-50">
                                <td class="whitespace-nowrap px-5 py-4 text-sm font-semibold text-slate-900">
                                    #{{ $report->id }}
                                </td>

                                <td class="whitespace-nowrap px-5 py-4 text-sm text-slate-700">
                                    {{ $report->reporter->profile->display_name ?? $report->reporter->name ?? '不明' }}
                                </td>

                                <td class="px-5 py-4 text-sm text-slate-700">
                                    @if ($report->reportedUser)
                                        <p>
                                            ユーザー：
                                            <span class="font-semibold">
                                                {{ $report->reportedUser->profile->display_name ?? $report->reportedUser->name }}
                                            </span>
                                        </p>
                                    @endif

                                    @if ($report->workPost)
                                        <p class="mt-1">
                                            募集：
                                            <span class="font-semibold">
                                                {{ $report->workPost->title }}
                                            </span>
                                        </p>
                                    @endif

                                    @if (! $report->reportedUser && ! $report->workPost)
                                        <span class="text-slate-400">対象なし</span>
                                    @endif
                                </td>

                                <td class="max-w-xs px-5 py-4 text-sm text-slate-700">
                                    <span class="line-clamp-2">
                                        {{ $report->reason }}
                                    </span>
                                </td>

                                <td class="whitespace-nowrap px-5 py-4">
                                    @if ($report->status === 1)
                                        <span class="rounded-full bg-rose-50 px-3 py-1 text-xs font-bold text-rose-700">
                                            未対応
                                        </span>
                                    @elseif ($report->status === 2)
                                        <span class="rounded-full bg-amber-50 px-3 py-1 text-xs font-bold text-amber-700">
                                            対応中
                                        </span>
                                    @elseif ($report->status === 3)
                                        <span class="rounded-full bg-emerald-50 px-3 py-1 text-xs font-bold text-emerald-700">
                                            対応済み
                                        </span>
                                    @else
                                        <span class="rounded-full bg-slate-100 px-3 py-1 text-xs font-bold text-slate-600">
                                            不明
                                        </span>
                                    @endif
                                </td>

                                <td class="whitespace-nowrap px-5 py-4 text-sm text-slate-500">
                                    {{ $report->created_at->format('Y/m/d H:i') }}
                                </td>

                                <td class="whitespace-nowrap px-5 py-4 text-right">
                                    <a
                                        href="{{ route('admin.reports.show', $report) }}"
                                        class="inline-flex items-center justify-center rounded-xl bg-indigo-600 px-4 py-2 text-sm font-bold text-white transition hover:bg-indigo-700"
                                    >
                                        詳細
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="px-5 py-12 text-center">
                                    <div class="mx-auto flex h-14 w-14 items-center justify-center rounded-full bg-slate-100 text-2xl">
                                        🛡️
                                    </div>

                                    <h2 class="mt-4 text-lg font-bold text-slate-900">
                                        通報はありません
                                    </h2>

                                    <p class="mt-2 text-sm text-slate-600">
                                        現在、確認が必要な通報はありません。
                                    </p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        @if (method_exists($reports, 'links'))
            <div class="mt-8">
                {{ $reports->links() }}
            </div>
        @endif
    </div>
</div>
@endsection
