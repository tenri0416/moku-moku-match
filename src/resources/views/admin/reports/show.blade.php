@extends('layouts.admin')

@section('title', '通報詳細')

@section('content')
<div class="min-h-screen bg-slate-50">
    <div class="mx-auto max-w-5xl px-4 py-10 sm:px-6 lg:px-8">
        {{-- Header --}}
        <div class="mb-8 flex flex-col gap-4 sm:flex-row sm:items-start sm:justify-between">
            <div>
                <p class="text-sm font-bold text-rose-600">REPORT DETAIL</p>

                <h1 class="mt-2 text-3xl font-bold text-slate-900">
                    通報詳細 #{{ $report->id }}
                </h1>

                <p class="mt-2 text-slate-600">
                    通報内容を確認し、対応状況を変更できます。
                </p>
            </div>

            <a
                href="{{ route('admin.reports.index') }}"
                class="inline-flex items-center justify-center rounded-xl border border-slate-300 bg-white px-5 py-3 text-sm font-bold text-slate-700 transition hover:bg-slate-50"
            >
                通報一覧へ戻る
            </a>
        </div>

        <div class="grid gap-6 lg:grid-cols-3">
            {{-- Main --}}
            <div class="space-y-6 lg:col-span-2">
                {{-- Report Content --}}
                <section class="rounded-2xl bg-white p-6 shadow-sm ring-1 ring-slate-200 sm:p-8">
                    <div class="mb-5 flex flex-wrap items-center gap-2">
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

                    <h2 class="text-xl font-bold text-slate-900">
                        通報理由
                    </h2>

                    <div class="mt-4 rounded-xl bg-rose-50 p-5 text-rose-950">
                        {{ $report->reason }}
                    </div>

                    <h2 class="mt-8 text-xl font-bold text-slate-900">
                        詳細内容
                    </h2>

                    <div class="mt-4 rounded-xl bg-slate-50 p-5 leading-8 text-slate-700">
                        {!! nl2br(e($report->body ?? '詳細内容はありません。')) !!}
                    </div>
                </section>

                {{-- Target Info --}}
                <section class="rounded-2xl bg-white p-6 shadow-sm ring-1 ring-slate-200 sm:p-8">
                    <h2 class="text-xl font-bold text-slate-900">
                        通報対象
                    </h2>

                    <div class="mt-5 grid gap-4 md:grid-cols-2">
                        <div class="rounded-xl bg-slate-50 p-4">
                            <p class="text-sm font-bold text-slate-500">
                                通報対象ユーザー
                            </p>

                            <p class="mt-2 font-semibold text-slate-900">
                                @if ($report->reportedUser)
                                    {{ $report->reportedUser->profile->display_name ?? $report->reportedUser->name }}
                                @else
                                    なし
                                @endif
                            </p>

                            @if ($report->reportedUser)
                                <p class="mt-1 text-sm text-slate-500">
                                    ID：{{ $report->reportedUser->id }}
                                </p>
                            @endif
                        </div>

                        <div class="rounded-xl bg-slate-50 p-4">
                            <p class="text-sm font-bold text-slate-500">
                                対象募集
                            </p>

                            <p class="mt-2 font-semibold text-slate-900">
                                {{ $report->workPost->title ?? 'なし' }}
                            </p>

                            @if ($report->workPost)
                                <p class="mt-1 text-sm text-slate-500">
                                    ID：{{ $report->workPost->id }}
                                </p>

                                <a
                                    href="{{ route('admin.work-posts.show', $report->workPost) }}"
                                    class="mt-3 inline-flex text-sm font-bold text-indigo-600 hover:text-indigo-700"
                                >
                                    募集詳細を見る →
                                </a>
                            @endif
                        </div>
                    </div>
                </section>
            </div>

            {{-- Side --}}
            <aside class="space-y-6">
                {{-- Reporter --}}
                <section class="rounded-2xl bg-white p-6 shadow-sm ring-1 ring-slate-200">
                    <h2 class="text-lg font-bold text-slate-900">
                        通報者
                    </h2>

                    <dl class="mt-5 space-y-4 text-sm">
                        <div>
                            <dt class="font-bold text-slate-500">
                                表示名
                            </dt>
                            <dd class="mt-1 text-slate-900">
                                {{ $report->reporter->profile->display_name ?? $report->reporter->name ?? '不明' }}
                            </dd>
                        </div>

                        <div>
                            <dt class="font-bold text-slate-500">
                                ユーザーID
                            </dt>
                            <dd class="mt-1 text-slate-900">
                                {{ $report->reporter->id ?? '不明' }}
                            </dd>
                        </div>

                        <div>
                            <dt class="font-bold text-slate-500">
                                通報日時
                            </dt>
                            <dd class="mt-1 text-slate-900">
                                {{ $report->created_at->format('Y/m/d H:i') }}
                            </dd>
                        </div>
                    </dl>
                </section>

                {{-- Actions --}}
                <section class="rounded-2xl bg-white p-6 shadow-sm ring-1 ring-slate-200">
                    <h2 class="text-lg font-bold text-slate-900">
                        対応操作
                    </h2>

                    <div class="mt-5 space-y-3">
                        @if ($report->status !== 2)
                            <form method="POST" action="{{ route('admin.reports.in-progress', $report) }}">
                                @csrf
                                @method('PATCH')

                                <button
                                    type="submit"
                                    class="flex w-full items-center justify-center rounded-xl bg-amber-500 px-5 py-3 text-sm font-bold text-white shadow-sm transition hover:bg-amber-600"
                                >
                                    対応中にする
                                </button>
                            </form>
                        @endif

                        @if ($report->status !== 3)
                            <form method="POST" action="{{ route('admin.reports.close', $report) }}">
                                @csrf
                                @method('PATCH')

                                <button
                                    type="submit"
                                    class="flex w-full items-center justify-center rounded-xl bg-emerald-600 px-5 py-3 text-sm font-bold text-white shadow-sm transition hover:bg-emerald-700"
                                >
                                    対応済みにする
                                </button>
                            </form>
                        @endif

                        <a
                            href="{{ route('admin.reports.index') }}"
                            class="flex w-full items-center justify-center rounded-xl border border-slate-300 bg-white px-5 py-3 text-sm font-bold text-slate-700 transition hover:bg-slate-50"
                        >
                            一覧へ戻る
                        </a>
                    </div>
                </section>
            </aside>
        </div>
    </div>
</div>
@endsection
