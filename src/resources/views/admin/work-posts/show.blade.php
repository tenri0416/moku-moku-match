@extends('layouts.admin')

@section('title', '募集詳細管理')

@section('content')
<div class="min-h-screen bg-slate-50">
    <div class="mx-auto max-w-5xl px-4 py-10 sm:px-6 lg:px-8">
        <div class="mb-8 flex flex-col gap-4 sm:flex-row sm:items-start sm:justify-between">
            <div>
                <p class="text-sm font-bold text-indigo-600">WORK POST DETAIL</p>

                <h1 class="mt-2 text-3xl font-bold text-slate-900">
                    募集詳細 #{{ $workPost->id }}
                </h1>

                <p class="mt-2 text-slate-600">
                    募集内容、投稿者、公開状態を確認できます。
                </p>
            </div>

            <a
                href="{{ route('admin.work-posts.index') }}"
                class="inline-flex items-center justify-center rounded-xl border border-slate-300 bg-white px-5 py-3 text-sm font-bold text-slate-700 transition hover:bg-slate-50"
            >
                募集管理へ戻る
            </a>
        </div>

        <div class="grid gap-6 lg:grid-cols-3">
            <div class="space-y-6 lg:col-span-2">
                <section class="rounded-2xl bg-white p-6 shadow-sm ring-1 ring-slate-200 sm:p-8">
                    <div class="mb-4 flex flex-wrap gap-2">
                        @if ($workPost->status === 1)
                            <span class="rounded-full bg-emerald-50 px-3 py-1 text-xs font-bold text-emerald-700">募集中</span>
                        @elseif ($workPost->status === 2)
                            <span class="rounded-full bg-slate-100 px-3 py-1 text-xs font-bold text-slate-600">終了</span>
                        @else
                            <span class="rounded-full bg-rose-50 px-3 py-1 text-xs font-bold text-rose-700">非公開</span>
                        @endif

                        <span class="rounded-full bg-indigo-50 px-3 py-1 text-xs font-bold text-indigo-700">
                            {{ $workPost->purpose }}
                        </span>
                    </div>

                    <h2 class="text-2xl font-bold text-slate-900">
                        {{ $workPost->title }}
                    </h2>

                    <p class="mt-4 text-sm text-slate-600">
                        投稿者：
                        <span class="font-semibold text-slate-800">
                            {{ $workPost->user->profile->display_name ?? $workPost->user->name }}
                        </span>
                    </p>

                    <div class="mt-8">
                        <h3 class="text-xl font-bold text-slate-900">
                            募集内容
                        </h3>

                        <div class="mt-4 rounded-xl bg-slate-50 p-5 leading-8 text-slate-700">
                            {!! nl2br(e($workPost->body)) !!}
                        </div>
                    </div>
                </section>

                <section class="rounded-2xl bg-white p-6 shadow-sm ring-1 ring-slate-200 sm:p-8">
                    <h2 class="text-xl font-bold text-slate-900">
                        投稿者情報
                    </h2>

                    <dl class="mt-5 grid gap-4 md:grid-cols-2">
                        <div class="rounded-xl bg-slate-50 p-4">
                            <dt class="text-sm font-bold text-slate-500">表示名</dt>
                            <dd class="mt-1 font-semibold text-slate-900">
                                {{ $workPost->user->profile->display_name ?? $workPost->user->name }}
                            </dd>
                        </div>

                        <div class="rounded-xl bg-slate-50 p-4">
                            <dt class="text-sm font-bold text-slate-500">メールアドレス</dt>
                            <dd class="mt-1 font-semibold text-slate-900">
                                {{ $workPost->user->email }}
                            </dd>
                        </div>

                        <div class="rounded-xl bg-slate-50 p-4">
                            <dt class="text-sm font-bold text-slate-500">職種</dt>
                            <dd class="mt-1 font-semibold text-slate-900">
                                {{ $workPost->user->profile->job_type ?? '未設定' }}
                            </dd>
                        </div>

                        <div class="rounded-xl bg-slate-50 p-4">
                            <dt class="text-sm font-bold text-slate-500">都道府県</dt>
                            <dd class="mt-1 font-semibold text-slate-900">
                                {{ $user->profile?->prefecture?->name ?? '未設定' }}
                            </dd>
                        </div>
                    </dl>
                </section>
            </div>

            <aside class="space-y-6">
                <section class="rounded-2xl bg-white p-6 shadow-sm ring-1 ring-slate-200">
                    <h2 class="text-lg font-bold text-slate-900">
                        募集情報
                    </h2>

                    <dl class="mt-5 space-y-4 text-sm">
                        <div>
                            <dt class="font-bold text-slate-500">開催形式</dt>
                            <dd class="mt-1 text-slate-900">{{ $workPost->location_type }}</dd>
                        </div>

                        <div>
                            <dt class="font-bold text-slate-500">使用ツール</dt>
                            <dd class="mt-1 text-slate-900">{{ $workPost->meeting_tool ?? '未定' }}</dd>
                        </div>

                        <div>
                            <dt class="font-bold text-slate-500">都道府県</dt>
                            <dd class="mt-1 text-slate-900">{{ $workPost->prefecture?->name ?? '未設定' }}</dd>
                        </div>

                        <div>
                            <dt class="font-bold text-slate-500">開始日時</dt>
                            <dd class="mt-1 text-slate-900">
                                {{ $workPost->start_at ? $workPost->start_at->format('Y/m/d H:i') : '未定' }}
                            </dd>
                        </div>

                        <div>
                            <dt class="font-bold text-slate-500">終了日時</dt>
                            <dd class="mt-1 text-slate-900">
                                {{ $workPost->end_at ? $workPost->end_at->format('Y/m/d H:i') : '未定' }}
                            </dd>
                        </div>

                        <div>
                            <dt class="font-bold text-slate-500">募集人数</dt>
                            <dd class="mt-1 text-slate-900">
                                {{ $workPost->max_participants ?? '未設定' }}
                            </dd>
                        </div>

                        <div>
                            <dt class="font-bold text-slate-500">投稿日時</dt>
                            <dd class="mt-1 text-slate-900">
                                {{ $workPost->created_at->format('Y/m/d H:i') }}
                            </dd>
                        </div>
                    </dl>
                </section>

                <section class="rounded-2xl bg-white p-6 shadow-sm ring-1 ring-slate-200">
                    <h2 class="text-lg font-bold text-slate-900">
                        管理操作
                    </h2>

                    <div class="mt-5 space-y-3">
                        @if (Route::has('admin.work-posts.edit'))
                            <a
                                href="{{ route('admin.work-posts.edit', $workPost) }}"
                                class="flex w-full items-center justify-center rounded-xl bg-indigo-600 px-5 py-3 text-sm font-bold text-white shadow-sm transition hover:bg-indigo-700"
                            >
                                編集する
                            </a>
                        @endif

                        @if ($workPost->status !== 3)
                            <form method="POST" action="{{ route('admin.work-posts.private', $workPost) }}">
                                @csrf
                                @method('PATCH')

                                <button
                                    type="submit"
                                    class="flex w-full items-center justify-center rounded-xl bg-rose-600 px-5 py-3 text-sm font-bold text-white shadow-sm transition hover:bg-rose-700"
                                >
                                    非公開にする
                                </button>
                            </form>
                        @else
                            <form method="POST" action="{{ route('admin.work-posts.open', $workPost) }}">
                                @csrf
                                @method('PATCH')

                                <button
                                    type="submit"
                                    class="flex w-full items-center justify-center rounded-xl bg-emerald-600 px-5 py-3 text-sm font-bold text-white shadow-sm transition hover:bg-emerald-700"
                                >
                                    再公開する
                                </button>
                            </form>
                        @endif

                        <a
                            href="{{ route('admin.work-posts.index') }}"
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
