@extends('layouts.admin')

@section('title', 'ユーザー詳細')

@section('content')
<div class="min-h-screen bg-slate-50">
    <div class="mx-auto w-full max-w-5xl px-3 py-6 sm:px-6 sm:py-10 lg:px-8">
        {{-- Header --}}
        <div class="mb-6 flex flex-col gap-4 sm:mb-8 sm:flex-row sm:items-start sm:justify-between">
            <div class="min-w-0">
                <p class="text-xs font-bold tracking-wide text-indigo-600 sm:text-sm">USER DETAIL</p>

                <h1 class="mt-2 break-words text-2xl font-bold leading-tight text-slate-900 sm:text-3xl">
                    ユーザー詳細 #{{ $user->id }}
                </h1>

                <p class="mt-2 text-sm leading-6 text-slate-600 sm:text-base">
                    ユーザー情報、プロフィール、投稿・申請状況を確認できます。
                </p>
            </div>

            <a
                href="{{ route('admin.users.index') }}"
                class="inline-flex w-full items-center justify-center rounded-xl border border-slate-300 bg-white px-4 py-3 text-sm font-bold text-slate-700 transition hover:bg-slate-50 sm:w-auto sm:px-5"
            >
                ユーザー一覧へ戻る
            </a>
        </div>

        <div class="grid grid-cols-1 gap-5 lg:grid-cols-3 lg:gap-6">
            {{-- Main --}}
            <div class="min-w-0 space-y-5 lg:col-span-2 lg:space-y-6">
                {{-- Basic Info --}}
                <section class="rounded-2xl bg-white p-4 shadow-sm ring-1 ring-slate-200 sm:p-8">
                    <div class="flex flex-col gap-4 sm:flex-row sm:items-start sm:justify-between">
                        <div class="min-w-0">
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

                            <h2 class="break-words text-xl font-bold leading-7 text-slate-900 sm:text-2xl">
                                {{ $user->profile->display_name ?? $user->name }}
                            </h2>

                            <p class="mt-2 break-all text-sm leading-6 text-slate-600">
                                {{ $user->email }}
                            </p>
                        </div>
                    </div>

                    <dl class="mt-6 grid grid-cols-1 gap-3 sm:mt-8 sm:grid-cols-2 sm:gap-4">
                        <div class="rounded-xl bg-slate-50 p-4">
                            <dt class="text-xs font-bold text-slate-500 sm:text-sm">
                                ユーザーID
                            </dt>
                            <dd class="mt-1 break-words font-semibold text-slate-900">
                                {{ $user->id }}
                            </dd>
                        </div>

                        <div class="rounded-xl bg-slate-50 p-4">
                            <dt class="text-xs font-bold text-slate-500 sm:text-sm">
                                名前
                            </dt>
                            <dd class="mt-1 break-words font-semibold text-slate-900">
                                {{ $user->name }}
                            </dd>
                        </div>

                        <div class="rounded-xl bg-slate-50 p-4">
                            <dt class="text-xs font-bold text-slate-500 sm:text-sm">
                                メールアドレス
                            </dt>
                            <dd class="mt-1 break-all font-semibold text-slate-900">
                                {{ $user->email }}
                            </dd>
                        </div>

                        <div class="rounded-xl bg-slate-50 p-4">
                            <dt class="text-xs font-bold text-slate-500 sm:text-sm">
                                登録日時
                            </dt>
                            <dd class="mt-1 break-words font-semibold text-slate-900">
                                {{ $user->created_at->format('Y/m/d H:i') }}
                            </dd>
                        </div>
                    </dl>
                </section>

                {{-- Profile --}}
                <section class="rounded-2xl bg-white p-4 shadow-sm ring-1 ring-slate-200 sm:p-8">
                    <h2 class="text-lg font-bold text-slate-900 sm:text-xl">
                        プロフィール
                    </h2>

                    @if ($user->profile)
                        <dl class="mt-5 grid grid-cols-1 gap-3 sm:grid-cols-2 sm:gap-4">
                            <div class="rounded-xl bg-slate-50 p-4">
                                <dt class="text-xs font-bold text-slate-500 sm:text-sm">
                                    表示名
                                </dt>
                                <dd class="mt-1 break-words font-semibold text-slate-900">
                                    {{ $user->profile->display_name }}
                                </dd>
                            </div>

                            <div class="rounded-xl bg-slate-50 p-4">
                                <dt class="text-xs font-bold text-slate-500 sm:text-sm">
                                    職種
                                </dt>
                                <dd class="mt-1 break-words font-semibold text-slate-900">
                                    {{ $user->profile->job_type ?? '未設定' }}
                                </dd>
                            </div>

                            <div class="rounded-xl bg-slate-50 p-4">
                                <dt class="text-xs font-bold text-slate-500 sm:text-sm">
                                    都道府県
                                </dt>
                                <dd class="mt-1 break-words font-semibold text-slate-900">
                                    {{ $user->profile?->prefecture?->name ?? '未設定' }}
                                </dd>
                            </div>

                            <div class="rounded-xl bg-slate-50 p-4">
                                <dt class="text-xs font-bold text-slate-500 sm:text-sm">
                                    利用目的
                                </dt>
                                <dd class="mt-1 break-words font-semibold text-slate-900">
                                    {{ $user->profile->purpose ?? '未設定' }}
                                </dd>
                            </div>

                            <div class="rounded-xl bg-slate-50 p-4 sm:col-span-2">
                                <dt class="text-xs font-bold text-slate-500 sm:text-sm">
                                    スキル
                                </dt>
                                <dd class="mt-1 break-words leading-7 text-slate-900">
                                    {!! nl2br(e($user->profile->skills ?? '未設定')) !!}
                                </dd>
                            </div>

                            <div class="rounded-xl bg-slate-50 p-4 sm:col-span-2">
                                <dt class="text-xs font-bold text-slate-500 sm:text-sm">
                                    自己紹介
                                </dt>
                                <dd class="mt-1 break-words leading-7 text-slate-900">
                                    {!! nl2br(e($user->profile->bio ?? '未設定')) !!}
                                </dd>
                            </div>

                            <div class="rounded-xl bg-slate-50 p-4 sm:col-span-2">
                                <dt class="text-xs font-bold text-slate-500 sm:text-sm">
                                    希望作業スタイル
                                </dt>
                                <dd class="mt-1 break-words leading-7 text-slate-900">
                                    {!! nl2br(e($user->profile->work_style ?? '未設定')) !!}
                                </dd>
                            </div>
                        </dl>
                    @else
                        <div class="mt-5 rounded-xl bg-amber-50 p-4 sm:p-5">
                            <p class="text-sm font-semibold leading-6 text-amber-800">
                                プロフィールは未登録です。
                            </p>
                        </div>
                    @endif
                </section>

                {{-- Activity --}}
                <section class="rounded-2xl bg-white p-4 shadow-sm ring-1 ring-slate-200 sm:p-8">
                    <h2 class="text-lg font-bold text-slate-900 sm:text-xl">
                        利用状況
                    </h2>

                    <div class="mt-5 grid grid-cols-1 gap-3 sm:grid-cols-2 sm:gap-4">
                        <div class="rounded-xl bg-indigo-50 p-4 sm:p-5">
                            <p class="text-sm font-bold text-indigo-700">
                                作成した募集
                            </p>
                            <p class="mt-2 text-2xl font-black text-indigo-900 sm:text-3xl">
                                {{ $user->workPosts->count() }}
                            </p>
                        </div>

                        <div class="rounded-xl bg-emerald-50 p-4 sm:p-5">
                            <p class="text-sm font-bold text-emerald-700">
                                参加申請
                            </p>
                            <p class="mt-2 text-2xl font-black text-emerald-900 sm:text-3xl">
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
                                        <p class="break-words font-semibold leading-6 text-slate-900">
                                            {{ $workPost->title }}
                                        </p>

                                        <p class="mt-1 text-sm text-slate-500">
                                            {{ $workPost->created_at->format('Y/m/d H:i') }}
                                        </p>

                                        <a
                                            href="{{ route('admin.work-posts.show', $workPost) }}"
                                            class="mt-3 inline-flex text-sm font-bold text-indigo-600 hover:text-indigo-700"
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
            <aside class="min-w-0 space-y-5 lg:space-y-6">
                {{-- Actions --}}
                <section class="rounded-2xl bg-white p-4 shadow-sm ring-1 ring-slate-200 sm:p-6">
                    <h2 class="text-lg font-bold text-slate-900">
                        管理操作
                    </h2>

                    <div class="mt-5 space-y-3">
                        @if (session('admin_impersonation.active'))
                            <div class="rounded-xl border border-amber-200 bg-amber-50 p-4 text-sm font-semibold leading-7 text-amber-800">
                                現在、別ユーザーとして代理ログイン中です。ユーザー画面左側の「管理者でログイン中」から終了してください。
                            </div>
                        @else
                        <form method="POST" action="{{ route('admin.users.impersonate.start', $user) }}">
                            @csrf
                        
                            <button
                                type="submit"
                                class="flex w-full touch-manipulation items-center justify-center rounded-xl bg-indigo-600 px-5 py-3 text-sm font-bold text-white shadow-sm transition hover:bg-indigo-700 active:bg-indigo-800"
                            >
                                このユーザーとして操作する
                            </button>
                        </form>
                        @endif

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
                <section class="rounded-2xl border border-amber-100 bg-amber-50 p-4 sm:p-6">
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
