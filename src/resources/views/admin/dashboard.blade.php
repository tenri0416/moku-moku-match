@extends('layouts.admin')

@section('title', '管理者ダッシュボード')

@section('content')
<div class="min-h-screen bg-slate-50">
    <div class="mx-auto max-w-7xl px-4 py-10 sm:px-6 lg:px-8">
        {{-- Header --}}
        <div class="mb-8">
            <p class="text-sm font-bold text-indigo-600">ADMIN DASHBOARD</p>

            <h1 class="mt-2 text-3xl font-bold text-slate-900">
                管理者ダッシュボード
            </h1>

            <p class="mt-2 text-slate-600">
                ユーザー、募集、通報の状況を確認できます。
            </p>
        </div>

        {{-- Summary Cards --}}
        <div class="grid gap-4 md:grid-cols-3">
            <div class="rounded-2xl bg-white p-6 shadow-sm ring-1 ring-slate-200">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-bold text-slate-500">
                            登録ユーザー数
                        </p>

                        <p class="mt-2 text-4xl font-black text-slate-900">
                            {{ $userCount }}
                        </p>
                    </div>

                    <div class="flex h-14 w-14 items-center justify-center rounded-2xl bg-indigo-50 text-2xl">
                        👤
                    </div>
                </div>

                <div class="mt-5">
                    <a
                        href="{{ route('admin.users.index') }}"
                        class="text-sm font-bold text-indigo-600 hover:text-indigo-700"
                    >
                        ユーザー一覧を見る →
                    </a>
                </div>
            </div>

            <div class="rounded-2xl bg-white p-6 shadow-sm ring-1 ring-slate-200">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-bold text-slate-500">
                            募集投稿数
                        </p>

                        <p class="mt-2 text-4xl font-black text-slate-900">
                            {{ $workPostCount }}
                        </p>
                    </div>

                    <div class="flex h-14 w-14 items-center justify-center rounded-2xl bg-emerald-50 text-2xl">
                        📝
                    </div>
                </div>

                <div class="mt-5">
                    <a
                        href="{{ route('admin.work-posts.index') }}"
                        class="text-sm font-bold text-indigo-600 hover:text-indigo-700"
                    >
                        募集一覧を見る →
                    </a>
                </div>
            </div>

            <div class="rounded-2xl bg-white p-6 shadow-sm ring-1 ring-slate-200">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-bold text-slate-500">
                            未対応通報数
                        </p>

                        <p class="mt-2 text-4xl font-black text-rose-600">
                            {{ $openReportCount }}
                        </p>
                    </div>

                    <div class="flex h-14 w-14 items-center justify-center rounded-2xl bg-rose-50 text-2xl">
                        ⚠️
                    </div>
                </div>

                <div class="mt-5">
                    <a
                        href="{{ route('admin.reports.index') }}"
                        class="text-sm font-bold text-rose-600 hover:text-rose-700"
                    >
                        通報一覧を見る →
                    </a>
                </div>
            </div>
        </div>

        {{-- Main Area --}}
        <div class="mt-8 grid gap-8 lg:grid-cols-3">
            {{-- Latest Reports --}}
            <section class="rounded-2xl bg-white p-6 shadow-sm ring-1 ring-slate-200 lg:col-span-2">
                <div class="mb-5 flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
                    <div>
                        <h2 class="text-xl font-bold text-slate-900">
                            最新通報
                        </h2>

                        <p class="mt-1 text-sm text-slate-500">
                            最近届いた通報を確認できます。
                        </p>
                    </div>

                    <a
                        href="{{ route('admin.reports.index') }}"
                        class="inline-flex items-center justify-center rounded-xl border border-slate-300 bg-white px-4 py-2 text-sm font-bold text-slate-700 transition hover:bg-slate-50"
                    >
                        すべて見る
                    </a>
                </div>

                <div class="space-y-4">
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

                                    <h3 class="font-bold text-slate-900">
                                        {{ $report->reason }}
                                    </h3>

                                    <p class="mt-2 text-sm text-slate-600">
                                        通報者：
                                        <span class="font-semibold text-slate-800">
                                            {{ $report->reporter->profile->display_name ?? $report->reporter->name ?? '不明' }}
                                        </span>
                                    </p>

                                    @if ($report->workPost)
                                        <p class="mt-1 text-sm text-slate-600">
                                            対象募集：
                                            <span class="font-semibold text-slate-800">
                                                {{ $report->workPost->title }}
                                            </span>
                                        </p>
                                    @endif
                                </div>

                                <a
                                    href="{{ route('admin.reports.show', $report) }}"
                                    class="inline-flex shrink-0 items-center justify-center rounded-xl bg-indigo-600 px-4 py-2 text-sm font-bold text-white transition hover:bg-indigo-700"
                                >
                                    詳細
                                </a>
                            </div>
                        </article>
                    @empty
                        <div class="rounded-xl bg-slate-50 p-8 text-center">
                            <div class="mx-auto flex h-14 w-14 items-center justify-center rounded-full bg-white text-2xl shadow-sm">
                                🛡️
                            </div>

                            <h3 class="mt-4 text-lg font-bold text-slate-900">
                                通報はありません
                            </h3>

                            <p class="mt-2 text-sm text-slate-600">
                                現在、確認が必要な通報はありません。
                            </p>
                        </div>
                    @endforelse
                </div>
            </section>

            {{-- Quick Links --}}
            <aside class="space-y-6">
                <section class="rounded-2xl bg-white p-6 shadow-sm ring-1 ring-slate-200">
                    <h2 class="text-lg font-bold text-slate-900">
                        管理メニュー
                    </h2>

                    <div class="mt-5 space-y-3">
                        <a
                            href="{{ route('admin.users.index') }}"
                            class="flex items-center justify-between rounded-xl border border-slate-200 px-4 py-3 text-sm font-bold text-slate-700 transition hover:bg-slate-50"
                        >
                            <span>ユーザー管理</span>
                            <span class="text-slate-400">→</span>
                        </a>

                        <a
                            href="{{ route('admin.work-posts.index') }}"
                            class="flex items-center justify-between rounded-xl border border-slate-200 px-4 py-3 text-sm font-bold text-slate-700 transition hover:bg-slate-50"
                        >
                            <span>募集管理</span>
                            <span class="text-slate-400">→</span>
                        </a>

                        <a
                            href="{{ route('admin.reports.index') }}"
                            class="flex items-center justify-between rounded-xl border border-slate-200 px-4 py-3 text-sm font-bold text-slate-700 transition hover:bg-slate-50"
                        >
                            <span>通報管理</span>
                            <span class="text-slate-400">→</span>
                        </a>

                        <a
                            href="{{ route('home') }}"
                            class="flex items-center justify-between rounded-xl border border-slate-200 px-4 py-3 text-sm font-bold text-slate-700 transition hover:bg-slate-50"
                        >
                            <span>サイトへ戻る</span>
                            <span class="text-slate-400">→</span>
                        </a>
                    </div>
                </section>

                <section class="rounded-2xl border border-amber-100 bg-amber-50 p-6">
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
