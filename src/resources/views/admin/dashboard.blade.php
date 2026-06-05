@extends('layouts.admin')

@section('title', '管理者ダッシュボード')

@section('content')
<div class="min-h-screen bg-slate-50">
    <div class="mx-auto w-full max-w-7xl px-3 py-6 sm:px-6 sm:py-10 lg:px-8">
        {{-- Header --}}
        <div class="mb-6 sm:mb-8">
            <p class="text-xs font-bold tracking-wide text-indigo-600 sm:text-sm">ADMIN DASHBOARD</p>

            <h1 class="mt-2 break-words text-2xl font-bold leading-tight text-slate-900 sm:text-3xl">
                管理者ダッシュボード
            </h1>

            <p class="mt-2 text-sm leading-6 text-slate-600 sm:text-base">
                ユーザー、募集、通報の状況を確認できます。
            </p>
        </div>

        {{-- Summary Cards --}}
        <div class="grid grid-cols-1 gap-3 sm:gap-4 md:grid-cols-3">
            <div class="rounded-2xl bg-white p-4 shadow-sm ring-1 ring-slate-200 sm:p-6">
                <div class="flex items-center justify-between gap-3">
                    <div class="min-w-0">
                        <p class="text-xs font-bold text-slate-500 sm:text-sm">
                            登録ユーザー数
                        </p>

                        <p class="mt-2 text-3xl font-black text-slate-900 sm:text-4xl">
                            {{ $userCount }}
                        </p>
                    </div>

                    <div class="flex h-12 w-12 shrink-0 items-center justify-center rounded-2xl bg-indigo-50 text-xl sm:h-14 sm:w-14 sm:text-2xl">
                        👤
                    </div>
                </div>

                <div class="mt-5">
                    <a
                        href="{{ route('admin.users.index') }}"
                        class="inline-flex text-sm font-bold text-indigo-600 hover:text-indigo-700"
                    >
                        ユーザー一覧を見る →
                    </a>
                </div>
            </div>

            <div class="rounded-2xl bg-white p-4 shadow-sm ring-1 ring-slate-200 sm:p-6">
                <div class="flex items-center justify-between gap-3">
                    <div class="min-w-0">
                        <p class="text-xs font-bold text-slate-500 sm:text-sm">
                            募集投稿数
                        </p>

                        <p class="mt-2 text-3xl font-black text-slate-900 sm:text-4xl">
                            {{ $workPostCount }}
                        </p>
                    </div>

                    <div class="flex h-12 w-12 shrink-0 items-center justify-center rounded-2xl bg-emerald-50 text-xl sm:h-14 sm:w-14 sm:text-2xl">
                        📝
                    </div>
                </div>
            </div>

            <div class="rounded-2xl bg-white p-4 shadow-sm ring-1 ring-slate-200 sm:p-6">
                <div class="flex items-center justify-between gap-3">
                    <div class="min-w-0">
                        <p class="text-xs font-bold text-slate-500 sm:text-sm">
                            未対応通報数
                        </p>

                        <p class="mt-2 text-3xl font-black text-rose-600 sm:text-4xl">
                            {{ $openReportCount }}
                        </p>
                    </div>

                    <div class="flex h-12 w-12 shrink-0 items-center justify-center rounded-2xl bg-rose-50 text-xl sm:h-14 sm:w-14 sm:text-2xl">
                        ⚠️
                    </div>
                </div>

                <div class="mt-5">
                    <a
                        href="{{ route('admin.reports.index') }}"
                        class="inline-flex text-sm font-bold text-rose-600 hover:text-rose-700"
                    >
                        通報一覧を見る →
                    </a>
                </div>
            </div>
        </div>

        {{-- Main Area --}}
        <div class="mt-6 grid grid-cols-1 gap-5 lg:mt-8 lg:grid-cols-3 lg:gap-8">
            {{-- Latest Reports --}}
            <section class="min-w-0 rounded-2xl bg-white p-4 shadow-sm ring-1 ring-slate-200 sm:p-6 lg:col-span-2">
                <div class="mb-5 flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
                    <div class="min-w-0">
                        <h2 class="text-lg font-bold text-slate-900 sm:text-xl">
                            最新通報
                        </h2>

                        <p class="mt-1 text-sm leading-6 text-slate-500">
                            最近届いた通報を確認できます。
                        </p>
                    </div>

                    <a
                        href="{{ route('admin.reports.index') }}"
                        class="inline-flex w-full items-center justify-center rounded-xl border border-slate-300 bg-white px-4 py-3 text-sm font-bold text-slate-700 transition hover:bg-slate-50 sm:w-auto sm:py-2"
                    >
                        すべて見る
                    </a>
                </div>

                <div class="space-y-3 sm:space-y-4">
                    @forelse ($latestReports as $report)
                        <article class="rounded-xl border border-slate-200 p-4 transition hover:bg-slate-50">
                            <div class="flex flex-col gap-4 sm:flex-row sm:items-start sm:justify-between">
                                <div class="min-w-0">
                                    <div class="mb-2 flex flex-wrap gap-2">
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

                                        <span class="rounded-full bg-slate-100 px-3 py-1 text-xs font-bold text-slate-600">
                                            {{ $report->created_at->format('Y/m/d H:i') }}
                                        </span>
                                    </div>

                                    <h3 class="break-words font-bold leading-6 text-slate-900">
                                        {{ $report->reason }}
                                    </h3>

                                    <p class="mt-2 break-words text-sm leading-6 text-slate-600">
                                        通報者：
                                        <span class="font-semibold text-slate-800">
                                            {{ $report->reporter->profile->display_name ?? $report->reporter->name ?? '不明' }}
                                        </span>
                                    </p>

                                    @if ($report->workPost)
                                        <p class="mt-1 break-words text-sm leading-6 text-slate-600">
                                            対象募集：
                                            <span class="font-semibold text-slate-800">
                                                {{ $report->workPost->title }}
                                            </span>
                                        </p>
                                    @endif
                                </div>

                                <a
                                    href="{{ route('admin.reports.show', $report) }}"
                                    class="inline-flex w-full shrink-0 items-center justify-center rounded-xl bg-indigo-600 px-4 py-3 text-sm font-bold text-white transition hover:bg-indigo-700 sm:w-auto sm:py-2"
                                >
                                    詳細
                                </a>
                            </div>
                        </article>
                    @empty
                        <div class="rounded-xl bg-slate-50 p-6 text-center sm:p-8">
                            <div class="mx-auto flex h-14 w-14 items-center justify-center rounded-full bg-white text-2xl shadow-sm">
                                🛡️
                            </div>

                            <h3 class="mt-4 text-lg font-bold text-slate-900">
                                通報はありません
                            </h3>

                            <p class="mt-2 text-sm leading-6 text-slate-600">
                                現在、確認が必要な通報はありません。
                            </p>
                        </div>
                    @endforelse
                </div>
            </section>

            {{-- Quick Links --}}
            <aside class="min-w-0 space-y-5 lg:space-y-6">
                <section class="rounded-2xl bg-white p-4 shadow-sm ring-1 ring-slate-200 sm:p-6">
                    <h2 class="text-lg font-bold text-slate-900">
                        管理メニュー
                    </h2>

                    <div class="mt-5 space-y-3">
                        <a
                            href="{{ route('admin.users.index') }}"
                            class="flex items-center justify-between gap-3 rounded-xl border border-slate-200 px-4 py-3 text-sm font-bold text-slate-700 transition hover:bg-slate-50"
                        >
                            <span class="min-w-0 break-words">ユーザー管理</span>
                            <span class="shrink-0 text-slate-400">→</span>
                        </a>

                        <a
                            href="{{ route('admin.work-posts.index') }}"
                            class="flex items-center justify-between gap-3 rounded-xl border border-slate-200 px-4 py-3 text-sm font-bold text-slate-700 transition hover:bg-slate-50"
                        >
                            <span class="min-w-0 break-words">募集管理</span>
                            <span class="shrink-0 text-slate-400">→</span>
                        </a>

                        <a
                            href="{{ route('admin.reports.index') }}"
                            class="flex items-center justify-between gap-3 rounded-xl border border-slate-200 px-4 py-3 text-sm font-bold text-slate-700 transition hover:bg-slate-50"
                        >
                            <span class="min-w-0 break-words">通報管理</span>
                            <span class="shrink-0 text-slate-400">→</span>
                        </a>

                        <a
                            href="{{ route('home') }}"
                            class="flex items-center justify-between gap-3 rounded-xl border border-slate-200 px-4 py-3 text-sm font-bold text-slate-700 transition hover:bg-slate-50"
                        >
                            <span class="min-w-0 break-words">サイトへ戻る</span>
                            <span class="shrink-0 text-slate-400">→</span>
                        </a>
                    </div>
                </section>

                <section class="rounded-2xl border border-amber-100 bg-amber-50 p-4 sm:p-6">
                    <h2 class="text-lg font-bold text-amber-900">
                        管理時の注意
                    </h2>

                    <ul class="mt-3 space-y-2 text-sm leading-7 text-amber-800">
                        <li>・通報内容は対象ユーザーや募集内容を確認してから対応してください。</li>
                        <li>・ユーザー停止や募集非公開は、誤操作に注意してください。</li>
                        <li>・緊急性の高い通報は優先して確認してください。</li>
                    </ul>
                </section>
            </aside>
        </div>
    </div>
</div>
@endsection
