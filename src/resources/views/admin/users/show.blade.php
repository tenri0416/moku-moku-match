@extends('layouts.admin')

@section('title', 'ユーザー詳細')

@section('content')
<div class="min-h-screen bg-slate-50">
    <div class="mx-auto max-w-5xl px-4 py-10 sm:px-6 lg:px-8">
        {{-- Header --}}
        <div class="mb-8 flex flex-col gap-4 sm:flex-row sm:items-start sm:justify-between">
            <div>
                <p class="text-sm font-bold text-indigo-600">USER DETAIL</p>

                <h1 class="mt-2 text-3xl font-bold text-slate-900">
                    ユーザー詳細 #{{ $user->id }}
                </h1>

                <p class="mt-2 text-slate-600">
                    ユーザー情報、プロフィール、投稿・申請状況を確認できます。
                </p>
            </div>

            <a
                href="{{ route('admin.users.index') }}"
                class="inline-flex items-center justify-center rounded-xl border border-slate-300 bg-white px-5 py-3 text-sm font-bold text-slate-700 transition hover:bg-slate-50"
            >
                ユーザー一覧へ戻る
            </a>
        </div>

        <div class="grid gap-6 lg:grid-cols-3">
            {{-- Main --}}
            <div class="space-y-6 lg:col-span-2">
                {{-- Basic Info --}}
                <section class="rounded-2xl bg-white p-6 shadow-sm ring-1 ring-slate-200 sm:p-8">
                    <div class="flex flex-col gap-4 sm:flex-row sm:items-start sm:justify-between">
                        <div>
                            <div class="mb-4 flex flex-wrap gap-2">
                                @if ($user->role === 2)
                                    <span class="rounded-full bg-indigo-50 px-3 py-1 text-xs font-bold text-indigo-700">
                                        管理者
                                    </span>
                                @else
                                    <span class="rounded-full bg-slate-100 px-3 py-1 text-xs font-bold text-slate-600">
                                        一般
                                    </span>
                                @endif

                                @if ($user->status === 1)
                                    <span class="rounded-full bg-emerald-50 px-3 py-1 text-xs font-bold text-emerald-700">
                                        有効
                                    </span>
                                @elseif ($user->status === 2)
                                    <span class="rounded-full bg-rose-50 px-3 py-1 text-xs font-bold text-rose-700">
                                        停止中
                                    </span>
                                @else
                                    <span class="rounded-full bg-slate-100 px-3 py-1 text-xs font-bold text-slate-600">
                                        不明
                                    </span>
                                @endif
                            </div>

                            <h2 class="text-2xl font-bold text-slate-900">
                                {{ $user->profile->display_name ?? $user->name }}
                            </h2>

                            <p class="mt-2 text-sm text-slate-600">
                                {{ $user->email }}
                            </p>
                        </div>
                    </div>

                    <dl class="mt-8 grid gap-4 md:grid-cols-2">
                        <div class="rounded-xl bg-slate-50 p-4">
                            <dt class="text-sm font-bold text-slate-500">
                                ユーザーID
                            </dt>
                            <dd class="mt-1 font-semibold text-slate-900">
                                {{ $user->id }}
                            </dd>
                        </div>

                        <div class="rounded-xl bg-slate-50 p-4">
                            <dt class="text-sm font-bold text-slate-500">
                                名前
                            </dt>
                            <dd class="mt-1 font-semibold text-slate-900">
                                {{ $user->name }}
                            </dd>
                        </div>

                        <div class="rounded-xl bg-slate-50 p-4">
                            <dt class="text-sm font-bold text-slate-500">
                                メールアドレス
                            </dt>
                            <dd class="mt-1 font-semibold text-slate-900">
                                {{ $user->email }}
                            </dd>
                        </div>

                        <div class="rounded-xl bg-slate-50 p-4">
                            <dt class="text-sm font-bold text-slate-500">
                                登録日時
                            </dt>
                            <dd class="mt-1 font-semibold text-slate-900">
                                {{ $user->created_at->format('Y/m/d H:i') }}
                            </dd>
                        </div>
                    </dl>
                </section>

                {{-- Profile --}}
                <section class="rounded-2xl bg-white p-6 shadow-sm ring-1 ring-slate-200 sm:p-8">
                    <h2 class="text-xl font-bold text-slate-900">
                        プロフィール
                    </h2>

                    @if ($user->profile)
                        <dl class="mt-5 grid gap-4 md:grid-cols-2">
                            <div class="rounded-xl bg-slate-50 p-4">
                                <dt class="text-sm font-bold text-slate-500">
                                    表示名
                                </dt>
                                <dd class="mt-1 font-semibold text-slate-900">
                                    {{ $user->profile->display_name }}
                                </dd>
                            </div>

                            <div class="rounded-xl bg-slate-50 p-4">
                                <dt class="text-sm font-bold text-slate-500">
                                    職種
                                </dt>
                                <dd class="mt-1 font-semibold text-slate-900">
                                    {{ $user->profile->job_type ?? '未設定' }}
                                </dd>
                            </div>

                            <div class="rounded-xl bg-slate-50 p-4">
                                <dt class="text-sm font-bold text-slate-500">
                                    都道府県
                                </dt>
                                <dd class="mt-1 font-semibold text-slate-900">
                                    {{ $user->profile?->prefecture?->name ?? '未設定' }}
                                </dd>
                            </div>

                            <div class="rounded-xl bg-slate-50 p-4">
                                <dt class="text-sm font-bold text-slate-500">
                                    利用目的
                                </dt>
                                <dd class="mt-1 font-semibold text-slate-900">
                                    {{ $user->profile->purpose ?? '未設定' }}
                                </dd>
                            </div>

                            <div class="rounded-xl bg-slate-50 p-4 md:col-span-2">
                                <dt class="text-sm font-bold text-slate-500">
                                    スキル
                                </dt>
                                <dd class="mt-1 leading-7 text-slate-900">
                                    {!! nl2br(e($user->profile->skills ?? '未設定')) !!}
                                </dd>
                            </div>

                            <div class="rounded-xl bg-slate-50 p-4 md:col-span-2">
                                <dt class="text-sm font-bold text-slate-500">
                                    自己紹介
                                </dt>
                                <dd class="mt-1 leading-7 text-slate-900">
                                    {!! nl2br(e($user->profile->bio ?? '未設定')) !!}
                                </dd>
                            </div>

                            <div class="rounded-xl bg-slate-50 p-4 md:col-span-2">
                                <dt class="text-sm font-bold text-slate-500">
                                    希望作業スタイル
                                </dt>
                                <dd class="mt-1 leading-7 text-slate-900">
                                    {!! nl2br(e($user->profile->work_style ?? '未設定')) !!}
                                </dd>
                            </div>
                        </dl>
                    @else
                        <div class="mt-5 rounded-xl bg-amber-50 p-5">
                            <p class="text-sm font-semibold text-amber-800">
                                プロフィールは未登録です。
                            </p>
                        </div>
                    @endif
                </section>

                {{-- Activity --}}
                <section class="rounded-2xl bg-white p-6 shadow-sm ring-1 ring-slate-200 sm:p-8">
                    <h2 class="text-xl font-bold text-slate-900">
                        利用状況
                    </h2>

                    <div class="mt-5 grid gap-4 md:grid-cols-2">
                        <div class="rounded-xl bg-indigo-50 p-5">
                            <p class="text-sm font-bold text-indigo-700">
                                作成した募集
                            </p>
                            <p class="mt-2 text-3xl font-black text-indigo-900">
                                {{ $user->workPosts->count() }}
                            </p>
                        </div>

                        <div class="rounded-xl bg-emerald-50 p-5">
                            <p class="text-sm font-bold text-emerald-700">
                                参加申請
                            </p>
                            <p class="mt-2 text-3xl font-black text-emerald-900">
                                {{ $user->applications->count() }}
                            </p>
                        </div>
                    </div>

                    @if ($user->workPosts->isNotEmpty())
                        <div class="mt-6">
                            <h3 class="text-sm font-bold text-slate-700">
                                最近の募集
                            </h3>

                            <div class="mt-3 space-y-3">
                                @foreach ($user->workPosts->take(5) as $workPost)
                                    <div class="rounded-xl border border-slate-200 p-4">
                                        <p class="font-semibold text-slate-900">
                                            {{ $workPost->title }}
                                        </p>

                                        <p class="mt-1 text-sm text-slate-500">
                                            {{ $workPost->created_at->format('Y/m/d H:i') }}
                                        </p>

                                        <a
                                            href="{{ route('admin.work-posts.show', $workPost) }}"
                                            class="mt-2 inline-flex text-sm font-bold text-indigo-600 hover:text-indigo-700"
                                        >
                                            募集詳細を見る →
                                        </a>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @endif
                </section>
            </div>

            {{-- Side --}}
            <aside class="space-y-6">
                {{-- Actions --}}
                <section class="rounded-2xl bg-white p-6 shadow-sm ring-1 ring-slate-200">
                    <h2 class="text-lg font-bold text-slate-900">
                        管理操作
                    </h2>

                    <div class="mt-5 space-y-3">
                        @if ($user->status === 1)
                            <form method="POST" action="{{ route('admin.users.suspend', $user) }}">
                                @csrf
                                @method('PATCH')

                                <button
                                    type="submit"
                                    class="flex w-full items-center justify-center rounded-xl bg-rose-600 px-5 py-3 text-sm font-bold text-white shadow-sm transition hover:bg-rose-700"
                                >
                                    停止する
                                </button>
                            </form>
                        @else
                            <form method="POST" action="{{ route('admin.users.activate', $user) }}">
                                @csrf
                                @method('PATCH')

                                <button
                                    type="submit"
                                    class="flex w-full items-center justify-center rounded-xl bg-emerald-600 px-5 py-3 text-sm font-bold text-white shadow-sm transition hover:bg-emerald-700"
                                >
                                    有効化する
                                </button>
                            </form>
                        @endif

                        <a
                            href="{{ route('admin.users.index') }}"
                            class="flex w-full items-center justify-center rounded-xl border border-slate-300 bg-white px-5 py-3 text-sm font-bold text-slate-700 transition hover:bg-slate-50"
                        >
                            一覧へ戻る
                        </a>
                    </div>
                </section>

                {{-- Notes --}}
                <section class="rounded-2xl border border-amber-100 bg-amber-50 p-6">
                    <h2 class="text-lg font-bold text-amber-900">
                        操作時の注意
                    </h2>

                    <ul class="mt-3 space-y-2 text-sm leading-7 text-amber-800">
                        <li>・停止中のユーザーは、必要に応じてログイン制限や投稿制限の対象にしてください。</li>
                        <li>・停止前に通報内容やメッセージ履歴を確認してください。</li>
                        <li>・管理者ユーザーを停止する場合は、運用上問題がないか確認してください。</li>
                    </ul>
                </section>
            </aside>
        </div>
    </div>
</div>
@endsection
