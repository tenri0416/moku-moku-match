@extends('layouts.app')

@section('content')
@php
    $profile = $user->profile;
    $avatarPath = $profile?->avatar_path;
    $avatarUrl = $avatarPath
        ? asset('storage/' . $avatarPath)
        : asset('images/default-avatar.png');

    $displayName = $profile?->display_name ?? $user->name;
    $jobType = $profile?->job_type ?? '職種未設定';
    $prefectureName = $profile?->prefecture?->name;
@endphp

<div class="min-h-screen bg-slate-50">
    <div class="mx-auto max-w-5xl px-4 py-10 sm:px-6 lg:px-8">

        {{-- Header --}}
        <div class="mb-8">
            <p class="text-sm font-bold text-indigo-600">
                USER PROFILE
            </p>
            <h1 class="mt-2 text-3xl font-bold text-slate-900">
                プロフィール
            </h1>
            <p class="mt-2 text-slate-600">
                ユーザーの自己紹介やスキル、トレーニング状況を確認できます。
            </p>
        </div>

        {{-- Profile Card --}}
        <section class="rounded-2xl bg-white p-6 shadow-sm ring-1 ring-slate-200">
            <div class="flex flex-col gap-6 md:flex-row md:items-center md:justify-between">

                {{-- User Info --}}
                <div class="flex flex-col gap-5 sm:flex-row sm:items-center">
                    <img
                        src="{{ $avatarUrl }}"
                        alt="{{ $displayName }}のプロフィール画像"
                        class="mx-auto h-24 w-24 flex-shrink-0 rounded-full border border-slate-200 bg-white object-cover sm:mx-0"
                    >

                    <div class="text-center sm:text-left">
                        <h2 class="text-2xl font-bold text-slate-900">
                            {{ $displayName }}
                        </h2>

                        <p class="mt-2 text-sm font-semibold text-slate-600">
                            {{ $jobType }}
                        </p>

                        @if ($prefectureName)
                            <p class="mt-1 text-sm text-slate-500">
                                {{ $prefectureName }}
                            </p>
                        @endif

                        <div class="mt-4 flex flex-wrap justify-center gap-2 sm:justify-start">
                            <span class="rounded-full bg-indigo-50 px-3 py-1 text-xs font-bold text-indigo-700">
                                総ポイント {{ $totalPoints ?? 0 }}pt
                            </span>

                            <span class="rounded-full bg-emerald-50 px-3 py-1 text-xs font-bold text-emerald-700">
                                月間 {{ $monthlyPoints ?? 0 }}pt
                            </span>

                            <span class="rounded-full bg-slate-100 px-3 py-1 text-xs font-bold text-slate-600">
                                {{ $trainingCount ?? 0 }}回実施
                            </span>
                        </div>
                    </div>
                </div>

                {{-- Action --}}
                <div class="flex flex-col gap-3 md:min-w-[190px]">
                    @auth
                        @if (auth()->id() !== $user->id)
                            <a
                                href="{{ route('messages.users.show', $user) }}"
                                class="inline-flex items-center justify-center rounded-xl bg-indigo-600 px-5 py-3 text-sm font-bold text-white shadow-sm transition hover:bg-indigo-700"
                            >
                                メッセージを送る
                            </a>
                        @else
                            <a
                                href="{{ route('profile.edit') }}"
                                class="inline-flex items-center justify-center rounded-xl bg-slate-900 px-5 py-3 text-sm font-bold text-white shadow-sm transition hover:bg-slate-800"
                            >
                                プロフィールを編集
                            </a>
                        @endif
                    @else
                        <a
                            href="{{ route('login') }}"
                            class="inline-flex items-center justify-center rounded-xl bg-indigo-600 px-5 py-3 text-sm font-bold text-white shadow-sm transition hover:bg-indigo-700"
                        >
                            ログインしてメッセージ
                        </a>
                    @endauth

                    <a
                        href="{{ route('trainings.ranking') }}"
                        class="inline-flex items-center justify-center rounded-xl border border-slate-300 bg-white px-5 py-3 text-sm font-bold text-slate-700 transition hover:bg-slate-50"
                    >
                        ランキングへ戻る
                    </a>
                </div>
            </div>
        </section>

        {{-- Details --}}
        <div class="mt-8 grid gap-6 lg:grid-cols-2">
            <section class="rounded-2xl bg-white p-6 shadow-sm ring-1 ring-slate-200">
                <h3 class="text-lg font-bold text-slate-900">
                    自己紹介
                </h3>

                <div class="mt-4 rounded-xl bg-slate-50 p-4 text-sm leading-7 text-slate-700 whitespace-pre-line">
                    {{ $profile?->bio ?: '自己紹介はまだ登録されていません。' }}
                </div>
            </section>

            <section class="rounded-2xl bg-white p-6 shadow-sm ring-1 ring-slate-200">
                <h3 class="text-lg font-bold text-slate-900">
                    スキル
                </h3>

                <div class="mt-4 rounded-xl bg-slate-50 p-4 text-sm leading-7 text-slate-700 whitespace-pre-line">
                    {{ $profile?->skills ?: 'スキルはまだ登録されていません。' }}
                </div>
            </section>

            <section class="rounded-2xl bg-white p-6 shadow-sm ring-1 ring-slate-200">
                <h3 class="text-lg font-bold text-slate-900">
                    利用目的
                </h3>

                <div class="mt-4 rounded-xl bg-slate-50 p-4 text-sm leading-7 text-slate-700 whitespace-pre-line">
                    {{ $profile?->purpose ?: '利用目的はまだ登録されていません。' }}
                </div>
            </section>

            <section class="rounded-2xl bg-white p-6 shadow-sm ring-1 ring-slate-200">
                <h3 class="text-lg font-bold text-slate-900">
                    作業スタイル
                </h3>

                <div class="mt-4 rounded-xl bg-slate-50 p-4 text-sm leading-7 text-slate-700 whitespace-pre-line">
                    {{ $profile?->work_style ?: '作業スタイルはまだ登録されていません。' }}
                </div>
            </section>
        </div>

        {{-- Bottom CTA --}}
        @auth
            @if (auth()->id() !== $user->id)
                <section class="mt-8 rounded-2xl bg-indigo-600 p-6 text-center shadow-sm">
                    <h3 class="text-xl font-bold text-white">
                        {{ $displayName }} さんにメッセージを送りますか？
                    </h3>

                    <p class="mt-2 text-sm text-indigo-100">
                        募集に関係なく、直接メッセージを送ることができます。
                    </p>

                    <a
                        href="{{ route('messages.users.show', $user) }}"
                        class="mt-5 inline-flex items-center justify-center rounded-xl bg-white px-6 py-3 text-sm font-bold text-indigo-600 transition hover:bg-indigo-50"
                    >
                        メッセージを送る
                    </a>
                </section>
            @endif
        @else
            <section class="mt-8 rounded-2xl bg-indigo-600 p-6 text-center shadow-sm">
                <h3 class="text-xl font-bold text-white">
                    メッセージを送るにはログインが必要です
                </h3>

                <p class="mt-2 text-sm text-indigo-100">
                    ログインすると、このユーザーに直接メッセージを送れます。
                </p>

                <a
                    href="{{ route('login') }}"
                    class="mt-5 inline-flex items-center justify-center rounded-xl bg-white px-6 py-3 text-sm font-bold text-indigo-600 transition hover:bg-indigo-50"
                >
                    ログインする
                </a>
            </section>
        @endauth
    </div>
</div>
@endsection
