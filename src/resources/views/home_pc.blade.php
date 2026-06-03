{{-- PC版：resources/views/home_pc.blade.php --}}
<div class="hidden md:block min-h-screen bg-[#F8FAFF] text-[#071433]">
  <div class="mx-auto w-full max-w-[1440px] px-8 py-8">

    <div class="grid grid-cols-[250px_1fr_310px] gap-6">

      {{-- 左サイド --}}
      <aside class="space-y-5">
        {{-- 募集作成 --}}
        <section class="rounded-[16px] bg-emerald-500 px-5 py-6 text-white shadow-[0_12px_24px_rgba(16,185,129,0.22)]">
          <div
            class="mx-auto mb-4 flex h-12 w-12 items-center justify-center rounded-full bg-white text-[30px] font-black text-emerald-500">
            +
          </div>

          <h2 class="text-center text-[22px] font-black">
            募集を作成する
          </h2>

          <p class="mt-3 text-center text-[14px] font-bold leading-relaxed text-emerald-50">
            作業・勉強・情報交換の<br>
            募集を始めよう！
          </p>

          <a href="{{ auth()->check() ? route('work-posts.create') : route('login') }}"
            class="mt-5 flex h-[44px] items-center justify-center rounded-[10px] bg-white text-[15px] font-black text-emerald-600">
            募集を作成する
          </a>
        </section>

        {{-- キーワード検索 --}}
        <section
          class="rounded-[16px] border border-[#DDE6F5] bg-white px-4 py-5 shadow-[0_8px_22px_rgba(15,43,95,0.06)]">
          <h2 class="mb-4 text-[18px] font-black text-[#071433]">
            キーワードで探す
          </h2>

          <form method="GET" action="{{ route('home') }}" class="space-y-3">
            <input type="text" name="keyword" value="{{ request('keyword') }}" placeholder="キーワードを入力"
              class="h-[44px] w-full rounded-[8px] border border-[#CBD7EA] bg-white px-4 text-[14px] font-bold text-[#071433] outline-none placeholder:text-[#94A3B8] focus:border-[#0D4FE8] focus:ring-4 focus:ring-blue-100">

            <button type="submit"
              class="h-[44px] w-full rounded-[8px] bg-[#0D4FE8] text-[15px] font-black text-white shadow-[0_8px_16px_rgba(13,79,232,0.22)]">
              検索する
            </button>

            @if (request()->hasAny(['keyword', 'purpose', 'location_type', 'time_zone', 'prefecture_id']))
            <a href="{{ route('home') }}"
              class="flex h-[44px] w-full items-center justify-center rounded-[8px] border border-[#CBD7EA] bg-white text-[14px] font-black text-[#071433]">
              条件をクリア
            </a>
            @else
            <a href="{{ route('home') }}"
              class="flex h-[44px] w-full items-center justify-center rounded-[8px] border border-[#CBD7EA] bg-white text-[14px] font-black text-[#071433]">
              条件をクリア
            </a>
            @endif
          </form>
        </section>

        {{-- クイックフィルター --}}
        <section
          class="rounded-[16px] border border-[#DDE6F5] bg-white px-4 py-5 shadow-[0_8px_22px_rgba(15,43,95,0.06)]">
          <h2 class="mb-4 text-[18px] font-black text-[#071433]">
            クイックフィルター
          </h2>

          <div class="space-y-2">
            @foreach ($quickFilterLinks as $filter)
            @php
            $isActive = count($filter['params']) === 0
            ? ! request()->hasAny(['purpose', 'location_type', 'time_zone'])
            : collect($filter['params'])->every(fn ($value, $key) => request($key) == $value);
            @endphp

            <a href="{{ route('home', $filter['params']) }}"
              class="{{ $isActive ? 'bg-blue-50 text-[#0D4FE8]' : 'bg-white text-[#071433]' }} flex items-center gap-3 rounded-[10px] px-3 py-2.5 text-[14px] font-black transition hover:bg-blue-50 hover:text-[#0D4FE8]">
              <span class="flex h-8 w-8 items-center justify-center rounded-full bg-[#F1F5F9] text-[17px]">
                {{ $filter['icon'] }}
              </span>
              {{ $filter['label'] }}
            </a>
            @endforeach
          </div>
        </section>
      </aside>

      {{-- 中央 --}}
      <main class="min-w-0 space-y-5">
        {{-- ヒーロー --}}
        {{-- ヒーロー --}}
        <section
          class="overflow-hidden rounded-[16px] border border-[#DDE6F5] bg-white shadow-[0_8px_22px_rgba(15,43,95,0.06)]">
          <img src="{{ $heroImageUrl }}" alt="MokuMoku Match メインビジュアル" class="h-auto w-full object-contain"
            loading="eager">
        </section>

        {{-- 募集一覧 --}}
        <section>
          <div class="mb-3 flex items-center justify-between">
            <h2 class="text-[22px] font-black text-[#071433]">
              募集一覧
            </h2>

            <p class="text-[13px] font-bold text-[#64748B]">
              {{ number_format($allWorkPostCount) }}件
            </p>
          </div>

          {{-- 検索条件 --}}
          <form method="GET" action="{{ route('home') }}" class="mb-4 grid grid-cols-[1fr_1fr_1fr_1fr_110px] gap-3">
            <div>
              <label class="mb-1 block text-[12px] font-bold text-[#64748B]">場所</label>
              <select name="prefecture_id" class="h-[42px] w-full rounded-[8px] border border-[#CBD7EA] bg-white px-3 text-[13px] font-bold text-[#071433]">
                  <option value="">すべて</option>
                  @foreach ($prefectures as $prefecture)
                      <option value="{{ $prefecture->id }}" @selected((string) request('prefecture_id') === (string) $prefecture->id)>
                          {{ $prefecture->name }}
                      </option>
                  @endforeach
              </select>
          </div>

            <div>
              <label class="mb-1 block text-[12px] font-bold text-[#64748B]">開催形式</label>
              <select name="location_type"
                class="h-[42px] w-full rounded-[8px] border border-[#CBD7EA] bg-white px-3 text-[13px] font-bold text-[#071433]">
                <option value="">すべて</option>
                <option value="online" @selected(request('location_type')==='online' )>オンライン</option>
                <option value="offline" @selected(request('location_type')==='offline' )>オフライン</option>
                <option value="both" @selected(request('location_type')==='both' )>どちらでも可</option>
              </select>
            </div>

            <div>
              <label class="mb-1 block text-[12px] font-bold text-[#64748B]">時間帯</label>
              <select name="time_zone"
                class="h-[42px] w-full rounded-[8px] border border-[#CBD7EA] bg-white px-3 text-[13px] font-bold text-[#071433]">
                <option value="">すべて</option>
                <option value="morning" @selected(request('time_zone')==='morning' )>朝</option>
                <option value="daytime" @selected(request('time_zone')==='daytime' )>昼</option>
                <option value="night" @selected(request('time_zone')==='night' )>夜</option>
              </select>
            </div>

            <div>
            </div>

            <div class="flex items-end">
              <button type="submit"
                class="h-[42px] w-full rounded-[8px] bg-[#0D4FE8] text-[14px] font-black text-white shadow-[0_8px_16px_rgba(13,79,232,0.22)]">
                検索する
              </button>
            </div>
          </form>

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

            <article
              class="rounded-[14px] border border-[#DDE6F5] bg-white px-4 py-4 shadow-[0_6px_18px_rgba(15,43,95,0.05)]">
              <div class="flex items-center justify-between gap-4">
                <div class="min-w-0 flex-1">
                  <div class="mb-2 flex items-center gap-2">
                    <span class="rounded-full bg-blue-50 px-3 py-1 text-[12px] font-black text-[#0D4FE8]">
                      {{ $purposeLabel }}
                    </span>

                    <span class="rounded-full bg-[#F1F5F9] px-3 py-1 text-[12px] font-black text-[#334155]">
                      {{ $locationLabel }}
                    </span>
                  </div>

                  <h3 class="truncate text-[17px] font-black text-[#071433]">
                    <a href="{{ route('work-posts.show', $workPost) }}">
                      {{ $workPost->title }}
                    </a>
                  </h3>

                  <p class="mt-2 line-clamp-1 text-[13px] font-bold text-[#46516B]">
                    {{ Str::limit(strip_tags($workPost->body), 70) }}
                  </p>

                  <div class="mt-3 flex flex-wrap items-center gap-x-5 gap-y-1 text-[13px] font-bold text-[#64748B]">
                    <span>🗓️ {{ optional($workPost->start_at)->format('Y/m/d') ??
                      optional($workPost->created_at)->format('Y/m/d') }}</span>
                    <span>{{ optional($workPost->start_at)->format('H:i') ?? '08:00' }}〜{{
                      optional($workPost->end_at)->format('H:i') ?? '10:00' }}</span>
                    <span>👥 {{ $participantsText }}</span>
                  </div>
                </div>

                <div class="w-[92px] shrink-0 text-center">
                  <img src="{{ $avatarUrl($user) }}" alt="{{ $displayName($user) }}のプロフィール画像"
                    class="mx-auto h-12 w-12 rounded-full border border-[#DDE6F5] bg-blue-50 object-cover">

                  <p class="mt-1 truncate text-[12px] font-black text-[#071433]">
                    {{ $displayName($user) }}
                  </p>

                  <p class="mt-1 text-[12px] font-bold text-[#64748B]">
                    {{ $user?->profile?->prefecture?->name ?? '' }}
                  </p>

                  <span
                    class="mt-2 inline-flex rounded-[8px] bg-amber-50 px-2 py-1 text-[12px] font-black text-amber-600">
                    募集中
                  </span>
                </div>
              </div>
            </article>
            @empty
            <div class="rounded-[14px] border border-dashed border-[#CBD7EA] bg-white px-5 py-8 text-center">
              <p class="text-[14px] font-bold text-[#64748B]">
                条件に一致する募集がありません。
              </p>
            </div>
            @endforelse
          </div>

          @if ($homeWorkPosts->hasPages())
          <div class="mt-5">
              {{ $homeWorkPosts->links() }}
          </div>
      @endif

        {{-- 記事 --}}
        @if (Route::has('articles.index') && $homeArticles->isNotEmpty())
        <section
          class="rounded-[16px] border border-[#DDE6F5] bg-white px-4 py-4 shadow-[0_8px_22px_rgba(15,43,95,0.06)]">
          <h2 class="mb-3 text-[18px] font-black text-[#071433]">
            お役立ち記事
          </h2>

          <div class="grid grid-cols-3 gap-3">
            @foreach ($homeArticles as $article)
            @php
            $articleUrl = $article->short_slug
            ? route('articles.short-show', $article->short_slug)
            : route('articles.show', $article);

            $articleTitle = $article->h1_title
            ?? $article->seo_title
            ?? $article->title;
            @endphp

            <a href="{{ $articleUrl }}"
              class="rounded-[12px] border border-[#DDE6F5] bg-white p-3 transition hover:bg-blue-50">
              <div class="mb-2 h-[70px] rounded-[8px] bg-blue-50"></div>

              <h3 class="line-clamp-2 text-[14px] font-black leading-relaxed text-[#071433]">
                {{ $articleTitle }}
              </h3>

              <p class="mt-1 text-[12px] font-bold text-[#64748B]">
                {{ optional($article->published_at ?? $article->created_at)->format('Y/m/d') }}
              </p>
            </a>
            @endforeach
          </div>

          <div class="mt-3 text-center">
            <a href="{{ route('articles.index') }}"
              class="inline-flex items-center gap-2 text-[14px] font-black text-[#0D4FE8]">
              記事一覧を見る
              <span class="text-[22px]">›</span>
            </a>
          </div>
        </section>
        @endif
      </main>

      {{-- 右サイド --}}
      <aside class="space-y-5">
        {{-- ランキング --}}
        <section
          class="rounded-[16px] border border-[#DDE6F5] bg-white px-4 py-5 shadow-[0_8px_22px_rgba(15,43,95,0.06)]">
          <h2 class="mb-4 text-[18px] font-black text-[#071433]">
            トレーニングランキング
          </h2>

          <div class="mb-5 grid grid-cols-2 overflow-hidden rounded-[8px] border border-[#CBD7EA] bg-[#F8FAFF] p-1">
            <a href="{{ route('home', ['ranking_mode' => 'monthly']) }}"
              class="{{ $rankingMode !== 'total' ? 'bg-white text-[#0D4FE8] shadow-sm ring-1 ring-[#0D4FE8]' : 'text-[#64748B]' }} flex h-[34px] items-center justify-center rounded-[7px] text-[13px] font-black">
              今月
            </a>

            <a href="{{ route('home', ['ranking_mode' => 'total']) }}"
              class="{{ $rankingMode === 'total' ? 'bg-white text-[#0D4FE8] shadow-sm ring-1 ring-[#0D4FE8]' : 'text-[#64748B]' }} flex h-[34px] items-center justify-center rounded-[7px] text-[13px] font-black">
              累計
            </a>
          </div>

          <div class="space-y-4">
            @forelse ($homeRankingUsers->take(5) as $index => $ranking)
            @php
            $rankUser = $ranking->user;
            $rank = $index + 1;
            @endphp

            <a href="{{ route('users.show', $rankUser) }}"
              class="grid grid-cols-[34px_42px_1fr_76px] items-center gap-3">
              <div class="text-center">
                @if ($rank === 1)
                <span class="text-[26px]">🥇</span>
                @elseif ($rank === 2)
                <span class="text-[26px]">🥈</span>
                @elseif ($rank === 3)
                <span class="text-[26px]">🥉</span>
                @else
                <span class="text-[15px] font-black text-[#071433]">{{ $rank }}</span>
                @endif
              </div>

              <img src="{{ $avatarUrl($rankUser) }}" alt="{{ $displayName($rankUser) }}のプロフィール画像"
                class="h-[42px] w-[42px] rounded-full border border-[#DDE6F5] bg-blue-50 object-cover">

              <p class="truncate text-[14px] font-black text-[#071433]">
                {{ $displayName($rankUser) }}
              </p>

              <p class="text-right text-[15px] font-black text-[#071433]">
                {{ number_format($ranking->total_points) }}pt
              </p>
            </a>
            @empty
            <p class="text-center text-[13px] font-bold text-[#64748B]">
              まだランキングデータがありません。
            </p>
            @endforelse
          </div>

          <div class="mt-5 text-right">
            <a href="{{ route('trainings.ranking') }}"
              class="inline-flex items-center gap-2 text-[13px] font-black text-[#0D4FE8]">
              ランキングを見る
              <span class="text-[20px]">›</span>
            </a>
          </div>
        </section>

        {{-- 人気キーワード --}}
        <section
          class="rounded-[16px] border border-[#DDE6F5] bg-white px-4 py-5 shadow-[0_8px_22px_rgba(15,43,95,0.06)]">
          <h2 class="mb-4 text-[18px] font-black text-[#071433]">
            人気のキーワード
          </h2>

          <div class="flex flex-wrap gap-2">
            @foreach ($quickFilterLinks as $filter)
            <a href="{{ route('home', $filter['params']) }}"
              class="inline-flex items-center gap-1 rounded-full border border-[#DDE6F5] bg-white px-3 py-2 text-[12px] font-black text-[#334155] hover:bg-blue-50 hover:text-[#0D4FE8]">
              <span>{{ $filter['icon'] }}</span>
              {{ $filter['label'] }}
            </a>
            @endforeach
          </div>
        </section>
      </aside>
    </div>
  </div>
</div>
