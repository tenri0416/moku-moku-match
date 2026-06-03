{{-- SP版：resources/views/trainings/index_sp.blade.php --}}
<div class="block md:hidden min-h-screen w-full overflow-x-hidden bg-[#F8FAFF] text-[#071433]">
  <div class="mx-auto min-h-screen w-full max-w-[430px] overflow-x-hidden bg-[#F8FAFF] px-4 pb-28 pt-4">

      @if (session('success'))
          <div class="mb-4 rounded-2xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-[14px] font-bold text-emerald-700">
              {{ session('success') }}
          </div>
      @endif

      @if (session('error'))
          <div class="mb-4 rounded-2xl border border-red-200 bg-red-50 px-4 py-3 text-[14px] font-bold text-red-700">
              {{ session('error') }}
          </div>
      @endif

      {{-- ヒーロー --}}
      <section class="mb-6 overflow-hidden rounded-[18px] bg-gradient-to-br from-[#1D66F3] to-[#0648D8] text-white shadow-[0_14px_28px_rgba(13,79,232,0.28)]">
          <div class="relative min-h-[178px] px-5 pb-5 pt-5">
              <div class="relative z-10 mb-3 inline-flex max-w-[210px] items-center gap-2 rounded-full border border-white/30 bg-white/10 px-3 py-1.5 text-[15px] font-bold leading-none">
                  <span>🏆</span>
                  <span class="truncate">自己成長トレーニング</span>
              </div>

              <div class="relative z-10 max-w-[220px]">
                  <h1 class="text-[24px] font-black leading-[1.45] tracking-[0.01em]">
                      今日も少しだけ、<br>
                      前に進もう ✨
                  </h1>
              </div>

              <img
                  src="{{ asset('images/training-top.png') }}"
                  alt="自己成長トレーニング"
                  class="absolute bottom-3 right-0 h-[112px] w-[176px] object-contain"
                  loading="eager"
              >
          </div>

          <div class="grid grid-cols-3 border-t border-white/20 bg-white/8 px-2 py-4">
              <div class="flex min-w-0 items-center justify-center gap-1 border-r border-white/25 px-1">
                  <div class="shrink-0 text-[30px]">🏅</div>
                  <div class="min-w-0">
                      <p class="text-[12px] font-bold leading-none text-blue-50">
                          総ポイント
                      </p>
                      <p class="mt-2 text-[24px] font-black leading-none">
                          {{ $myTotalPoints }}<span class="text-[13px]">pt</span>
                      </p>
                  </div>
              </div>

              <div class="flex min-w-0 items-center justify-center gap-1 border-r border-white/25 px-1">
                  <div class="shrink-0 text-[30px]">📋</div>
                  <div class="min-w-0">
                      <p class="text-[12px] font-bold leading-none text-blue-50">
                          本日完了
                      </p>
                      <p class="mt-2 text-[24px] font-black leading-none">
                          {{ $completedTodayCount }}<span class="text-[13px]">/6</span>
                      </p>
                  </div>
              </div>

              <div class="flex min-w-0 items-center justify-center gap-1 px-1">
                  <div class="shrink-0 text-[30px]">📄</div>
                  <div class="min-w-0">
                      <p class="text-[12px] font-bold leading-none text-blue-50">
                          履歴
                      </p>
                      <p class="mt-2 text-[24px] font-black leading-none">
                          {{ $historyCount }}<span class="text-[13px]">件</span>
                      </p>
                  </div>
              </div>
          </div>
      </section>

      {{-- 今日できるトレーニング --}}
      <section class="mb-7">
          <div class="mb-4 flex items-center justify-between gap-3">
              <h2 class="flex min-w-0 items-center gap-2 text-[23px] font-black leading-tight text-[#071433]">
                  <span class="shrink-0 text-[28px] text-amber-500">☀</span>
                  <span>
                      今日できる<br>
                      トレーニング
                  </span>
              </h2>

              <a href="{{ route('trainings.ranking') }}"
                  class="flex h-[46px] shrink-0 items-center justify-center gap-2 rounded-full border border-[#DDE6F5] bg-white px-4 text-[17px] font-black text-[#0D4FE8] shadow-[0_8px_18px_rgba(15,43,95,0.06)]">
                  📊
                  ランキング
                  <span class="text-[22px]">›</span>
              </a>
          </div>

          <div class="space-y-3">
              @foreach ($trainingCards as $index => $card)
                  @php
                      $isDone = $todayStatuses[$card['key']] ?? false;
                  @endphp

                  <a href="{{ $isDone ? 'javascript:void(0)' : $card['route'] }}"
                      @if (!$isDone && $card['loading'])
                          data-ai-loading-link="true"
                          data-ai-loading-type="question"
                      @endif
                      class="block overflow-hidden rounded-[18px] border border-[#DDE6F5] bg-white shadow-[0_8px_22px_rgba(15,43,95,0.06)] {{ $isDone ? 'opacity-80' : '' }}">

                      <div class="flex items-center gap-4 px-4 py-4">
                          <div class="flex h-[64px] w-[64px] shrink-0 items-center justify-center rounded-full {{ $card['bg'] }} text-[34px]">
                              {{ $card['emoji'] }}
                          </div>

                          <div class="min-w-0 flex-1">
                              <h3 class="text-[21px] font-black leading-tight text-[#071433]">
                                  {{ $index + 1 }}. {{ $card['short_label'] }}
                              </h3>

                              <p class="mt-2 text-[16px] font-bold leading-relaxed text-[#46516B]">
                                  {{ $card['description'] }}
                              </p>
                          </div>

                          <span class="shrink-0 text-[30px] text-[#8793A8]">
                              ›
                          </span>
                      </div>

                      <div class="flex items-center justify-between border-t border-[#E8EDF6] bg-[#FBFCFF] px-4 py-3">
                          <span class="rounded-full border border-orange-200 bg-orange-50 px-3 py-1.5 text-[16px] font-bold text-orange-600">
                              {{ $card['points'] }}
                          </span>

                          @if ($isDone)
                              <span class="flex h-[42px] min-w-[104px] items-center justify-center rounded-[12px] border border-emerald-200 bg-emerald-50 px-4 text-[18px] font-black text-emerald-700">
                                  ✓ 完了
                              </span>
                          @else
                              <span class="flex h-[42px] min-w-[104px] items-center justify-center rounded-[12px] border border-[#DDE6F5] bg-white px-4 text-[18px] font-black text-[#071433]">
                                  未実施
                              </span>
                          @endif
                      </div>
                  </a>
              @endforeach
          </div>
      </section>

      {{-- 最近の履歴 --}}
      <section class="mb-6">
          <div class="mb-4 flex items-center justify-between">
              <h2 class="flex items-center gap-2 text-[24px] font-black text-[#071433]">
                  <span class="text-[28px]">🕘</span>
                  最近の履歴
              </h2>

              <a href="#trainingHistory" class="flex items-center gap-1 text-[18px] font-black text-[#0D4FE8]">
                  すべて見る
                  <span class="text-[24px]">›</span>
              </a>
          </div>

          <div id="trainingHistory" class="overflow-hidden rounded-[18px] border border-[#DDE6F5] bg-white shadow-[0_8px_22px_rgba(15,43,95,0.06)]">
              @forelse ($trainings->take(3) as $training)
                  <a href="{{ route('trainings.show', ['type' => $training['type'], 'id' => $training['id']]) }}"
                      class="block border-b border-[#E8EDF6] px-4 py-4 last:border-b-0">

                      <div class="flex items-center gap-3">
                          <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-lg bg-blue-50 text-[24px]">
                              🗓️
                          </div>

                          <div class="min-w-0 flex-1">
                              <div class="flex items-center gap-3">
                                  <span class="text-[16px] font-bold text-[#46516B]">
                                      {{ $training['training_date']->format('Y-m-d') }}
                                  </span>

                                  <span class="truncate text-[16px] font-black text-[#071433]">
                                      {{ $training['type_label'] }}
                                  </span>
                              </div>

                              <div class="mt-2 flex items-center gap-3">
                                  <span class="text-[18px] font-black text-[#0D4FE8]">
                                      {{ $training['total_score'] ?? '-' }}<span class="text-[12px]">点</span>
                                  </span>

                                  <span class="rounded-full border border-orange-200 bg-orange-50 px-2 py-1 text-[15px] font-bold text-orange-600">
                                      +{{ $training['earned_points'] }}pt
                                  </span>
                              </div>
                          </div>

                          <span class="shrink-0 text-[28px] text-[#8793A8]">
                              ›
                          </span>
                      </div>
                  </a>
              @empty
                  <div class="px-5 py-8 text-center">
                      <p class="text-[15px] font-bold text-[#64748B]">
                          まだトレーニング履歴がありません。
                      </p>
                  </div>
              @endforelse
          </div>
      </section>
  </div>
</div>
