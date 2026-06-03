{{-- SP版：resources/views/home_sp.blade.php --}}
<div class="block md:hidden min-h-screen w-full overflow-x-hidden bg-[#F8FAFF] text-[#071433]">
    <div class="mx-auto min-h-screen w-full max-w-[430px] overflow-x-hidden bg-[#F8FAFF] px-4 pb-24 pt-4">

        {{-- メインビジュアル --}}
        {{-- メインビジュアル --}}
        <section
            class="mb-5 overflow-hidden rounded-[18px] border border-[#DDE6F5] bg-white shadow-[0_8px_22px_rgba(15,43,95,0.06)]">
            <img src="{{ $heroSpImageUrl }}" alt="MokuMoku Match メインビジュアル" class="h-auto w-full object-contain"
                loading="eager">
        </section>

        {{-- 募集作成 --}}
        <section class="mb-5">
            <a href="{{ auth()->check() ? route('work-posts.create') : route('login') }}"
                class="flex h-[62px] items-center justify-center gap-3 rounded-[14px] bg-emerald-500 text-[22px] font-black text-white shadow-[0_12px_22px_rgba(16,185,129,0.25)]">
                <span
                    class="flex h-9 w-9 items-center justify-center rounded-full bg-white text-[24px] font-black text-emerald-500">
                    +
                </span>
                募集を作成する
            </a>
        </section>

        {{-- キーワード検索 --}}
        <section
            class="mb-5 rounded-[18px] border border-[#DDE6F5] bg-white px-4 py-5 shadow-[0_8px_22px_rgba(15,43,95,0.06)]">
            <h2 class="mb-4 text-[21px] font-black text-[#071433]">
                キーワードで探す
            </h2>

            <form method="GET" action="{{ route('home') }}" class="flex gap-3">
                <input type="text" name="keyword" value="{{ request('keyword') }}" placeholder="キーワードを入力"
                    class="h-[56px] min-w-0 flex-1 rounded-[12px] border border-[#CBD7EA] bg-white px-4 text-[17px] font-bold text-[#071433] outline-none placeholder:text-[#94A3B8] focus:border-[#0D4FE8] focus:ring-4 focus:ring-blue-100">

                <button type="submit"
                    class="h-[56px] w-[108px] shrink-0 rounded-[12px] bg-[#0D4FE8] text-[17px] font-black text-white shadow-[0_10px_18px_rgba(13,79,232,0.24)]">
                    検索する
                </button>
            </form>
        </section>

        {{-- クイックフィルター --}}
        <section
            class="mb-5 rounded-[18px] border border-[#DDE6F5] bg-white px-4 py-5 shadow-[0_8px_22px_rgba(15,43,95,0.06)]">
            <div class="grid grid-cols-5 gap-x-3 gap-y-4">
                @foreach ($quickFilterLinks as $filter)
                @php
                $isActive = count($filter['params']) === 0
                ? ! request()->hasAny(['purpose', 'location_type', 'time_zone'])
                : collect($filter['params'])->every(fn ($value, $key) => request($key) == $value);
                @endphp

                <a href="{{ route('home', $filter['params']) }}" class="flex flex-col items-center gap-2">
                    <span
                        class="{{ $isActive ? 'bg-blue-100 text-[#0D4FE8]' : 'bg-[#F1F5F9] text-[#334155]' }} flex h-[56px] w-[56px] items-center justify-center rounded-full text-[25px]">
                        {{ $filter['icon'] }}
                    </span>

                    <span class="text-center text-[13px] font-black leading-tight text-[#071433]">
                        {{ $filter['label'] }}
                    </span>
                </a>
                @endforeach
            </div>
        </section>

        {{-- 新着の募集 --}}
        <section class="mb-5">
            <div class="mb-3 flex items-center justify-between">
                <h2 class="text-[23px] font-black text-[#071433]">
                    新着の募集
                </h2>

                <a href="{{ route('work-posts.index') }}"
                    class="flex items-center gap-1 text-[15px] font-black text-[#0D4FE8]">
                    すべて見る
                    <span class="text-[22px]">›</span>
                </a>
            </div>

            <div class="space-y-3">
                @forelse ($homeWorkPosts as $workPost)
                @php
                $user = $workPost->user;
                $purposeLabel = $workPost->purpose ?: '未設定';
                $locationLabel = $formatLocationType($workPost->location_type);
                $participantsText = ($workPost->applications_count ?? 0)
                . ' / '
                . ($workPost->max_participants ?? '-')
                . '人';
                @endphp

                <a href="{{ route('work-posts.show', $workPost) }}"
                    class="block rounded-[16px] border border-[#DDE6F5] bg-white px-4 py-4 shadow-[0_8px_22px_rgba(15,43,95,0.06)]">
                    <div class="mb-3 flex items-center gap-2">
                        <span class="rounded-full bg-blue-50 px-3 py-1 text-[13px] font-black text-[#0D4FE8]">
                            {{ $purposeLabel }}
                        </span>

                        <span class="rounded-full bg-[#F1F5F9] px-3 py-1 text-[13px] font-black text-[#334155]">
                            {{ $locationLabel }}
                        </span>
                    </div>

                    <div class="flex items-start justify-between gap-3">
                        <div class="min-w-0 flex-1">
                            <h3 class="line-clamp-2 text-[17px] font-black leading-relaxed text-[#071433]">
                                {{ $workPost->title }}
                            </h3>

                            <div
                                class="mt-3 flex flex-wrap items-center gap-x-4 gap-y-2 text-[14px] font-bold text-[#64748B]">
                                <span>🗓️ {{ optional($workPost->start_at)->format('n/j (D)') ??
                                    optional($workPost->created_at)->format('n/j') }}</span>
                                <span>🕘 {{ optional($workPost->start_at)->format('H:i') ?? '08:00' }}〜{{
                                    optional($workPost->end_at)->format('H:i') ?? '10:00' }}</span>
                                <span>👥 {{ $participantsText }}</span>
                            </div>
                        </div>

                        <div class="shrink-0 text-right">
                            <img src="{{ $avatarUrl($user) }}" alt="{{ $displayName($user) }}のプロフィール画像"
                                class="h-12 w-12 rounded-full border border-[#DDE6F5] bg-blue-50 object-cover">

                            <span
                                class="mt-2 inline-flex rounded-[8px] bg-amber-50 px-2 py-1 text-[13px] font-black text-amber-600">
                                募集中
                            </span>
                        </div>
                    </div>
                </a>
                @empty
                <div class="rounded-[16px] border border-dashed border-[#CBD7EA] bg-white px-5 py-8 text-center">
                    <p class="text-[15px] font-bold text-[#64748B]">
                        まだ募集がありません。
                    </p>
                </div>
                @endforelse
            </div>

            @if ($homeWorkPosts->hasPages())
                <div class="mt-5">
                    {{ $homeWorkPosts->links() }}
                </div>
            @endif
        </section>
        </section>

        {{-- トレーニングランキング --}}
        <section
            class="mb-5 rounded-[18px] border border-[#DDE6F5] bg-white px-4 py-5 shadow-[0_8px_22px_rgba(15,43,95,0.06)]">
            <h2 class="mb-4 text-[22px] font-black text-[#071433]">
                トレーニングランキング
            </h2>

            <div class="mb-5 grid grid-cols-2 overflow-hidden rounded-[10px] border border-[#CBD7EA] bg-white">
                <a href="{{ route('home', ['ranking_mode' => 'monthly']) }}"
                    class="{{ $rankingMode !== 'total' ? 'bg-white text-[#0D4FE8] ring-2 ring-[#0D4FE8]' : 'bg-[#F8FAFF] text-[#334155]' }} flex h-[46px] items-center justify-center text-[16px] font-black">
                    今月
                </a>

                <a href="{{ route('home', ['ranking_mode' => 'total']) }}"
                    class="{{ $rankingMode === 'total' ? 'bg-white text-[#0D4FE8] ring-2 ring-[#0D4FE8]' : 'bg-[#F8FAFF] text-[#334155]' }} flex h-[46px] items-center justify-center text-[16px] font-black">
                    累計
                </a>
            </div>

            @if ($topRankingUser)
            @php
            $rankUser = $topRankingUser->user;
            @endphp

            <div class="grid grid-cols-[54px_54px_1fr_92px] items-center gap-3">
                <div class="text-[40px]">🥇</div>

                <img src="{{ $avatarUrl($rankUser) }}" alt="{{ $displayName($rankUser) }}のプロフィール画像"
                    class="h-[54px] w-[54px] rounded-full border border-[#DDE6F5] bg-blue-50 object-cover">

                <p class="truncate text-[19px] font-black text-[#071433]">
                    {{ $displayName($rankUser) }}
                </p>

                <p class="text-right text-[21px] font-black text-[#071433]">
                    {{ number_format($topRankingUser->total_points) }}pt
                </p>
            </div>

            <a href="{{ route('trainings.ranking') }}"
                class="mt-5 flex items-center justify-center gap-2 text-[16px] font-black text-[#0D4FE8]">
                ランキングを見る
                <span class="text-[22px]">›</span>
            </a>
            @else
            <p class="text-center text-[15px] font-bold text-[#64748B]">
                まだランキングデータがありません。
            </p>
            @endif
        </section>
    </div>
</div>
