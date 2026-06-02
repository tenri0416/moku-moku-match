@extends('layouts.app')

@section('title', 'MokuMoku Match')

@section('content')
@php
    $rankingMode = request('ranking_mode', 'monthly');

    $homeWorkPosts = $latestWorkPosts
        ?? \App\Models\WorkPost::query()
            ->with(['user.profile'])
            ->latest()
            ->take(8)
            ->get();

    $homeArticles = $latestArticles
        ?? (
            class_exists(\App\Models\Article::class)
                ? \App\Models\Article::query()
                    ->with('prefecture')
                    ->latest('published_at')
                    ->take(3)
                    ->get()
                : collect()
        );

    $allWorkPostCount = class_exists(\App\Models\WorkPost::class)
        ? \App\Models\WorkPost::query()->count()
        : $homeWorkPosts->count();

    $homeMonthlyTrainingRankings = $homeMonthlyTrainingRankings
        ?? (
            class_exists(\App\Models\UserTrainingPointHistory::class)
                ? \App\Models\UserTrainingPointHistory::query()
                    ->select('user_id')
                    ->selectRaw('SUM(points) as total_points')
                    ->selectRaw('COUNT(*) as training_count')
                    ->whereBetween('earned_on', [
                        now()->startOfMonth()->toDateString(),
                        now()->endOfMonth()->toDateString(),
                    ])
                    ->with('user.profile')
                    ->groupBy('user_id')
                    ->orderByDesc('total_points')
                    ->limit(20)
                    ->get()
                : collect()
        );

    $homeTotalTrainingRankings = $homeTotalTrainingRankings
        ?? (
            class_exists(\App\Models\UserTrainingPointHistory::class)
                ? \App\Models\UserTrainingPointHistory::query()
                    ->select('user_id')
                    ->selectRaw('SUM(points) as total_points')
                    ->selectRaw('COUNT(*) as training_count')
                    ->with('user.profile')
                    ->groupBy('user_id')
                    ->orderByDesc('total_points')
                    ->limit(20)
                    ->get()
                : collect()
        );

    $homeRankingUsers = $rankingMode === 'total'
        ? $homeTotalTrainingRankings
        : $homeMonthlyTrainingRankings;

    $heroImageUrl = asset('images/home-top-visual.png');

    $quickFilterLinks = [
        ['label' => 'すべて', 'params' => []],
        ['label' => '黙々作業', 'params' => ['purpose' => '黙々作業']],
        ['label' => '勉強', 'params' => ['purpose' => '勉強']],
        ['label' => '情報交換', 'params' => ['purpose' => '情報交換']],
        ['label' => '朝', 'params' => ['time_zone' => 'morning']],
        ['label' => '昼', 'params' => ['time_zone' => 'daytime']],
        ['label' => '夜', 'params' => ['time_zone' => 'night']],
        ['label' => 'オンライン', 'params' => ['location_type' => 'online']],
        ['label' => 'オフライン', 'params' => ['location_type' => 'offline']],
        ['label' => 'どちらでも可', 'params' => ['location_type' => 'both']],
    ];
