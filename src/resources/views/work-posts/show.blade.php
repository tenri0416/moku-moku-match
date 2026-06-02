@extends('layouts.app')

@section('title', $workPost->title)

@section('content')
<div class="min-h-screen bg-slate-50">
    <div class="mx-auto max-w-5xl px-4 py-10 sm:px-6 lg:px-8">
        {{-- Header --}}
        <div class="mb-6 rounded-2xl bg-white p-6 shadow-sm ring-1 ring-slate-200 sm:p-8">
            <div class="mb-4 flex flex-wrap gap-2">
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

                @if ($workPost->location_type === 'online')
                    <span class="rounded-full bg-sky-50 px-3 py-1 text-xs font-bold text-sky-700">
                        オンライン
                    </span>
                @elseif ($workPost->location_type === 'offline')
                    <span class="rounded-full bg-amber-50 px-3 py-1 text-xs font-bold text-amber-700">
                        オフライン
                    </span>
                @elseif ($workPost->location_type === 'both')
                    <span class="rounded-full bg-purple-50 px-3 py-1 text-xs font-bold text-purple-700">
                        どちらでも可
                    </span>
                @else
                    <span class="rounded-full bg-slate-100 px-3 py-1 text-xs font-bold text-slate-600">
                        未設定
                    </span>
                @endif
            </div>

            <h1 class="text-3xl font-bold leading-tight text-slate-900">
                {{ $workPost->title }}
            </h1>
            @php
            $profile = $workPost->user->profile;
            $avatarPath = $profile?->avatar_path;
            $avatarUrl = $avatarPath
                ? asset('storage/' . $avatarPath)
                : asset('images/default-avatar.png');
            $displayName = $profile?->display_name ?? $workPost->user->name;
        @endphp
        
        <div class="mt-5 flex items-center gap-3 rounded-xl bg-slate-50 p-4">
            <img
                src="{{ $avatarUrl }}"
                alt="{{ $displayName }}のプロフィール画像"
                class="h-14 w-14 flex-shrink-0 rounded-full border border-slate-200 bg-white object-cover"
            >
        
            <div>
                <p class="text-xs font-bold text-slate-500">
                    投稿者
                </p>
                <p class="text-base font-bold text-slate-900">
                    {{ $displayName }}
                </p>
            </div>
        </div>
        </div>

        <div class="grid gap-6 lg:grid-cols-3">
            {{-- Main --}}
            <div class="space-y-6 lg:col-span-2">
                {{-- Body --}}
                <section class="rounded-2xl bg-white p-6 shadow-sm ring-1 ring-slate-200 sm:p-8">
                    <h2 class="text-xl font-bold text-slate-900">
                        募集内容
                    </h2>

                    <div class="mt-4 leading-8 text-slate-700">
                        {!! nl2br(e($workPost->body)) !!}
                    </div>
                </section>

                {{-- Owner Profile --}}
                <section class="rounded-2xl bg-white p-6 shadow-sm ring-1 ring-slate-200 sm:p-8">
                    <h2 class="text-xl font-bold text-slate-900">
                        投稿者プロフィール
                    </h2>
                
                    @php
                        $profile = $workPost->user->profile;
                        $avatarPath = $profile?->avatar_path;
                        $avatarUrl = $avatarPath
                            ? asset('storage/' . $avatarPath)
                            : asset('images/default-avatar.png');
                        $displayName = $profile?->display_name ?? $workPost->user->name;
                    @endphp
                
                    <div class="mt-5 flex items-center gap-4 rounded-xl bg-slate-50 p-4">
                        <img
                            src="{{ $avatarUrl }}"
                            alt="{{ $displayName }}のプロフィール画像"
                            class="h-16 w-16 flex-shrink-0 rounded-full border border-slate-200 bg-white object-cover"
                        >
                
                        <div>
                            <p class="text-xs font-bold text-slate-500">
                                投稿者
                            </p>
                            <p class="text-lg font-bold text-slate-900">
                                {{ $displayName }}
                            </p>
                        </div>
                    </div>
                
                    <div class="mt-5 space-y-3 text-sm text-slate-600">
                        <p>
                            表示名：
                            <span class="font-semibold text-slate-800">
                                {{ $displayName }}
                            </span>
                        </p>
                
                        <p>
                            職種：
                            <span class="font-semibold text-slate-800">
                                {{ $profile?->job_type ?? '未設定' }}
                            </span>
                        </p>
                
                        <p>
                            スキル：
                            <span class="font-semibold text-slate-800">
                                {{ $profile?->skills ?? '未設定' }}
                            </span>
                        </p>
                    </div>
                </section>
            </div>

            {{-- Side --}}
            <aside class="space-y-6">
                {{-- Info --}}
                <section class="rounded-2xl bg-white p-6 shadow-sm ring-1 ring-slate-200">
                    <h2 class="text-lg font-bold text-slate-900">
                        募集情報
                    </h2>

                    <dl class="mt-5 space-y-4 text-sm">
                        <div>
                            <dt class="font-bold text-slate-500">目的</dt>
                            <dd class="mt-1 text-slate-900">{{ $workPost->purpose }}</dd>
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
                    </dl>
                </section>

                {{-- Actions --}}
                <section class="rounded-2xl bg-white p-6 shadow-sm ring-1 ring-slate-200">
                    <h2 class="text-lg font-bold text-slate-900">
                        操作
                    </h2>

                    <div class="mt-5 space-y-3">
                        @auth
                            @if ($workPost->user_id === auth()->id())
                                <a
                                    href="{{ route('work-posts.edit', $workPost) }}"
                                    class="flex w-full items-center justify-center rounded-xl bg-indigo-600 px-5 py-3 text-sm font-bold text-white transition hover:bg-indigo-700"
                                >
                                    編集する
                                </a>

                                @if ($workPost->status === 1)
                                    <form method="POST" action="{{ route('work-posts.close', $workPost) }}">
                                        @csrf
                                        @method('PATCH')

                                        <button
                                            type="submit"
                                            class="flex w-full items-center justify-center rounded-xl border border-slate-300 bg-white px-5 py-3 text-sm font-bold text-slate-700 transition hover:bg-slate-50"
                                        >
                                            募集終了
                                        </button>
                                    </form>
                                @endif

                                <a
                                    href="{{ route('applications.index', $workPost) }}"
                                    class="flex w-full items-center justify-center rounded-xl border border-slate-300 bg-white px-5 py-3 text-sm font-bold text-slate-700 transition hover:bg-slate-50"
                                >
                                    参加申請一覧
                                </a>
                            @else
                                @if (! $hasApplied && $workPost->status === 1)
                                    <a
                                        href="{{ route('applications.create', $workPost) }}"
                                        class="flex w-full items-center justify-center rounded-xl bg-indigo-600 px-5 py-3 text-sm font-bold text-white transition hover:bg-indigo-700"
                                    >
                                        参加申請する
                                    </a>
                                @elseif ($hasApplied)
                                    <div class="rounded-xl bg-emerald-50 p-4 text-sm font-semibold text-emerald-800">
                                        この募集には参加申請済みです。
                                    </div>
                                @else
                                    <div class="rounded-xl bg-slate-100 p-4 text-sm font-semibold text-slate-600">
                                        この募集は現在申請できません。
                                    </div>
                                @endif

                                <a
                                    href="{{ route('reports.create', [
                                        'reported_user_id' => $workPost->user_id,
                                        'work_post_id' => $workPost->id,
                                    ]) }}"
                                    class="flex w-full items-center justify-center rounded-xl border border-rose-200 bg-white px-5 py-3 text-sm font-bold text-rose-600 transition hover:bg-rose-50"
                                >
                                    通報する
                                </a>
                            @endif
                        @else
                            <a
                                href="{{ route('login') }}"
                                class="flex w-full items-center justify-center rounded-xl bg-indigo-600 px-5 py-3 text-sm font-bold text-white transition hover:bg-indigo-700"
                            >
                                ログインして参加申請
                            </a>
                        @endauth

                        <a
                            href="{{ route('home') }}"
                            class="flex w-full items-center justify-center rounded-xl border border-slate-300 bg-white px-5 py-3 text-sm font-bold text-slate-700 transition hover:bg-slate-50"
                        >
                            募集一覧へ戻る
                        </a>
                    </div>
                </section>
            </aside>
        </div>
    </div>
</div>
@endsection
