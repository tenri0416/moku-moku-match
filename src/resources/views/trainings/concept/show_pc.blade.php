{{-- PC版：resources/views/trainings/concept/show_pc.blade.php --}}
<div class="hidden md:block bg-[#F8FAFF] text-[#071433]">
  <div class="mx-auto max-w-[1440px] px-8 py-7">
      @if (session('success'))
          <div class="mb-5 rounded-2xl border border-emerald-200 bg-emerald-50 px-5 py-3 text-sm font-bold text-emerald-700 shadow-sm">
              {{ session('success') }}
          </div>
      @endif

      @if (session('info'))
          <div class="mb-5 rounded-2xl border border-sky-200 bg-sky-50 px-5 py-3 text-sm font-bold text-sky-700 shadow-sm">
              {{ session('info') }}
          </div>
      @endif

      @if (session('error'))
          <div class="mb-5 rounded-2xl border border-red-200 bg-red-50 px-5 py-3 text-sm font-bold text-red-700 shadow-sm">
              {{ session('error') }}
          </div>
      @endif

      <div class="grid grid-cols-[minmax(0,1fr)_480px] gap-7 xl:grid-cols-[minmax(0,1fr)_520px]">
          <main>
              <div class="mb-5 flex items-start justify-between gap-6">
                  <div class="flex items-start gap-5">
                      <div class="flex h-[74px] w-[74px] shrink-0 items-center justify-center rounded-[20px] bg-blue-50 text-[52px] shadow-[inset_0_0_0_1px_rgba(37,99,235,0.12)]">
                          🧩
                      </div>

                      <div>
                          <h1 class="text-[34px] font-black leading-tight tracking-[0.01em] text-[#071433]">
                              {{ $typeLabel }} 詳細
                          </h1>

                          <div class="mt-7 flex items-center gap-3 text-[18px] font-black text-[#46516B]">
                              <span class="text-[22px]">🗓️</span>
                              <span>{{ optional($training->training_date)->format('Y-m-d') }}</span>
                          </div>

                          <p class="mt-4 text-[17px] font-bold leading-relaxed text-[#24304D]">
                              一見違う2つの言葉から、共通する本質を見つける練習です。
                          </p>
                      </div>
                  </div>

                  <div class="relative hidden min-h-[178px] w-[270px] shrink-0 overflow-hidden lg:block">
                      <img
                          src="{{ asset('images/training-top.png') }}"
                          alt="トレーニング"
                          class="h-[178px] w-full object-contain"
                          loading="eager"
                      >
                  </div>
              </div>

              {{-- 入力内容 --}}
              <section class="rounded-[18px] border border-[#DDE6F5] bg-white p-6 shadow-[0_12px_30px_rgba(15,43,95,0.08)]">
                  <div class="mb-5 flex items-center gap-3">
                      <div class="flex h-9 w-9 items-center justify-center rounded-lg bg-blue-100 text-xl">📄</div>
                      <h2 class="text-[24px] font-black text-[#071433]">入力内容</h2>
                  </div>

                  <div class="space-y-5">
                      <div class="rounded-[18px] border border-[#DDE6F5] bg-white p-6">
                          <div class="mb-4 flex items-center justify-between gap-4">
                              <h3 class="flex items-center gap-3 text-[18px] font-black text-[#0D4FE8]">
                                  <span>🧩</span>
                                  本日のテーマ
                              </h3>

                              <span class="rounded-full border border-[#8DB3FF] bg-[#F0F7FF] px-4 py-1 text-[14px] font-black text-[#0D4FE8]">
                                  {{ $training->difficulty_label }}
                              </span>
                          </div>

                          <div class="rounded-[16px] border border-[#BFD6FF] bg-[#F0F7FF] px-5 py-5 text-center">
                              <p class="text-[15px] font-black text-[#0D4FE8]">テーマ</p>
                              <p class="mt-2 text-[34px] font-black leading-tight text-[#071433]">
                                  {{ $training->theme_a }}
                                  <span class="mx-3 text-[#94A3B8]">×</span>
                                  {{ $training->theme_b }}
                              </p>
                          </div>
                      </div>

                      <div class="rounded-[18px] border border-[#DDE6F5] bg-white p-6">
                          <h3 class="mb-3 flex items-center gap-3 text-[18px] font-black text-[#0D4FE8]">
                              <span>📖</span>
                              問題本文
                          </h3>

                          <div class="whitespace-pre-wrap rounded-[16px] bg-[#F8FAFF] p-5 text-[18px] font-bold leading-[2] text-[#1B2540]">{{ $questionBody }}</div>
                      </div>

                      <div class="rounded-[18px] border border-[#DDE6F5] bg-white p-6">
                          <h3 class="mb-3 flex items-center gap-3 text-[18px] font-black text-[#0D4FE8]">
                              <span>✏️</span>
                              あなたの回答
                          </h3>

                          <div class="whitespace-pre-wrap rounded-[16px] bg-[#F8FAFF] p-5 text-[18px] font-bold leading-[2] text-[#1B2540]">{{ $answerBody }}</div>
                      </div>
                  </div>

                  <div class="mt-6 flex flex-wrap gap-3">
                      <span class="inline-flex items-center gap-2 rounded-lg border border-purple-200 bg-purple-50 px-4 py-2 text-[15px] font-black text-[#2246D2]">
                          🧩 {{ $typeLabel }}
                      </span>

                      <span class="inline-flex items-center gap-2 rounded-lg border border-emerald-200 bg-emerald-50 px-4 py-2 text-[15px] font-black text-emerald-700">
                          ✅ 採点済み
                      </span>

                      <span class="inline-flex items-center gap-2 rounded-lg border border-blue-200 bg-blue-50 px-4 py-2 text-[15px] font-black text-[#0D4FE8]">
                          ⭐ 本日の成長記録
                      </span>
                  </div>
              </section>

              {{-- 模範解答例 --}}
              <section class="mt-4 rounded-[18px] border border-emerald-200 bg-emerald-50/80 p-6 shadow-[0_12px_30px_rgba(15,43,95,0.06)]">
                  <div class="mb-5 flex items-center justify-between gap-3">
                      <div>
                          <h2 class="flex items-center gap-3 text-[22px] font-black text-emerald-800">
                              <span>✨</span>
                              模範解答例
                          </h2>

                          <p class="mt-2 text-[14px] font-bold leading-relaxed text-emerald-700">
                              回答は1つだけではありません。考え方の参考として確認してください。
                          </p>
                      </div>

                      <span class="rounded-full bg-white px-4 py-2 text-[13px] font-black text-emerald-700 shadow-sm">
                          参考
                      </span>
                  </div>

                  @if (filled($answerPoint))
                      <div class="mb-4 rounded-[16px] bg-white p-5">
                          <p class="text-[14px] font-black text-emerald-700">回答のポイント</p>
                          <p class="mt-2 whitespace-pre-wrap text-[15px] font-bold leading-8 text-[#1B2540]">{{ $answerPoint }}</p>
                      </div>
                  @endif

                  @if (filled($modelAnswer))
                      <div class="mb-4 rounded-[16px] bg-white p-5">
                          <p class="text-[14px] font-black text-emerald-700">模範解答例</p>
                          <p class="mt-2 whitespace-pre-wrap text-[15px] font-bold leading-8 text-[#1B2540]">{{ $modelAnswer }}</p>
                      </div>
                  @endif

                  @if (filled($alternativeAnswer))
                      <div class="rounded-[16px] bg-white p-5">
                          <p class="text-[14px] font-black text-emerald-700">別解例</p>
                          <p class="mt-2 whitespace-pre-wrap text-[15px] font-bold leading-8 text-[#1B2540]">{{ $alternativeAnswer }}</p>
                      </div>
                  @endif
              </section>

              {{-- 下部ボタン --}}
              <div class="mt-4 grid grid-cols-3 gap-5">
                  <a href="{{ route('trainings.index') }}"
                      class="flex h-[62px] items-center justify-center gap-3 rounded-lg border-2 border-[#0D4FE8] bg-white text-[18px] font-black text-[#0D4FE8]">
                      <span class="text-[26px]">‹</span>
                      一覧に戻る
                  </a>

                  <a href="{{ route('trainings.ranking') }}"
                      class="flex h-[62px] items-center justify-center gap-3 rounded-lg border-2 border-amber-400 bg-white text-[18px] font-black text-amber-700">
                      <span>🏆</span>
                      ランキングを見る
                  </a>

                  <a href="{{ route('trainings.index') }}"
                      class="flex h-[62px] items-center justify-center gap-3 rounded-lg bg-[#0D4FE8] text-[20px] font-black text-white shadow-[0_10px_18px_rgba(13,79,232,0.28)]">
                      次のトレーニングへ
                      <span>▶</span>
                  </a>
              </div>
          </main>

          <aside class="space-y-4">
              {{-- 採点結果 --}}
              <section id="scoreResultSection"
                  class="relative overflow-hidden rounded-[18px] bg-gradient-to-br from-[#1D66F3] to-[#0648D8] p-6 text-white shadow-[0_16px_32px_rgba(13,79,232,0.28)]">
                  <div class="absolute right-8 top-8 text-[26px] text-white/80">✦</div>
                  <div class="absolute left-8 top-20 text-[22px] text-yellow-300">✦</div>

                  <div class="inline-flex rounded-full bg-[#FFE866] px-6 py-2 text-[16px] font-black text-[#071433] shadow-sm">
                      採点結果
                  </div>

                  <div class="mt-2 flex items-end justify-center gap-4">
                      <p class="text-[92px] font-black leading-none tracking-tight drop-shadow-[0_4px_0_rgba(0,0,0,0.13)]">
                          {{ $totalScore ?? '-' }}
                      </p>
                      <p class="mb-3 text-[32px] font-black">点</p>

                      <div class="mb-5 rounded-2xl bg-white px-5 py-3 text-[22px] font-black text-orange-500">
                          +{{ $earnedPoints }}pt
                      </div>
                  </div>

                  <p class="text-center text-[18px] font-black tracking-wide text-blue-50">
                      {{ $scoreMessage }}
                  </p>

                  <div class="mt-5 grid grid-cols-3 rounded-2xl bg-white px-4 py-4 text-[#071433]">
                      <div class="flex items-center justify-center gap-3 border-r border-[#DDE6F5]">
                          <span class="text-[38px]">🔥</span>
                          <div>
                              <p class="text-[14px] font-black text-[#0D4FE8]">連続</p>
                              <p class="text-[28px] font-black">{{ $continuousDays }}<span class="text-[15px]">日</span></p>
                          </div>
                      </div>

                      <div class="flex items-center justify-center gap-3 border-r border-[#DDE6F5]">
                          <span class="text-[38px]">🏅</span>
                          <div>
                              <p class="text-[14px] font-black text-[#0D4FE8]">月間</p>
                              <p class="text-[28px] font-black">{{ $monthlyRank }}<span class="text-[15px]">位</span></p>
                          </div>
                      </div>

                      <div class="flex items-center justify-center gap-3">
                          <span class="text-[38px]">🪙</span>
                          <div>
                              <p class="text-[14px] font-black text-[#0D4FE8]">総pt</p>
                              <p class="text-[28px] font-black">{{ $myTotalPoints }}<span class="text-[15px]">pt</span></p>
                          </div>
                      </div>
                  </div>
              </section>

              {{-- 評価 --}}
              <section class="rounded-[18px] border border-[#DDE6F5] bg-white p-6 shadow-[0_12px_30px_rgba(15,43,95,0.08)]">
                  <h2 class="mb-5 flex items-center gap-3 text-[20px] font-black text-[#071433]">
                      <span class="text-[#0D4FE8]">📊</span>
                      評価
                  </h2>

                  <div class="space-y-5">
                      @foreach ($scoreRows as $row)
                          @php
                              $score = $row['score'];
                              $width = $score !== null ? min(100, max(0, ($score / 25) * 100)) : 0;
                          @endphp

                          <div class="grid grid-cols-[120px_1fr_70px] items-center gap-3">
                              <span class="text-[15px] font-black text-[#1B2540]">
                                  {{ $row['label'] }}
                              </span>

                              <div class="h-3 overflow-hidden rounded-full bg-[#E8EDF6]">
                                  <div class="h-full rounded-full bg-[#0D4FE8]" style="width: {{ $width }}%;"></div>
                              </div>

                              <span class="text-right text-[15px] font-black text-[#0D4FE8]">
                                  {{ $score ?? '-' }}
                                  <span class="text-[#46516B]">/ 25</span>
                              </span>
                          </div>
                      @endforeach
                  </div>
              </section>

              {{-- 良い点 --}}
              <section class="rounded-[18px] border border-emerald-300 bg-emerald-50/60 p-6 shadow-sm">
                  <h2 class="mb-3 flex items-center gap-3 text-[20px] font-black text-emerald-700">
                      <span class="text-[28px]">👍</span>
                      良い点
                  </h2>

                  <p class="whitespace-pre-wrap pl-12 text-[15px] font-bold leading-8 text-[#1B2540]">{{ trim($training->good_point ?? '') ?: '未採点です。' }}</p>
              </section>

              {{-- 改善点 --}}
              <section class="rounded-[18px] border border-amber-300 bg-amber-50/70 p-6 shadow-sm">
                  <h2 class="mb-3 flex items-center gap-3 text-[20px] font-black text-amber-700">
                      <span class="text-[28px]">💡</span>
                      改善点
                  </h2>

                  <p class="whitespace-pre-wrap pl-12 text-[15px] font-bold leading-8 text-[#1B2540]">{{ trim($training->improvement_point ?? '') ?: '未採点です。' }}</p>
              </section>

              {{-- 次回の課題 --}}
              <section class="rounded-[18px] border border-blue-300 bg-blue-50/70 p-6 shadow-sm">
                  <h2 class="mb-3 flex items-center gap-3 text-[20px] font-black text-[#0D4FE8]">
                      <span class="text-[30px]">🎯</span>
                      次回の課題
                  </h2>

                  <p class="whitespace-pre-wrap pl-12 text-[15px] font-bold leading-8 text-[#1B2540]">{{ trim($training->next_task ?? '') ?: '未採点です。' }}</p>
              </section>
          </aside>
      </div>
  </div>
</div>
