@extends('layouts.app')

@section('title', '参加申請一覧')

@section('content')
<div class="min-h-screen bg-slate-50">
    <div class="mx-auto max-w-6xl px-4 py-10 sm:px-6 lg:px-8">
        {{-- Header --}}
        <div class="mb-8">
            <p class="text-sm font-bold text-indigo-600">APPLICATIONS</p>

            <h1 class="mt-2 text-3xl font-bold text-slate-900">
                参加申請一覧
            </h1>

            <p class="mt-2 text-slate-600">
                自分の募集に届いた参加申請を確認し、承認または否認できます。
            </p>
        </div>

        {{-- Work Post Summary --}}
        <section class="mb-8 rounded-2xl bg-white p-6 shadow-sm ring-1 ring-slate-200 sm:p-8">
            <div class="flex flex-col gap-4 sm:flex-row sm:items-start sm:justify-between">
                <div>
                    <p class="text-sm font-bold text-slate-500">
                        対象募集
                    </p>

                    <h2 class="mt-2 text-2xl font-bold text-slate-900">
                        {{ $workPost->title }}
                    </h2>

                    <div class="mt-4 flex flex-wrap gap-2">
                        @if ($workPost->status === 1)
                            <span class="rounded-full bg-emerald-50 px-3 py-1 text-xs font-bold text-emerald-700">
                                募集中
                            </span>
                        @elseif ($workPost->status === 2)
                            <span class="rounded-full bg-slate-100 px-3 py-1 text-xs font-bold text-slate-600">
                                終了
                            </span>
                        @else
                            <span class="rounded-full bg-rose-50 px-3 py-1 text-xs font-bold text-rose-700">
                                非公開
                            </span>
                        @endif

                        <span class="rounded-full bg-indigo-50 px-3 py-1 text-xs font-bold text-indigo-700">
                            {{ $workPost->purpose }}
                        </span>
                    </div>
                </div>

                <a
                    href="{{ route('work-posts.show', $workPost) }}"
                    class="inline-flex items-center justify-center rounded-xl border border-slate-300 bg-white px-5 py-3 text-sm font-bold text-slate-700 transition hover:bg-slate-50"
                >
                    募集詳細へ戻る
                </a>
            </div>
        </section>

        {{-- Applications --}}
        <section>
            <div class="mb-5 flex items-center justify-between">
                <h2 class="text-xl font-bold text-slate-900">
                    申請者
                </h2>

                <p class="text-sm text-slate-500">
                    {{ $applications->count() }}件
                </p>
            </div>

            <div class="space-y-5">
                @forelse ($applications as $application)
                    <article class="rounded-2xl bg-white p-6 shadow-sm ring-1 ring-slate-200 sm:p-8">
                        <div class="flex flex-col gap-6 lg:flex-row lg:items-start lg:justify-between">
                            <div class="min-w-0 flex-1">
                                <div class="flex flex-wrap items-center gap-2">
                                    @if ($application->status === 1)
                                        <span class="rounded-full bg-amber-50 px-3 py-1 text-xs font-bold text-amber-700">
                                            承認待ち
                                        </span>
                                    @elseif ($application->status === 2)
                                        <span class="rounded-full bg-emerald-50 px-3 py-1 text-xs font-bold text-emerald-700">
                                            承認済み
                                        </span>
                                    @elseif ($application->status === 3)
                                        <span class="rounded-full bg-rose-50 px-3 py-1 text-xs font-bold text-rose-700">
                                            否認
                                        </span>
                                    @else
                                        <span class="rounded-full bg-slate-100 px-3 py-1 text-xs font-bold text-slate-600">
                                            不明
                                        </span>
                                    @endif

                                    <span class="rounded-full bg-slate-100 px-3 py-1 text-xs font-bold text-slate-600">
                                        {{ $application->created_at->format('Y/m/d H:i') }}
                                    </span>
                                </div>

                                <h3 class="mt-4 text-xl font-bold text-slate-900">
                                    {{ $application->user->profile->display_name ?? $application->user->name }}
                                </h3>

                                <dl class="mt-5 grid gap-4 text-sm md:grid-cols-2">
                                    <div class="rounded-xl bg-slate-50 p-4">
                                        <dt class="font-bold text-slate-500">職種</dt>
                                        <dd class="mt-1 text-slate-900">
                                            {{ $application->user->profile->job_type ?? '未設定' }}
                                        </dd>
                                    </div>

                                    <div class="rounded-xl bg-slate-50 p-4">
                                        <dt class="font-bold text-slate-500">都道府県</dt>
                                        <dd class="mt-1 text-slate-900">
                                            {{ $application->user->profile->prefecture?->name ?? '未設定' }}
                                        </dd>
                                    </div>

                                    <div class="rounded-xl bg-slate-50 p-4 md:col-span-2">
                                        <dt class="font-bold text-slate-500">スキル</dt>
                                        <dd class="mt-1 leading-7 text-slate-900">
                                            {!! nl2br(e($application->user->profile->skills ?? '未設定')) !!}
                                        </dd>
                                    </div>

                                    <div class="rounded-xl bg-slate-50 p-4 md:col-span-2">
                                        <dt class="font-bold text-slate-500">自己紹介</dt>
                                        <dd class="mt-1 leading-7 text-slate-900">
                                            {!! nl2br(e($application->user->profile->bio ?? '未設定')) !!}
                                        </dd>
                                    </div>

                                    <div class="rounded-xl bg-indigo-50 p-4 md:col-span-2">
                                        <dt class="font-bold text-indigo-700">申請メッセージ</dt>
                                        <dd class="mt-2 leading-7 text-indigo-950">
                                            {!! nl2br(e($application->message ?? 'メッセージはありません。')) !!}
                                        </dd>
                                    </div>
                                </dl>
                            </div>

                            {{-- Actions --}}
                            <div class="w-full shrink-0 space-y-3 lg:w-48">
                                @if ($application->status === 1)
                                    <form method="POST" action="{{ route('applications.approve', $application) }}">
                                        @csrf
                                        @method('PATCH')

                                        <button
                                            type="submit"
                                            class="flex w-full items-center justify-center rounded-xl bg-indigo-600 px-5 py-3 text-sm font-bold text-white shadow-sm transition hover:bg-indigo-700"
                                        >
                                            承認する
                                        </button>
                                    </form>

                                    <form method="POST" action="{{ route('applications.reject', $application) }}">
                                        @csrf
                                        @method('PATCH')

                                        <button
                                            type="submit"
                                            class="flex w-full items-center justify-center rounded-xl border border-rose-200 bg-white px-5 py-3 text-sm font-bold text-rose-600 transition hover:bg-rose-50"
                                        >
                                            否認する
                                        </button>
                                    </form>
                                @endif

                                @if ($application->status === 2)
                                    <a
                                        href="{{ route('messages.show', [$workPost, $application->user]) }}"
                                        class="flex w-full items-center justify-center rounded-xl bg-indigo-600 px-5 py-3 text-sm font-bold text-white shadow-sm transition hover:bg-indigo-700"
                                    >
                                        メッセージ
                                    </a>
                                @endif
                            </div>
                        </div>
                    </article>
                @empty
                    <div class="rounded-2xl bg-white p-10 text-center shadow-sm ring-1 ring-slate-200">
                        <div class="mx-auto flex h-14 w-14 items-center justify-center rounded-full bg-slate-100 text-2xl">
                            📝
                        </div>

                        <h3 class="mt-4 text-lg font-bold text-slate-900">
                            参加申請はありません
                        </h3>

                        <p class="mt-2 text-sm leading-6 text-slate-600">
                            この募集には、まだ参加申請が届いていません。
                        </p>

                        <div class="mt-6">
                            <a
                                href="{{ route('work-posts.show', $workPost) }}"
                                class="inline-flex items-center justify-center rounded-xl bg-indigo-600 px-5 py-3 text-sm font-bold text-white transition hover:bg-indigo-700"
                            >
                                募集詳細へ戻る
                            </a>
                        </div>
                    </div>
                @endforelse
            </div>
        </section>
    </div>
</div>
@endsection
