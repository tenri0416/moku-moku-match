@extends('layouts.app')

@section('title', '募集一覧')

@section('content')
<div class="min-h-screen bg-slate-50">
    <div class="mx-auto max-w-7xl px-4 py-10 sm:px-6 lg:px-8">
        {{-- Header --}}
        <div class="mb-8 flex flex-col gap-4 sm:flex-row sm:items-end sm:justify-between">
            <div>
                <p class="text-sm font-bold text-indigo-600">WORK POSTS</p>
                <h1 class="mt-2 text-3xl font-bold text-slate-900">
                    募集一覧
                </h1>
                <p class="mt-2 text-slate-600">
                    一緒に黙々作業・勉強・情報交換できる相手を探しましょう。
                </p>
            </div>

            @auth
            <a href="{{ route('work-posts.create') }}"
                class="inline-flex items-center justify-center rounded-xl bg-indigo-600 px-5 py-3 text-sm font-bold text-white shadow-sm transition hover:bg-indigo-700">
                募集を作成する
            </a>
            @else
            <a href="{{ route('login') }}"
                class="inline-flex items-center justify-center rounded-xl bg-indigo-600 px-5 py-3 text-sm font-bold text-white shadow-sm transition hover:bg-indigo-700">
                ログインして募集する
            </a>
            @endauth
        </div>

        {{-- Search --}}
        <section class="mb-8 rounded-2xl bg-white p-6 shadow-sm ring-1 ring-slate-200">
            <div class="mb-5">
                <h2 class="text-lg font-bold text-slate-900">
                    募集を検索
                </h2>
                <p class="mt-1 text-sm text-slate-500">
                    キーワード、目的、開催形式などで絞り込みできます。
                </p>
            </div>

            <form method="GET" action="{{ route('work-posts.index') }}">
                <div class="grid gap-4 md:grid-cols-2 lg:grid-cols-3">
                    <div>
                        <label for="keyword" class="mb-2 block text-sm font-bold text-slate-700">
                            キーワード
                        </label>
                        <input type="text" id="keyword" name="keyword" value="{{ request('keyword') }}"
                            placeholder="タイトル・本文から検索"
                            class="block w-full rounded-xl border-slate-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                    </div>

                    <div>
                        <label for="purpose" class="mb-2 block text-sm font-bold text-slate-700">
                            目的
                        </label>
                        <input type="text" id="purpose" name="purpose" value="{{ request('purpose') }}"
                            placeholder="例：黙々作業、勉強、情報交換"
                            class="block w-full rounded-xl border-slate-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                    </div>

                    <div>
                        <label for="location_type" class="mb-2 block text-sm font-bold text-slate-700">
                            開催形式
                        </label>
                        <select id="location_type" name="location_type"
                            class="block w-full rounded-xl border-slate-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                            <option value="">すべて</option>
                            <option value="online" @selected(request('location_type')==='online' )>
                                オンライン
                            </option>
                            <option value="offline" @selected(request('location_type')==='offline' )>
                                オフライン
                            </option>
                            <option value="both" @selected(request('location_type')==='both' )>
                                どちらでも可
                            </option>
                        </select>
                    </div>

                    <div>
                        <label for="prefecture_id" class="mb-2 block text-sm font-bold text-slate-700">
                            都道府県
                        </label>

                        <select id="prefecture_id" name="prefecture_id"
                            class="block w-full rounded-xl border-slate-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                            <option value="">すべて</option>
                            @foreach ($prefectures as $prefecture)
                            <option value="{{ $prefecture->id }}" @selected((string) request('prefecture_id')===(string)
                                $prefecture->id)
                                >
                                {{ $prefecture->name }}
                            </option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label for="time_zone" class="mb-2 block text-sm font-bold text-slate-700">
                            時間帯
                        </label>
                        <select id="time_zone" name="time_zone"
                            class="block w-full rounded-xl border-slate-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                            <option value="">すべて</option>
                            <option value="morning" @selected(request('time_zone')==='morning' )>
                                朝
                            </option>
                            <option value="daytime" @selected(request('time_zone')==='daytime' )>
                                昼
                            </option>
                            <option value="night" @selected(request('time_zone')==='night' )>
                                夜
                            </option>
                        </select>
                    </div>

                    <div class="flex items-end gap-3">
                        <button type="submit"
                            class="inline-flex w-full items-center justify-center rounded-xl bg-indigo-600 px-5 py-3 text-sm font-bold text-white shadow-sm transition hover:bg-indigo-700">
                            検索
                        </button>

                        <a href="{{ route('work-posts.index') }}"
                            class="inline-flex w-full items-center justify-center rounded-xl border border-slate-300 bg-white px-5 py-3 text-sm font-bold text-slate-700 transition hover:bg-slate-50">
                            クリア
                        </a>
                    </div>
                </div>
            </form>
        </section>

        {{-- Guest Notice --}}
        @guest
        <div class="mb-8 rounded-2xl border border-indigo-100 bg-indigo-50 p-5">
            <p class="text-sm leading-7 text-indigo-900">
                募集の閲覧はログインなしで可能です。
                募集作成や参加申請をする場合は
                <a href="{{ route('login') }}" class="font-bold underline">
                    ログイン
                </a>
                してください。
            </p>
        </div>
        @endguest

        {{-- List --}}
        <section>
            <div class="mb-5 flex items-center justify-between">
                <h2 class="text-xl font-bold text-slate-900">
                    募集中の投稿
                </h2>

                @if (method_exists($workPosts, 'total'))
                <p class="text-sm text-slate-500">
                    {{ $workPosts->total() }}件
                </p>
                @endif
            </div>

            <div class="grid gap-6 md:grid-cols-2 lg:grid-cols-3">
                @forelse ($workPosts as $workPost)
                <article
                    class="rounded-2xl bg-white p-6 shadow-sm ring-1 ring-slate-200 transition hover:-translate-y-1 hover:shadow-md">
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

                    <h3 class="text-lg font-bold leading-7 text-slate-900">
                        <a href="{{ route('work-posts.show', $workPost) }}" class="hover:text-indigo-600">
                            {{ $workPost->title }}
                        </a>
                    </h3>

                    <div class="mt-4 space-y-2 text-sm text-slate-600">
                        @php
                        $profile = $workPost->user->profile;
                        $avatarPath = $profile?->avatar_path;
                        $avatarUrl = $avatarPath
                        ? asset('storage/' . $avatarPath)
                        : asset('images/default-avatar.png');
                        $displayName = $profile?->display_name ?? $workPost->user->name;
                        @endphp

                        <div class="mt-4 flex items-center gap-3 rounded-xl bg-slate-50 p-3">
                            <img src="{{ $avatarUrl }}" alt="{{ $displayName }}のプロフィール画像"
                                class="h-12 w-12 flex-shrink-0 rounded-full border border-slate-200 bg-white object-cover">

                            <div class="min-w-0">
                                <p class="text-xs font-bold text-slate-500">
                                    投稿者
                                </p>
                                <p class="truncate text-sm font-bold text-slate-900">
                                    {{ $displayName }}
                                </p>
                            </div>
                        </div>

                        <p>
                            開始日時：
                            <span class="font-semibold text-slate-800">
                                {{ $workPost->start_at ? $workPost->start_at->format('Y/m/d H:i') : '未定' }}
                            </span>
                        </p>

                        @if ($workPost->prefecture?->name)
                        <p>
                            都道府県：
                            <span class="font-semibold text-slate-800">
                                {{ $workPost->prefecture->name }}
                            </span>
                        </p>
                        @endif
                    </div>

                    <div class="mt-5 flex items-center justify-between">
                        <a href="{{ route('work-posts.show', $workPost) }}"
                            class="text-sm font-bold text-indigo-600 hover:text-indigo-700">
                            詳細を見る →
                        </a>

                        @if ($workPost->start_at)
                        <span class="text-xs text-slate-400">
                            {{ $workPost->start_at->diffForHumans() }}
                        </span>
                        @endif
                    </div>
                </article>
                @empty
                <div
                    class="rounded-2xl bg-white p-10 text-center shadow-sm ring-1 ring-slate-200 md:col-span-2 lg:col-span-3">
                    <div class="mx-auto flex h-14 w-14 items-center justify-center rounded-full bg-slate-100 text-2xl">
                        🔍
                    </div>

                    <h3 class="mt-4 text-lg font-bold text-slate-900">
                        募集がありません
                    </h3>

                    <p class="mt-2 text-sm leading-6 text-slate-600">
                        条件に一致する募集がありませんでした。
                        検索条件を変更するか、新しい募集を作成してください。
                    </p>

                    <div class="mt-6 flex flex-wrap justify-center gap-3">
                        <a href="{{ route('work-posts.index') }}"
                            class="inline-flex items-center justify-center rounded-xl border border-slate-300 bg-white px-5 py-3 text-sm font-bold text-slate-700 transition hover:bg-slate-50">
                            条件をクリア
                        </a>

                        @auth
                        <a href="{{ route('work-posts.create') }}"
                            class="inline-flex items-center justify-center rounded-xl bg-indigo-600 px-5 py-3 text-sm font-bold text-white transition hover:bg-indigo-700">
                            募集を作成する
                        </a>
                        @endauth
                    </div>
                </div>
                @endforelse
            </div>

            <div class="mt-8">
                {{ $workPosts->links() }}
            </div>
        </section>
    </div>
</div>
@endsection
