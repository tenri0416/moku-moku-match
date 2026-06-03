{{-- PC版：resources/views/trainings/ranking_pc.blade.php --}}
<div class="hidden md:block min-h-screen bg-[#F8FAFF] text-[#071433]">
  <div class="mx-auto w-full max-w-[1440px] px-8 py-8">

      <div class="grid grid-cols-[1fr_380px] gap-8">
          {{-- 左側 --}}
          <main>
              {{-- ヒーロー --}}
              <section class="mb-6">
                  <div class="grid grid-cols-[1fr_430px] items-center gap-6">
                      <div>
                          <div class="mb-4 flex items-center gap-5">
                              <span class="text-[70px] leading-none">🏆</span>

                              <h1 class="text-[48px] font-black leading-tight tracking-[0.01em] text-[#071433]">
                                  トレーニングランキング
                              </h1>
                          </div>

                          <p class="text-[18px] font-bold leading-relaxed text-[#334155]">
                              毎日の積み重ねが見える。成長を楽しもう。
                          </p>

                          <p class="mt-4 text-[16px] font-bold leading-relaxed text-[#334155]">
                              仲間と競い合いながら、楽しく続けてスキルアップ！上位を目指してがんばろう！
                          </p>
                      </div>

                      <div class="relative flex h-[190px] items-end justify-center">
                          <div class="absolute inset-x-0 bottom-0 mx-auto flex w-[330px] items-end justify-center gap-2">
                              <div class="flex h-[68px] w-[88px] items-center justify-center rounded-t-[12px] bg-blue-300 text-[34px] font-black text-white">2</div>
                              <div class="relative flex h-[108px] w-[100px] items-center justify-center rounded-t-[12px] bg-yellow-400 text-[38px] font-black text-white">
                                  <span class="absolute -top-12 text-[54px]">🏆</span>
                                  1
                              </div>
                              <div class="flex h-[58px] w-[88px] items-center justify-center rounded-t-[12px] bg-orange-400 text-[34px] font-black text-white">3</div>
                          </div>

                          <div class="absolute left-6 top-4 text-[24px] text-yellow-400">✦</div>
                          <div class="absolute right-8 top-8 text-[24px] text-yellow-400">✦</div>
                          <div class="absolute left-20 bottom-14 text-[20px] text-orange-400">◆</div>
                          <div class="absolute right-20 bottom-16 text-[20px] text-emerald-400">◆</div>
                      </div>
                  </div>
              </section>

              {{-- 青い状況カード --}}
              <section class="mb-6 overflow-hidden rounded-[18px] bg-gradient-to-br from-[#1D66F3] to-[#0648D8] px-7 py-6 text-white shadow-[0_14px_28px_rgba(13,79,232,0.28)]">
                  <div class="grid grid-cols-[300px_1fr_280px] divide-x divide-white/25">
                      <div class="flex items-center gap-5 pr-7">
                          <div class="h-[96px] w-[96px] overflow-hidden rounded-full bg-blue-100 ring-4 ring-white/60">
                              @if ($myMonthlyRanking)
                                  <img
                                      src="{{ $avatarUrl($myMonthlyRanking) }}"
                                      alt="あなた"
                                      class="h-full w-full object-cover"
                                  >
                              @else
                                  <img
                                      src="{{ asset('images/default-avatar.png') }}"
                                      alt="あなた"
                                      class="h-full w-full object-cover"
                                  >
                              @endif
                          </div>

                          <div>
                              <p class="text-[16px] font-bold text-blue-50">あなたの今月順位</p>
                              <p class="mt-1 text-[52px] font-black leading-none">
                                  {{ $myMonthlyRank ? $myMonthlyRank . '位' : '-' }}
                                  <span class="ml-2 text-[22px] text-emerald-300">↑3</span>
                              </p>
                              <p class="mt-3 text-[18px] font-bold text-blue-50">
                                  / 256人中
                              </p>
                          </div>
                      </div>

                      <div class="px-8">
                          <div class="grid grid-cols-2 gap-6">
                              <div class="flex items-center gap-4">
                                  <span class="text-[42px]">🏅</span>
                                  <div>
                                      <p class="text-[16px] font-bold text-blue-50">今月のポイント</p>
                                      <p class="mt-1 text-[36px] font-black leading-none">
                                          {{ number_format($myMonthlyPoints) }}<span class="text-[20px]">pt</span>
                                      </p>
                                  </div>
                              </div>

                              <div class="flex items-center gap-4">
                                  <span class="text-[38px]">🗓️</span>
                                  <div>
                                      <p class="text-[16px] font-bold text-blue-50">今月のトレーニング回数</p>
                                      <p class="mt-1 text-[36px] font-black leading-none">
                                          {{ $myMonthlyTrainingCount }}<span class="text-[20px]">回</span>
                                      </p>
                                  </div>
                              </div>
                          </div>

                          <div class="mt-5">
                              <p class="mb-2 text-[16px] font-bold text-blue-50">
                                  @if ($pointsToTopTen !== null)
                                      あと{{ $pointsToTopTen }}ptでTOP10！
                                  @else
                                      あと35ptでTOP10！
                                  @endif
                              </p>

                              <div class="h-3 overflow-hidden rounded-full bg-white/25">
                                  <div class="h-full w-[72%] rounded-full bg-yellow-300"></div>
                              </div>

                              <p class="mt-2 text-right text-[15px] font-bold text-blue-50">
                                  {{ number_format($myMonthlyPoints) }} / {{ number_format($myMonthlyPoints + ($pointsToTopTen ?? 35)) }}pt
                              </p>
                          </div>
                      </div>

                      <div class="pl-8">
                          <p class="text-[16px] font-bold text-blue-50">ランキングタイプ</p>

                          <div class="mt-4 grid grid-cols-2 gap-3">
                              <button
                                  type="button"
                                  data-ranking-group="pc-ranking"
                                  data-ranking-tab="monthly"
                                  class="rounded-[12px] border border-white bg-white px-4 py-3 text-[16px] font-black text-[#0D4FE8]">
                                  月間
                              </button>

                              <button
                                  type="button"
                                  data-ranking-group="pc-ranking"
                                  data-ranking-tab="total"
                                  class="rounded-[12px] border border-white/40 bg-white/10 px-4 py-3 text-[16px] font-black text-white">
                                  総合
                              </button>
                          </div>
                      </div>
                  </div>
              </section>

              {{-- 月間ランキング --}}
              <section data-ranking-group="pc-ranking" data-ranking-panel="monthly">
                  @include('trainings.ranking_pc_panel', [
                      'topThree' => $monthlyTopThree,
                      'rankings' => $monthlyRankings,
                  ])
              </section>

              {{-- 総合ランキング --}}
              <section data-ranking-group="pc-ranking" data-ranking-panel="total" class="hidden">
                  @include('trainings.ranking_pc_panel', [
                      'topThree' => $totalTopThree,
                      'rankings' => $totalRankings,
                  ])
              </section>
          </main>

          {{-- 右側 --}}
          <aside class="space-y-5">
              <section class="rounded-[18px] border border-[#DDE6F5] bg-white px-6 py-6 shadow-[0_8px_22px_rgba(15,43,95,0.06)]">
                  <h2 class="mb-6 flex items-center gap-3 text-[22px] font-black text-[#071433]">
                      💡
                      ランキングの見方
                  </h2>

                  <div class="space-y-6">
                      <div class="flex gap-4">
                          <span class="flex h-12 w-12 shrink-0 items-center justify-center rounded-full bg-blue-100 text-[26px]">📅</span>
                          <div>
                              <h3 class="text-[17px] font-black text-[#071433]">月間ランキング</h3>
                              <p class="mt-1 text-[14px] font-bold leading-relaxed text-[#46516B]">
                                  毎月1日〜月末までのポイントを競います
                              </p>
                          </div>
                      </div>

                      <div class="flex gap-4">
                          <span class="flex h-12 w-12 shrink-0 items-center justify-center rounded-full bg-blue-100 text-[26px]">👑</span>
                          <div>
                              <h3 class="text-[17px] font-black text-[#071433]">総合ランキング</h3>
                              <p class="mt-1 text-[14px] font-bold leading-relaxed text-[#46516B]">
                                  これまでに獲得した総ポイントで競います
                              </p>
                          </div>
                      </div>

                      <div class="flex gap-4">
                          <span class="flex h-12 w-12 shrink-0 items-center justify-center rounded-full bg-blue-100 text-[26px]">⭐</span>
                          <div>
                              <h3 class="text-[17px] font-black text-[#071433]">ポイントについて</h3>
                              <p class="mt-1 text-[14px] font-bold leading-relaxed text-[#46516B]">
                                  各トレーニングの達成でポイントを獲得できます
                              </p>
                          </div>
                      </div>

                      <div class="flex gap-4">
                          <span class="flex h-12 w-12 shrink-0 items-center justify-center rounded-full bg-blue-100 text-[26px]">🎖️</span>
                          <div>
                              <h3 class="text-[17px] font-black text-[#071433]">同点の場合</h3>
                              <p class="mt-1 text-[14px] font-bold leading-relaxed text-[#46516B]">
                                  同点の場合は、トレーニング回数が多い方が上位になります
                              </p>
                          </div>
                      </div>
                  </div>
              </section>

              <section class="rounded-[18px] border border-[#DDE6F5] bg-white px-6 py-6 shadow-[0_8px_22px_rgba(15,43,95,0.06)]">
                  <h2 class="mb-5 flex items-center gap-3 text-[22px] font-black text-[#071433]">
                      📊
                      今日の状況
                  </h2>

                  <div class="grid grid-cols-3 gap-3">
                      <div class="rounded-[12px] border border-[#DDE6F5] bg-white px-3 py-4 text-center">
                          <div class="text-[32px]">🔥</div>
                          <p class="mt-2 text-[14px] font-black text-[#334155]">連続</p>
                          <p class="mt-1 text-[28px] font-black leading-none text-[#071433]">
                              {{ $continuousDays }}<span class="text-[14px]">日</span>
                          </p>
                      </div>

                      <div class="rounded-[12px] border border-[#DDE6F5] bg-white px-3 py-4 text-center">
                          <div class="text-[32px]">🏅</div>
                          <p class="mt-2 text-[14px] font-black text-[#334155]">総pt</p>
                          <p class="mt-1 text-[25px] font-black leading-none text-[#071433]">
                              {{ number_format($myMonthlyPoints) }}<span class="text-[13px]">pt</span>
                          </p>
                      </div>

                      <div class="rounded-[12px] border border-[#DDE6F5] bg-white px-3 py-4 text-center">
                          <div class="text-[32px]">🏅</div>
                          <p class="mt-2 text-[14px] font-black text-[#334155]">月間</p>
                          <p class="mt-1 text-[28px] font-black leading-none text-[#071433]">
                              {{ $myMonthlyRank ?? '-' }}<span class="text-[14px]">位</span>
                          </p>
                      </div>
                  </div>
              </section>

              <section class="relative overflow-hidden rounded-[18px] border border-[#BFD6FF] bg-[#F0F7FF] px-6 py-6 shadow-[0_8px_22px_rgba(15,43,95,0.06)]">
                  <div class="pr-20">
                      <p class="text-[18px] font-black leading-relaxed text-[#0D4FE8]">
                          一歩一歩が、未来の自分をつくる！
                      </p>

                      <p class="mt-4 text-[15px] font-bold leading-relaxed text-[#334155]">
                          続けることで、確実に力になります。<br>
                          昨日の自分を超える毎日を<br>
                          楽しんでいきましょう！
                      </p>
                  </div>

                  <div class="absolute bottom-5 right-5 text-[64px]">
                      🪴
                  </div>
              </section>

              <section class="relative overflow-hidden rounded-[18px] border border-[#DDE6F5] bg-white px-6 py-6 shadow-[0_8px_22px_rgba(15,43,95,0.06)]">
                  <div class="pr-20">
                      <p class="text-[18px] font-black leading-relaxed text-[#0D4FE8]">
                          上位を目指してみよう！
                      </p>

                      <p class="mt-4 text-[15px] font-bold leading-relaxed text-[#334155]">
                          トップ10に入ると限定バッジを獲得！<br>
                          さらに素敵なご褒美が待っているかも？
                      </p>

                      <a href="{{ route('trainings.index') }}"
                          class="mt-5 flex h-[44px] w-[140px] items-center justify-center gap-2 rounded-[10px] border border-[#DDE6F5] bg-white text-[15px] font-black text-[#0D4FE8]">
                          詳しく見る
                          <span class="text-[20px]">›</span>
                      </a>
                  </div>

                  <div class="absolute bottom-5 right-4 text-[70px]">
                      🏅
                  </div>
              </section>
          </aside>
      </div>
  </div>
</div>
