@extends('layouts.app')

@section('title', 'マイページ')

@section('content')
<div class="min-h-screen bg-slate-50">
    <div class="mx-auto max-w-7xl px-4 py-10 sm:px-6 lg:px-8">
        {{-- Header --}}
        <div class="mb-8">
            <p class="text-sm font-bold text-indigo-600">MY PAGE</p>
            <h1 class="mt-2 text-3xl font-bold text-slate-900">
                マイページ
            </h1>
            <p class="mt-2 text-slate-600">
                プロフィール、募集、参加申請、メッセージを確認できます。
            </p>
        </div>

        {{-- Profile --}}
        <section class="rounded-2xl bg-white p-6 shadow-sm ring-1 ring-slate-200">
            <div class="flex flex-col gap-4 sm:flex-row sm:items-start sm:justify-between">
                <div>
                    <h2 class="text-xl font-bold text-slate-900">
                        プロフィール
                    </h2>
                    <p class="mt-1 text-sm text-slate-500">
                        他のユーザーに表示される情報です。
                    </p>
                </div>

                <a
                    href="{{ route('profile.edit') }}"
                    class="inline-flex items-center justify-center rounded-xl bg-indigo-600 px-4 py-2 text-sm font-bold text-white transition hover:bg-indigo-700"
                >
                    プロフィール編集
                </a>
            </div>

            @if ($user->profile)
                <div class="mt-6 grid gap-4 md:grid-cols-2">
                    <div class="rounded-xl bg-slate-50 p-4">
                        <dt class="text-xs font-bold uppercase tracking-wide text-slate-500">
                            表示名
                        </dt>
                        <dd class="mt-1 font-semibold text-slate-900">
                            {{ $user->profile->display_name }}
                        </dd>
                    </div>

                    <div class="rounded-xl bg-slate-50 p-4">
                        <dt class="text-xs font-bold uppercase tracking-wide text-slate-500">
                            職種
                        </dt>
                        <dd class="mt-1 font-semibold text-slate-900">
                            {{ $user->profile->job_type ?? '未設定' }}
                        </dd>
                    </div>

                    <div class="rounded-xl bg-slate-50 p-4">
                        <dt class="text-xs font-bold uppercase tracking-wide text-slate-500">
                            都道府県
                        </dt>
                        <dd class="mt-1 font-semibold text-slate-900">
                            {{ $user->profile->prefecture ?? '未設定' }}
                        </dd>
                    </div>

                    <div class="rounded-xl bg-slate-50 p-4">
                        <dt class="text-xs font-bold uppercase tracking-wide text-slate-500">
                            利用目的
                        </dt>
                        <dd class="mt-1 font-semibold text-slate-900">
                            {{ $user->profile->purpose ?? '未設定' }}
                        </dd>
                    </div>

                    <div class="rounded-xl bg-slate-50 p-4 md:col-span-2">
                        <dt class="text-xs font-bold uppercase tracking-wide text-slate-500">
                            スキル
                        </dt>
                        <dd class="mt-1 leading-7 text-slate-900">
                            {!! nl2br(e($user->profile->skills ?? '未設定')) !!}
                        </dd>
                    </div>

                    <div class="rounded-xl bg-slate-50 p-4 md:col-span-2">
                        <dt class="text-xs font-bold uppercase tracking-wide text-slate-500">
                            希望作業スタイル
                        </dt>
                        <dd class="mt-1 leading-7 text-slate-900">
                            {{ $user->profile->work_style ?? '未設定' }}
                        </dd>
                    </div>
                </div>
            @else
                <div class="mt-6 rounded-xl border border-amber-200 bg-amber-50 p-5">
                    <p class="font-semibold text-amber-900">
                        プロフィールが未登録です。
                    </p>
                    <p class="mt-2 text-sm leading-7 text-amber-800">
                        募集作成や参加申請を行うには、プロフィール登録が必要です。
                    </p>
                </div>
            @endif
        </section>

        {{-- Main Grid --}}
        <div class="mt-8 grid gap-8 lg:grid-cols-2">
            {{-- My Work Posts --}}
            <section class="rounded-2xl bg-white p-6 shadow-sm ring-1 ring-slate-200">
                <div class="mb-5 flex items-center justify-between gap-4">
                    <div>
                        <h2 class="text-xl font-bold text-slate-900">
                            自分の募集
                        </h2>
                        <p class="mt-1 text-sm text-slate-500">
                            作成した募集を確認できます。
                        </p>
                    </div>

                    <a
                        href="{{ route('work-posts.create') }}"
                        class="shrink-0 rounded-xl bg-indigo-600 px-4 py-2 text-sm font-bold text-white transition hover:bg-indigo-700"
                    >
                        募集作成
                    </a>
                </div>

                <div class="space-y-4">
                    @forelse ($workPosts as $workPost)
                        <article class="rounded-xl border border-slate-200 p-4 transition hover:bg-slate-50">
                            <h3 class="font-bold text-slate-900">
                                <a href="{{ route('work-posts.show', $workPost) }}" class="hover:text-indigo-600">
                                    {{ $workPost->title }}
                                </a>
                            </h3>

                            <div class="mt-3 flex flex-wrap gap-2">
                                <span class="rounded-full bg-indigo-50 px-3 py-1 text-xs font-bold text-indigo-700">
                                    {{ $workPost->purpose }}
                                </span>

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
                            </div>

                            <div class="mt-4 flex flex-wrap gap-3 text-sm font-semibold">
                                <a href="{{ route('work-posts.edit', $workPost) }}" class="text-indigo-600 hover:text-indigo-700">
                                    編集
                                </a>
                                <a href="{{ route('applications.index', $workPost) }}" class="text-indigo-600 hover:text-indigo-700">
                                    参加申請一覧
                                </a>
                            </div>
                        </article>
                    @empty
                        <div class="rounded-xl bg-slate-50 p-5 text-center">
                            <p class="text-sm text-slate-600">
                                作成した募集はありません。
                            </p>
                        </div>
                    @endforelse
                </div>
            </section>

            {{-- Applications --}}
            <section class="rounded-2xl bg-white p-6 shadow-sm ring-1 ring-slate-200">
                <div class="mb-5">
                    <h2 class="text-xl font-bold text-slate-900">
                        参加申請した募集
                    </h2>
                    <p class="mt-1 text-sm text-slate-500">
                        自分が参加申請した募集の状態を確認できます。
                    </p>
                </div>

                <div class="space-y-4">
                    @forelse ($applications as $application)
                        <article class="rounded-xl border border-slate-200 p-4 transition hover:bg-slate-50">
                            <h3 class="font-bold text-slate-900">
                                <a href="{{ route('work-posts.show', $application->workPost) }}" class="hover:text-indigo-600">
                                    {{ $application->workPost->title }}
                                </a>
                            </h3>

                            <p class="mt-2 text-sm text-slate-600">
                                投稿者：
                                <span class="font-semibold text-slate-800">
                                    {{ $application->workPost->user->profile->display_name ?? $application->workPost->user->name }}
                                </span>
                            </p>

                            <div class="mt-3">
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
                            </div>

                            @if ($application->status === 2)
                                <div class="mt-4">
                                    <a
                                        href="{{ route('messages.show', [$application->workPost, $application->workPost->user]) }}"
                                        class="text-sm font-bold text-indigo-600 hover:text-indigo-700"
                                    >
                                        メッセージする →
                                    </a>
                                </div>
                            @endif
                        </article>
                    @empty
                        <div class="rounded-xl bg-slate-50 p-5 text-center">
                            <p class="text-sm text-slate-600">
                                参加申請した募集はありません。
                            </p>
                        </div>
                    @endforelse
                </div>
            </section>
        </div>

        {{-- Bottom Grid --}}
        <div class="mt-8 grid gap-8 lg:grid-cols-2">
            {{-- Approved Applications --}}
            <section class="rounded-2xl bg-white p-6 shadow-sm ring-1 ring-slate-200">
                <div class="mb-5">
                    <h2 class="text-xl font-bold text-slate-900">
                        承認済みの募集
                    </h2>
                    <p class="mt-1 text-sm text-slate-500">
                        メッセージ可能な募集です。
                    </p>
                </div>

                <div class="space-y-4">
                    @forelse ($approvedApplications as $application)
                        <article class="rounded-xl border border-slate-200 p-4">
                            <h3 class="font-bold text-slate-900">
                                {{ $application->workPost->title }}
                            </h3>

                            <p class="mt-2 text-sm text-slate-600">
                                投稿者：
                                <span class="font-semibold text-slate-800">
                                    {{ $application->workPost->user->profile->display_name ?? $application->workPost->user->name }}
                                </span>
                            </p>

                            <div class="mt-4">
                                <a
                                    href="{{ route('messages.show', [$application->workPost, $application->workPost->user]) }}"
                                    class="text-sm font-bold text-indigo-600 hover:text-indigo-700"
                                >
                                    メッセージする →
                                </a>
                            </div>
                        </article>
                    @empty
                        <div class="rounded-xl bg-slate-50 p-5 text-center">
                            <p class="text-sm text-slate-600">
                                承認済みの募集はありません。
                            </p>
                        </div>
                    @endforelse
                </div>
            </section>

            {{-- Latest Messages --}}
            <section class="rounded-2xl bg-white p-6 shadow-sm ring-1 ring-slate-200">
                <div class="mb-5 flex items-center justify-between gap-4">
                    <div>
                        <h2 class="text-xl font-bold text-slate-900">
                            最新メッセージ
                        </h2>
                        <p class="mt-1 text-sm text-slate-500">
                            最近のやり取りを確認できます。
                        </p>
                    </div>

                    <a href="{{ route('messages.index') }}" class="text-sm font-bold text-indigo-600 hover:text-indigo-700">
                        一覧
                    </a>
                </div>

                <div class="space-y-4">
                    @forelse ($messages as $message)
                        @php
                            $partner = $message->sender_id === auth()->id()
                                ? $message->receiver
                                : $message->sender;
                        @endphp

                        <article class="rounded-xl border border-slate-200 p-4">
                            <h3 class="font-bold text-slate-900">
                                {{ $message->workPost->title }}
                            </h3>

                            <p class="mt-2 text-sm text-slate-600">
                                相手：
                                <span class="font-semibold text-slate-800">
                                    {{ $partner->profile->display_name ?? $partner->name }}
                                </span>
                            </p>

                            <p class="mt-3 line-clamp-2 text-sm leading-6 text-slate-600">
                                {{ $message->body }}
                            </p>

                            <p class="mt-2 text-xs text-slate-400">
                                {{ $message->created_at->format('Y/m/d H:i') }}
                            </p>

                            <div class="mt-4">
                                <a
                                    href="{{ route('messages.show', [$message->workPost, $partner]) }}"
                                    class="text-sm font-bold text-indigo-600 hover:text-indigo-700"
                                >
                                    メッセージを見る →
                                </a>
                            </div>
                        </article>
                    @empty
                        <div class="rounded-xl bg-slate-50 p-5 text-center">
                            <p class="text-sm text-slate-600">
                                メッセージはありません。
                            </p>
                        </div>
                    @endforelse
                </div>
            </section>
        </div>
    </div>
</div>
@endsection