@endphp



        {{-- TOP画像 --}}
        <section class="mx-auto max-w-7xl px-4 pt-8 sm:px-6 lg:px-8">
            <div class="overflow-hidden rounded-[32px] bg-white shadow-sm ring-1 ring-slate-200">
                <img
                    src="{{ $heroImageUrl }}"
                    alt="MokuMoku Match メインビジュアル"
                    class="h-64 w-full object-cover sm:h-80 lg:h-[420px]"
                >
            </div>
        </section>
    
        {{-- Main Area --}}
    {{-- Main Area --}}
    <section class="mx-auto max-w-7xl px-4 py-8 sm:px-6 lg:px-8">
        <div class="grid gap-6 xl:grid-cols-[280px_minmax(0,1fr)_320px]">
            {{-- Left Sidebar --}}
            <aside class="space-y-4">
                <a
                    href="{{ auth()->check() ? route('work-posts.create') : route('login') }}"
                    class="flex items-center justify-between rounded-[24px] bg-emerald-500 px-6 py-6 text-white shadow-sm transition hover:bg-emerald-600"
                >
                    <div>
                        <div class="text-sm font-bold opacity-90">まずは投稿</div>
                        <div class="mt-1 text-2xl font-black">募集をする</div>
                    </div>

                    <span class="text-2xl font-black">→</span>
                </a>

                <div class="rounded-[24px] bg-[#e9f7f4] p-5">
                    <h2 class="mb-4 text-lg font-black text-slate-900">
                        募集をキーワードで探す
                    </h2>
                    <form method="GET" action="{{ route('home') }}" class="space-y-3">
                        <input
                            type="text"
                            name="keyword"
                            value="{{ request('keyword') }}"
                            placeholder="キーワードを入力"
                            class="w-full rounded-xl border border-transparent bg-white px-4 py-3 text-sm font-semibold text-slate-800 outline-none ring-1 ring-slate-200 placeholder:text-slate-400 focus:ring-2 focus:ring-emerald-400"
                        >
                    
                        <button
                            type="submit"
                            class="w-full rounded-xl bg-slate-900 px-4 py-3 text-sm font-black text-white transition hover:bg-slate-800"
                        >
                            検索する
                        </button>
                    
                        @if (request()->hasAny(['keyword', 'purpose', 'location_type', 'time_zone']))
                            <a
                                href="{{ route('home') }}"
                                class="block rounded-xl border border-slate-200 bg-white px-4 py-3 text-center text-sm font-black text-slate-700 transition hover:bg-slate-50"
                            >
                                条件をリセット
                            </a>
                        @endif
                    </form>
                </div>

                <div class="rounded-[24px] bg-[#e9f7f4] p-5">
                    <h2 class="mb-4 text-lg font-black text-slate-900">
                        条件から探す
                    </h2>
                    <div class="flex flex-wrap gap-2">
                        @foreach ($quickFilterLinks as $filter)
                            <a
                                href="{{ route('home', $filter['params']) }}"
                                class="rounded-full bg-white px-4 py-2 text-sm font-bold text-slate-700 ring-1 ring-slate-200 transition hover:bg-emerald-50 hover:text-emerald-700
                                    {{ collect($filter['params'])->every(fn ($value, $key) => request($key) == $value) && count($filter['params']) > 0 ? 'bg-emerald-100 text-emerald-700 ring-emerald-300' : '' }}"
                            >
                                {{ $filter['label'] }}
                            </a>
                        @endforeach
                    </div>
                </div>

                <div class="rounded-[24px] bg-white p-5 ring-1 ring-slate-200">
                    <h2 class="mb-3 text-lg font-black text-slate-900">
                        MokuMoku Matchとは？
                    </h2>

                    <ul class="space-y-3 text-sm font-semibold leading-7 text-slate-600">
                        <li>・一緒に黙々作業できる仲間を探せる</li>
                        <li>・勉強仲間や情報交換相手も見つかる</li>
                        <li>・自己成長トレーニングで継続力も高められる</li>
                    </ul>
                </div>
            </aside>

            {{-- Center Content --}}
            <div class="min-w-0 space-y-6">
                <div class="rounded-[28px] bg-white p-6 shadow-sm ring-1 ring-slate-200">
                    <div class="flex flex-col gap-3 border-b border-slate-100 pb-5 sm:flex-row sm:items-end sm:justify-between">
                        <div>
                            <p class="text-sm font-black tracking-widest text-emerald-600">
                                RECRUITMENT
                            </p>
                            <h2 class="mt-2 text-3xl font-black text-slate-900">
                                募集一覧
                            </h2>
                            <p class="mt-2 text-sm font-semibold text-slate-500">
                                {{ number_format($allWorkPostCount) }}件の募集があります
                            </p>
                        </div>

                        <div class="flex flex-wrap gap-2 text-sm font-bold text-slate-500">
                            <a href="{{ route('work-posts.index') }}" class="rounded-full bg-slate-100 px-3 py-1.5 transition hover:bg-emerald-50 hover:text-emerald-700">
                                新着
                            </a>
                            <a href="{{ route('work-posts.index', ['sort' => 'popular']) }}" class="rounded-full bg-slate-100 px-3 py-1.5 transition hover:bg-emerald-50 hover:text-emerald-700">
                                人気
                            </a>
                            <a href="{{ route('work-posts.index', ['sort' => 'recent']) }}" class="rounded-full bg-slate-100 px-3 py-1.5 transition hover:bg-emerald-50 hover:text-emerald-700">
                                直近開催
                            </a>
                        </div>
                    </div>

                    <div class="mt-6 space-y-4">
                        @forelse ($homeWorkPosts as $workPost)
                            @php
                                $profile = $workPost->user->profile ?? null;
                                $displayName = $profile?->display_name ?? $workPost->user->name;
                                $jobType = $profile?->job_type ?? '職種未設定';
                                $avatarPath = $profile?->avatar_path;
                                $avatarUrl = $avatarPath
                                    ? asset('storage/' . $avatarPath)
                                    : asset('images/default-avatar.png');

                                $purposeLabel = $workPost->purpose ?: '未設定';
                                $bodyText = \Illuminate\Support\Str::limit(strip_tags($workPost->body), 100);
                            @endphp

                            <article class="rounded-[24px] border border-slate-200 bg-slate-50/60 p-5 transition hover:border-emerald-300 hover:bg-white">
                                <div class="flex flex-col gap-4 sm:flex-row sm:items-start sm:justify-between">
                                    <div class="min-w-0 flex-1">
                                        <div class="mb-3 flex flex-wrap items-center gap-2">
                                            <span class="rounded-full bg-emerald-100 px-3 py-1 text-xs font-black text-emerald-700">
                                                {{ $purposeLabel }}
                                            </span>

                                            @if ($workPost->location_type === 'online')
                                                <span class="rounded-full bg-sky-100 px-3 py-1 text-xs font-black text-sky-700">
                                                    オンライン
                                                </span>
                                            @elseif ($workPost->location_type === 'offline')
                                                <span class="rounded-full bg-amber-100 px-3 py-1 text-xs font-black text-amber-700">
                                                    オフライン
                                                </span>
                                            @elseif ($workPost->location_type === 'both')
                                                <span class="rounded-full bg-violet-100 px-3 py-1 text-xs font-black text-violet-700">
                                                    どちらでも可
                                                </span>
                                            @endif

                                            @if (!empty($workPost->time_zone))
                                                <span class="rounded-full bg-slate-200 px-3 py-1 text-xs font-black text-slate-700">
                                                    {{ $workPost->time_zone }}
                                                </span>
                                            @endif

                                            <span class="text-xs font-bold text-slate-400">
                                                {{ optional($workPost->created_at)->format('Y/m/d') }}
                                            </span>
                                        </div>

                                        <h3 class="text-2xl font-black leading-tight text-slate-900">
                                            <a href="{{ route('work-posts.show', $workPost) }}" class="transition hover:text-emerald-600">
                                                {{ $workPost->title }}
                                            </a>
                                        </h3>

                                        <p class="mt-3 text-sm font-semibold leading-7 text-slate-600">
                                            {{ $bodyText }}
                                        </p>

                                        <div class="mt-4 flex items-center gap-3">
                                            <img
                                                src="{{ $avatarUrl }}"
                                                alt="{{ $displayName }}のプロフィール画像"
                                                class="h-12 w-12 rounded-full border border-slate-200 bg-white object-cover"
                                            >

                                            <div class="min-w-0">
                                                <p class="truncate text-sm font-black text-slate-900">
                                                    {{ $displayName }}
                                                </p>
                                                <p class="truncate text-xs font-semibold text-slate-500">
                                                    {{ $jobType }}
                                                </p>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="sm:pl-4">
                                        <a
                                            href="{{ route('work-posts.show', $workPost) }}"
                                            class="inline-flex items-center justify-center rounded-xl bg-slate-900 px-4 py-3 text-sm font-black text-white transition hover:bg-emerald-600"
                                        >
                                            詳細を見る
                                        </a>
                                    </div>
                                </div>
                            </article>
                        @empty
                            <div class="rounded-[24px] bg-slate-50 p-8 text-center ring-1 ring-slate-200">
                                <h3 class="text-xl font-black text-slate-900">
                                    まだ募集がありません
                                </h3>
                                <p class="mt-2 text-sm font-semibold text-slate-500">
                                    最初の募集を作成して、仲間探しを始めましょう。
                                </p>

                                <div class="mt-5">
                                    <a
                                        href="{{ auth()->check() ? route('work-posts.create') : route('login') }}"
                                        class="inline-flex items-center justify-center rounded-xl bg-emerald-500 px-5 py-3 text-sm font-black text-white transition hover:bg-emerald-600"
                                    >
                                        募集をする
                                    </a>
                                </div>
                            </div>
                        @endforelse
                    </div>
                </div>

                {{-- Articles --}}
{{-- Articles --}}
@if (Route::has('articles.index') && $homeArticles->isNotEmpty())
    <div class="rounded-[28px] bg-white p-6 shadow-sm ring-1 ring-slate-200">
        <div class="mb-6 flex flex-col gap-3 sm:flex-row sm:items-end sm:justify-between">
            <div>
                <p class="text-sm font-black tracking-widest text-indigo-600">
                    ARTICLES
                </p>

                <h2 class="mt-2 text-2xl font-black text-slate-900">
                    お役立ち記事
                </h2>

                <p class="mt-2 text-sm font-semibold text-slate-500">
                    リモートワークや継続に役立つ記事です。
                </p>
            </div>

            <a
                href="{{ route('articles.index') }}"
                class="inline-flex items-center justify-center rounded-xl border border-slate-200 bg-white px-5 py-3 text-sm font-black text-slate-700 transition hover:bg-slate-50"
            >
                記事一覧を見る
            </a>
        </div>

        <div class="divide-y divide-slate-100 rounded-2xl border border-slate-200 bg-slate-50/60">
            @foreach ($homeArticles as $article)
                @php
                    $articleUrl = $article->short_slug
                        ? route('articles.short-show', $article->short_slug)
                        : route('articles.show', $article);

                    $articleTitle = $article->h1_title
                        ?? $article->seo_title
                        ?? $article->title;
                @endphp

                <article class="group bg-white first:rounded-t-2xl last:rounded-b-2xl">
                    <a
                        href="{{ $articleUrl }}"
                        class="block px-5 py-4 transition hover:bg-indigo-50"
                    >
                        <h3 class="text-base font-black leading-7 text-slate-900 transition group-hover:text-indigo-600 sm:text-lg">
                            {{ $articleTitle }}
                        </h3>
                    </a>
                </article>
            @endforeach
        </div>
    </div>
