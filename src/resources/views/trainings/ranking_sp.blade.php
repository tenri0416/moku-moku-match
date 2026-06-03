{{-- SP版：resources/views/trainings/ranking_sp.blade.php --}}
<div class="block md:hidden min-h-screen w-full overflow-x-hidden bg-[#F8FAFF] text-[#071433]">
  <div class="mx-auto min-h-screen w-full max-w-[430px] overflow-x-hidden bg-[#F8FAFF] px-3 pb-28 pt-4">

      {{-- ヒーロー --}}
      <section class="mb-5 overflow-hidden rounded-[18px] bg-gradient-to-br from-[#1D66F3] to-[#0648D8] text-white shadow-[0_14px_28px_rgba(13,79,232,0.28)]">
          <div class="relative min-h-[156px] px-5 pb-5 pt-5">
              <div class="relative z-10 flex items-center gap-3">
                  <span class="text-[42px] leading-none">🏆</span>

                  <h1 class="text-[18px] font-black leading-tight tracking-[0.01em]">
                      トレーニングランキング
                  </h1>
              </div>

              <p class="relative z-10 mt-4 text-[16px] font-bold leading-relaxed text-blue-50">
                  毎日の積み重ねが見える ✨
              </p>

              <div class="pointer-events-none absolute right-4 top-5 text-[86px] opacity-95">
                  🏆
              </div>

              <div class="pointer-events-none absolute right-24 top-12 text-[18px] text-yellow-300">✦</div>
              <div class="pointer-events-none absolute right-10 bottom-10 text-[16px] text-yellow-300">✦</div>
              <div class="pointer-events-none absolute right-28 bottom-8 text-[13px] text-white/70">◆</div>
          </div>

          <div class="grid grid-cols-3 border-t border-white/20 bg-white/8 px-2 py-4">
              <div class="flex min-w-0 items-center justify-center gap-1 border-r border-white/25 px-1">
                  <span class="shrink-0 text-[24px]">👑</span>
                  <div class="min-w-0">
                      <p class="text-[12px] font-bold leading-none text-blue-50">今月順位</p>
                      <p class="mt-2 text-[18px] font-black leading-none">
                          {{ $myMonthlyRank ? $myMonthlyRank . '位' : '-' }}
                      </p>
                  </div>
              </div>

              <div class="flex min-w-0 items-center justify-center gap-1 border-r border-white/25 px-1">
                  <span class="shrink-0 text-[24px]">🪙</span>
                  <div class="min-w-0">
                      <p class="text-[12px] font-bold leading-none text-blue-50">総ポイント</p>
                      <p class="mt-2 text-[18px] font-black leading-none">
                          {{ number_format($myMonthlyPoints) }}<span class="text-[15px]">pt</span>
                      </p>
                  </div>
              </div>

              <div class="flex min-w-0 items-center justify-center gap-1 px-1">
                  <span class="shrink-0 text-[24px]">📈</span>
                  <div class="min-w-0">
                      <p class="text-[12px] font-bold leading-none text-blue-50">あと35ptで</p>
                      <p class="mt-2 text-[18px] font-black leading-none">
                          TOP10!
                      </p>
                  </div>
              </div>
          </div>
      </section>

      {{-- タブ --}}
      <div class="mb-5 grid grid-cols-2 gap-3">
          <button
              type="button"
              data-ranking-group="sp-ranking"
              data-ranking-tab="monthly"
              class="rounded-[8px] border border-[#0D4FE8] bg-[#0D4FE8] px-3 py-3 text-[12px] font-black text-white shadow-[0_8px_18px_rgba(13,79,232,0.18)]">
              月間
          </button>

          <button
              type="button"
              data-ranking-group="sp-ranking"
              data-ranking-tab="total"
              class="rounded-[8px] border border-[#DDE6F5] bg-white px-3 py-3 text-[12px] font-black text-[#071433] shadow-[0_8px_18px_rgba(15,43,95,0.06)]">
              総合
          </button>
      </div>

      {{-- 月間 --}}
      <section data-ranking-group="sp-ranking" data-ranking-panel="monthly">
          @include('trainings.ranking_sp_panel', [
              'topThree' => $monthlyTopThree,
              'rows' => $mobileMonthlyRows,
          ])
      </section>

      {{-- 総合 --}}
      <section data-ranking-group="sp-ranking" data-ranking-panel="total" class="hidden">
          @include('trainings.ranking_sp_panel', [
              'topThree' => $totalTopThree,
              'rows' => $mobileTotalRows,
          ])
      </section>

      {{-- あなたの月間成績 --}}
      <section class="mt-5 overflow-hidden rounded-[18px] border border-[#DDE6F5] bg-white px-5 py-5 shadow-[0_8px_22px_rgba(15,43,95,0.06)]">
          <div class="flex items-center gap-4">
              <div class="flex h-[62px] w-[52px] shrink-0 items-center justify-center text-[72px]">
                  🪴
              </div>

              <div class="min-w-0 flex-1">
                  <h2 class="text-[20px] font-black text-[#071433]">
                      あなたの月間成績
                  </h2>

                  <div class="mt-4 grid grid-cols-2 gap-3">
                      <div>
                          <p class="text-[8px] font-bold text-[#46516B]">🔥 トレーニング日数</p>
                          <p class="mt-1 text-[16px] font-black leading-none text-[#071433]">
                              {{ $myMonthlyTrainingCount }}<span class="text-[15px]">日</span>
                          </p>
                      </div>

                      <div>
                          <p class="text-[8px] font-bold text-[#46516B]">🪙 獲得ポイント</p>
                          <p class="mt-1 text-[16px] font-black leading-none text-[#071433]">
                              {{ number_format($myMonthlyPoints) }}<span class="text-[15px]">pt</span>
                          </p>
                      </div>
                  </div>

                  <p class="mt-4 text-[15px] font-black leading-relaxed text-[#071433]">
                      素晴らしいペースです！<br>
                      一緒にがんばりましょう！
                  </p>
              </div>
          </div>
      </section>
  </div>
</div>
