@php
    $rankingMode = request('ranking_mode', 'monthly');

    $rankingUsers = $rankingMode === 'total'
        ? ($homeTotalTrainingRankings ?? collect())
        : ($homeMonthlyTrainingRankings ?? collect());
@endphp

<aside class="rounded-3xl border border-slate-200 bg-white p-5 shadow-sm lg:sticky lg:top-24">
    <div class="mb-5">
        <p class="text-xs font-black tracking-widest text-indigo-600">
            TRAINING RANKING
        </p>

        <h2 class="mt-2 text-xl font-black leading-snug text-slate-900">
            活躍している<br>
            ユーザーランキング
        </h2>

        <p class="mt-2 text-xs leading-5 text-slate-500">
            自己成長トレーニングの獲得ポイント順です。
        </p>
    </div>

    <div class="mb-5 grid grid-cols-2 overflow-hidden rounded-2xl border border-slate-200 bg-slate-100 p-1">
        <a
            href="{{ route('home', ['ranking_mode' => 'monthly']) }}"
            class="rounded-xl px-3 py-2 text-center text-sm font-black transition
                {{ $rankingMode !== 'total' ? 'bg-slate-900 text-white shadow-sm' : 'text-slate-500 hover:bg-white' }}"
        >
            月間
        </a>

        <a
            href="{{ route('home', ['ranking_mode' => 'total']) }}"
            class="rounded-xl px-3 py-2 text-center text-sm font-black transition
                {{ $rankingMode === 'total' ? 'bg-slate-900 text-white shadow-sm' : 'text-slate-500 hover:bg-white' }}"
        >
            総合
        </a>
    </div>

    <div class="space-y-4">
        @forelse ($rankingUsers as $index => $ranking)
            @php
                $profile = $ranking->user->profile ?? null;
                $displayName = $profile?->display_name ?? $ranking->user->name;
                $jobType = $profile?->job_type ?? '職種未設定';
                $avatarPath = $profile?->avatar_path;
                $avatarUrl = $avatarPath
                    ? asset('storage/' . $avatarPath)
                    : asset('images/default-avatar.png');

                $rank = $index + 1;

                $rankIcon = match ($rank) {
                    1 => '👑',
                    2 => '🥈',
                    3 => '🥉',
                    default => $rank . '位',
                };
            @endphp

            <div class="border-b border-dashed border-slate-200 pb-4 last:border-b-0 last:pb-0">
                <div class="flex items-center gap-3">
                    <div class="relative flex-shrink-0">
                        <img
                            src="{{ $avatarUrl }}"
                            alt="{{ $displayName }}のプロフィール画像"
                            class="h-14 w-14 rounded-2xl border border-slate-200 bg-slate-100 object-cover"
                        >
                    </div>

                    <div class="min-w-0 flex-1">
                        <div class="mb-1 flex items-center gap-2">
                            <span class="text-sm font-black text-amber-600">
                                {{ $rankIcon }}
                            </span>

                            <span class="rounded-full bg-indigo-50 px-2 py-0.5 text-xs font-black text-indigo-700">
                                {{ number_format($ranking->total_points) }} pt
                            </span>
                        </div>

                        <p class="truncate text-sm font-black leading-5 text-slate-900">
                            {{ $displayName }}
                        </p>

                        <p class="truncate text-xs font-semibold leading-5 text-slate-500">
                            {{ $jobType }}
                        </p>

                        <p class="mt-1 text-xs font-bold text-slate-400">
                            {{ $ranking->training_count }}回 実施
                        </p>
                    </div>
                </div>
            </div>
        @empty
            <div class="rounded-2xl bg-slate-50 p-5 text-center">
                <p class="text-sm font-bold text-slate-600">
                    まだランキングデータがありません。
                </p>

                @auth
                    @if (Route::has('trainings.index'))
                        <a
                            href="{{ route('trainings.index') }}"
                            class="mt-4 inline-flex items-center justify-center rounded-xl bg-indigo-600 px-4 py-2 text-sm font-bold text-white transition hover:bg-indigo-700"
                        >
                            トレーニングを始める
                        </a>
                    @endif
                @else
                    <a
                        href="{{ route('register') }}"
                        class="mt-4 inline-flex items-center justify-center rounded-xl bg-indigo-600 px-4 py-2 text-sm font-bold text-white transition hover:bg-indigo-700"
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
                class="block rounded-2xl border border-slate-200 bg-slate-50 px-4 py-3 text-center text-sm font-black text-slate-700 transition hover:bg-slate-100"
            >
                ランキング一覧を見る →
            </a>
        </div>
    @endif
</aside>