@endif
            </div>

            {{-- Right Ranking Sidebar --}}
            <aside class="rounded-[28px] bg-white p-5 shadow-sm ring-1 ring-slate-200 xl:sticky xl:top-24 xl:self-start">
                <div class="mb-5">
                    <p class="text-sm font-black tracking-widest text-emerald-600">
                        TRAINING RANKING
                    </p>
                    <h2 class="mt-2 text-3xl font-black leading-tight text-slate-900">
                        活躍している<br>
                        ユーザーランキング
                    </h2>
                    <p class="mt-2 text-sm font-semibold leading-6 text-slate-500">
                        トレーニングの獲得ポイント上位20名を表示しています。
                    </p>
                </div>

                <div class="mb-5 grid grid-cols-2 overflow-hidden rounded-2xl border border-slate-200 bg-slate-100 p-1">
                    <a
                        href="{{ route('home', ['ranking_mode' => 'monthly']) }}"
                        class="rounded-xl px-3 py-2.5 text-center text-sm font-black transition
                            {{ $rankingMode !== 'total' ? 'bg-slate-900 text-white shadow-sm' : 'text-slate-500 hover:bg-white' }}"
                    >
                        月間
                    </a>

                    <a
                        href="{{ route('home', ['ranking_mode' => 'total']) }}"
                        class="rounded-xl px-3 py-2.5 text-center text-sm font-black transition
                            {{ $rankingMode === 'total' ? 'bg-slate-900 text-white shadow-sm' : 'text-slate-500 hover:bg-white' }}"
                    >
                        総合
                    </a>
                </div>

                <div class="max-h-[980px] space-y-4 overflow-y-auto pr-1">
                    @forelse ($homeRankingUsers as $index => $ranking)
                        @php
                            $profile = $ranking->user->profile ?? null;
                            $displayName = $profile?->display_name ?? $ranking->user->name;
                            $jobType = $profile?->job_type ?? '職種未設定';
                            $avatarPath = $profile?->avatar_path;
                            $avatarUrl = $avatarPath
                                ? asset('storage/' . $avatarPath)
                                : asset('images/default-avatar.png');
                            $rank = $index + 1;
                        @endphp

                        <div class="border-b border-dashed border-slate-200 pb-4 last:border-b-0 last:pb-0">
                            <div class="flex items-start gap-3">
                                <img
                                    src="{{ $avatarUrl }}"
                                    alt="{{ $displayName }}のプロフィール画像"
                                    class="h-16 w-16 rounded-2xl border border-slate-200 bg-slate-100 object-cover"
                                >

                                <div class="min-w-0 flex-1">
                                    <div class="mb-1 flex flex-wrap items-center gap-2">
                                        @if ($rank === 1)
                                            <span class="text-sm font-black text-amber-500">👑 1位</span>
                                        @elseif ($rank === 2)
                                            <span class="text-sm font-black text-slate-400">🥈 2位</span>
                                        @elseif ($rank === 3)
                                            <span class="text-sm font-black text-orange-500">🥉 3位</span>
                                        @else
                                            <span class="text-sm font-black text-slate-700">{{ $rank }}位</span>
                                        @endif

                                        <span class="rounded-full bg-emerald-50 px-2 py-0.5 text-xs font-black text-emerald-700">
                                            {{ number_format($ranking->total_points) }} pt
                                        </span>
                                    </div>

                                    <p class="break-words text-lg font-black leading-6 text-slate-900">
                                        {{ $displayName }}
                                    </p>

                                    <p class="mt-1 break-words text-sm font-semibold leading-6 text-slate-500">
                                        {{ $jobType }}
                                    </p>

                                    <p class="mt-1 text-xs font-bold text-slate-400">
                                        {{ $ranking->training_count }}回 実施
                                    </p>
                                </div>
                            </div>
                        </div>
                    @empty
                        <div class="rounded-[24px] bg-slate-50 p-6 text-center">
                            <p class="text-sm font-black text-slate-600">
                                まだランキングデータがありません。
                            </p>

                            @auth
                                @if (Route::has('trainings.index'))
                                    <a
                                        href="{{ route('trainings.index') }}"
                                        class="mt-4 inline-flex items-center justify-center rounded-xl bg-indigo-600 px-4 py-2.5 text-sm font-black text-white transition hover:bg-indigo-700"
                                    >
                                        トレーニングを始める
                                    </a>
                                @endif
                            @else
                                <a
                                    href="{{ route('register') }}"
                                    class="mt-4 inline-flex items-center justify-center rounded-xl bg-indigo-600 px-4 py-2.5 text-sm font-black text-white transition hover:bg-indigo-700"
                                >
                                    会員登録して参加する
                                </a>
                            @endauth
                        </div>
                    @endforelse
                </div>

                @if (Route::has('trainings.ranking'))
                    <div class="mt-5">
                        <a
                            href="{{ route('trainings.ranking') }}"
                            class="block rounded-2xl bg-slate-900 px-4 py-3 text-center text-sm font-black text-white transition hover:bg-emerald-600"
                        >
                            ランキングをもっと見る
                        </a>
                    </div>
                @endif
            </aside>
        </div>
    </section>
</div>
@endsection
