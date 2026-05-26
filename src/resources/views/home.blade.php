@extends('layouts.app')

@section('title', 'MokuMoku Match')

@section('content')
<div class="min-h-screen bg-slate-50">
    {{-- Hero --}}
    <section class="relative overflow-hidden bg-gradient-to-br from-indigo-600 via-blue-600 to-sky-500">
        <div class="absolute inset-0 opacity-20">
            <div class="absolute -top-24 -left-24 h-72 w-72 rounded-full bg-white blur-3xl"></div>
            <div class="absolute bottom-0 right-0 h-96 w-96 rounded-full bg-cyan-200 blur-3xl"></div>
        </div>

        <div class="relative mx-auto max-w-7xl px-4 py-20 sm:px-6 lg:px-8">
            <div class="max-w-3xl">
                <p class="inline-flex rounded-full bg-white/15 px-4 py-2 text-sm font-semibold text-white ring-1 ring-white/30">
                    フルリモート作業仲間マッチングサービス
                </p>

                <h1 class="mt-6 text-4xl font-bold leading-tight tracking-tight text-white sm:text-5xl lg:text-6xl">
                    ひとりで頑張るリモートワーカーに、作業仲間を。
                </h1>

                <p class="mt-6 text-lg leading-8 text-blue-50">
                    MokuMoku Matchは、フルリモートで働くITエンジニアや学習者が、
                    一緒に黙々作業・勉強・情報交換できる相手を探すためのサービスです。
                </p>

                <div class="mt-10 flex flex-wrap gap-4">
                    @guest
                        <a
                            href="{{ route('register') }}"
                            class="inline-flex items-center justify-center rounded-xl bg-white px-6 py-3 text-sm font-bold text-indigo-700 shadow-lg shadow-indigo-900/20 transition hover:bg-indigo-50"
                        >
                            会員登録する
                        </a>

                        <a
                            href="{{ route('login') }}"
                            class="inline-flex items-center justify-center rounded-xl border border-white/40 px-6 py-3 text-sm font-bold text-white transition hover:bg-white/10"
                        >
                            ログイン
                        </a>
                    @endguest

                    <a
                        href="{{ route('work-posts.index') }}"
                        class="inline-flex items-center justify-center rounded-xl bg-indigo-950/30 px-6 py-3 text-sm font-bold text-white ring-1 ring-white/20 transition hover:bg-indigo-950/40"
                    >
                        募集を見る
                    </a>
                </div>
            </div>
        </div>
    </section>

    {{-- Recommend --}}
    <section class="mx-auto max-w-7xl px-4 py-16 sm:px-6 lg:px-8">
        <div class="mb-8">
            <p class="text-sm font-bold text-indigo-600">RECOMMEND</p>
            <h2 class="mt-2 text-3xl font-bold text-slate-900">
                こんな方におすすめ
            </h2>
            <p class="mt-3 text-slate-600">
                一人で頑張る時間を、少しだけ誰かと共有できる場所を目指しています。
            </p>
        </div>

        <div class="grid gap-5 md:grid-cols-2 lg:grid-cols-3">
            <div class="rounded-2xl bg-white p-6 shadow-sm ring-1 ring-slate-200">
                <div class="flex h-12 w-12 items-center justify-center rounded-xl bg-indigo-100 text-xl">
                    🏠
                </div>
                <h3 class="mt-4 text-lg font-bold text-slate-900">
                    孤独感がある方
                </h3>
                <p class="mt-2 text-sm leading-7 text-slate-600">
                    自宅で一人作業をしていて、人との接点が少なくなっている方。
                </p>
            </div>

            <div class="rounded-2xl bg-white p-6 shadow-sm ring-1 ring-slate-200">
                <div class="flex h-12 w-12 items-center justify-center rounded-xl bg-sky-100 text-xl">
                    ⏱️
                </div>
                <h3 class="mt-4 text-lg font-bold text-slate-900">
                    モチベーション維持が難しい方
                </h3>
                <p class="mt-2 text-sm leading-7 text-slate-600">
                    誰かと同じ時間に作業することで、作業習慣を作りたい方。
                </p>
            </div>

            <div class="rounded-2xl bg-white p-6 shadow-sm ring-1 ring-slate-200">
                <div class="flex h-12 w-12 items-center justify-center rounded-xl bg-emerald-100 text-xl">
                    💻
                </div>
                <h3 class="mt-4 text-lg font-bold text-slate-900">
                    勉強仲間がほしい方
                </h3>
                <p class="mt-2 text-sm leading-7 text-slate-600">
                    プログラミングや資格勉強を、一人ではなく誰かと継続したい方。
                </p>
            </div>

            <div class="rounded-2xl bg-white p-6 shadow-sm ring-1 ring-slate-200">
                <div class="flex h-12 w-12 items-center justify-center rounded-xl bg-amber-100 text-xl">
                    🤝
                </div>
                <h3 class="mt-4 text-lg font-bold text-slate-900">
                    黙々作業したい方
                </h3>
                <p class="mt-2 text-sm leading-7 text-slate-600">
                    会話は少なめで、集中する時間を一緒に作りたい方。
                </p>
            </div>

            <div class="rounded-2xl bg-white p-6 shadow-sm ring-1 ring-slate-200">
                <div class="flex h-12 w-12 items-center justify-center rounded-xl bg-rose-100 text-xl">
                    📚
                </div>
                <h3 class="mt-4 text-lg font-bold text-slate-900">
                    学習を継続したい方
                </h3>
                <p class="mt-2 text-sm leading-7 text-slate-600">
                    Laravel、React、AWSなど、技術学習を続けたい方。
                </p>
            </div>

            <div class="rounded-2xl bg-white p-6 shadow-sm ring-1 ring-slate-200">
                <div class="flex h-12 w-12 items-center justify-center rounded-xl bg-purple-100 text-xl">
                    💬
                </div>
                <h3 class="mt-4 text-lg font-bold text-slate-900">
                    情報交換したい方
                </h3>
                <p class="mt-2 text-sm leading-7 text-slate-600">
                    働き方、案件、技術キャッチアップについて話せる相手がほしい方。
                </p>
            </div>
        </div>
    </section>

    {{-- Features --}}
    <section class="bg-white">
        <div class="mx-auto max-w-7xl px-4 py-16 sm:px-6 lg:px-8">
            <div class="mb-8">
                <p class="text-sm font-bold text-indigo-600">FEATURES</p>
                <h2 class="mt-2 text-3xl font-bold text-slate-900">
                    MokuMoku Matchでできること
                </h2>
            </div>

            <div class="grid gap-6 lg:grid-cols-3">
                <article class="rounded-2xl border border-slate-200 p-6">
                    <h3 class="text-xl font-bold text-slate-900">
                        黙々作業
                    </h3>
                    <p class="mt-3 text-sm leading-7 text-slate-600">
                        ZoomやGoogle Meetなどをつないで、最初と最後だけ会話し、
                        作業中は集中して黙々作業できます。
                    </p>
                </article>

                <article class="rounded-2xl border border-slate-200 p-6">
                    <h3 class="text-xl font-bold text-slate-900">
                        勉強仲間探し
                    </h3>
                    <p class="mt-3 text-sm leading-7 text-slate-600">
                        Laravel、React、AWSなど、同じ技術を学ぶ仲間を探せます。
                    </p>
                </article>

                <article class="rounded-2xl border border-slate-200 p-6">
                    <h3 class="text-xl font-bold text-slate-900">
                        情報交換
                    </h3>
                    <p class="mt-3 text-sm leading-7 text-slate-600">
                        フリーランス案件、働き方、技術キャッチアップについて気軽に話せる相手を探せます。
                    </p>
                </article>
            </div>
        </div>
    </section>

    {{-- Latest Work Posts --}}
    <section class="mx-auto max-w-7xl px-4 py-16 sm:px-6 lg:px-8">
        <div class="mb-8 flex flex-col gap-4 sm:flex-row sm:items-end sm:justify-between">
            <div>
                <p class="text-sm font-bold text-indigo-600">NEW POSTS</p>
                <h2 class="mt-2 text-3xl font-bold text-slate-900">
                    新着募集
                </h2>
                <p class="mt-3 text-slate-600">
                    最近投稿された作業・勉強仲間の募集です。
                </p>
            </div>

            <a
                href="{{ route('work-posts.index') }}"
                class="inline-flex items-center justify-center rounded-xl bg-indigo-600 px-5 py-3 text-sm font-bold text-white shadow-sm transition hover:bg-indigo-700"
            >
                募集一覧を見る
            </a>
        </div>

        <div class="grid gap-6 md:grid-cols-2 lg:grid-cols-3">
            @forelse ($latestWorkPosts as $workPost)
                <article class="rounded-2xl bg-white p-6 shadow-sm ring-1 ring-slate-200 transition hover:-translate-y-1 hover:shadow-md">
                    <div class="mb-4 flex items-center gap-2">
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
                            <span class="rounded-full bg-emerald-50 px-3 py-1 text-xs font-bold text-emerald-700">
                                どちらでも可
                            </span>
                        @else
                            <span class="rounded-full bg-slate-100 px-3 py-1 text-xs font-bold text-slate-600">
                                未設定
                            </span>
                        @endif
                    </div>

                    <h3 class="text-lg font-bold leading-7 text-slate-900">
                        <a href="{{ route('work-posts.show', $workPost) }}" class="hover:text-indigo-600">
                            {{ $workPost->title }}
                        </a>
                    </h3>

                    <div class="mt-4 space-y-2 text-sm text-slate-600">
                        <p>
                            投稿者：
                            <span class="font-semibold text-slate-800">
                                {{ $workPost->user->profile->display_name ?? $workPost->user->name }}
                            </span>
                        </p>

                        <p>
                            開始日時：
                            <span class="font-semibold text-slate-800">
                                {{ $workPost->start_at ? $workPost->start_at->format('Y/m/d H:i') : '未定' }}
                            </span>
                        </p>
                    </div>

                    <div class="mt-5">
                        <a
                            href="{{ route('work-posts.show', $workPost) }}"
                            class="text-sm font-bold text-indigo-600 hover:text-indigo-700"
                        >
                            詳細を見る →
                        </a>
                    </div>
                </article>
            @empty
                <div class="rounded-2xl bg-white p-8 text-center shadow-sm ring-1 ring-slate-200 md:col-span-2 lg:col-span-3">
                    <p class="text-slate-600">
                        現在、募集中の投稿はありません。
                    </p>

                    @auth
                        <div class="mt-5">
                            <a
                                href="{{ route('work-posts.create') }}"
                                class="inline-flex items-center justify-center rounded-xl bg-indigo-600 px-5 py-3 text-sm font-bold text-white transition hover:bg-indigo-700"
                            >
                                最初の募集を作成する
                            </a>
                        </div>
                    @endauth
                </div>
            @endforelse
        </div>
    </section>
</div>
@endsection
