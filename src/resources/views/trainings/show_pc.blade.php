{{-- PC版：resources/views/trainings/show_pc.blade.php --}}
<div class="hidden md:block bg-[#F8FAFF] text-[#071433]">
  <div class="mx-auto max-w-[1440px] px-8 py-7">
      @if (session('success'))
          <div class="mb-5 rounded-2xl border border-emerald-200 bg-emerald-50 px-5 py-3 text-sm font-bold text-emerald-700 shadow-sm">
              {{ session('success') }}
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
                          📘
                      </div>
                      <div>
                          <h1 class="text-[38px] font-black leading-tight tracking-[0.01em] text-[#071433]">
                              {{ $training->typeLabel() }} 詳細
                          </h1>
                          <div class="mt-7 flex items-center gap-3 text-[18px] font-black text-[#46516B]">
                              <span class="text-[22px]">🗓️</span>
                              <span>{{ $training->training_date->format('Y-m-d') }}</span>
                          </div>
                          <p class="mt-4 text-[17px] font-bold leading-relaxed text-[#24304D]">
                              今日の振り返りが、明日の成長につながります。
                          </p>
                      </div>
                  </div>

                  <div class="relative hidden min-h-[178px] w-[360px] shrink-0 overflow-hidden lg:block">
                    <img
                        src="{{ asset('images/training-top.png') }}"
                        alt="トレーニング"
                        class="h-[178px] w-full object-contain"
                        loading="eager"
                    >
                  </div>
              </div>

              <section class="rounded-[18px] border border-[#DDE6F5] bg-white p-6 shadow-[0_12px_30px_rgba(15,43,95,0.08)]">
                  <div class="mb-5 flex items-center gap-3">
                      <div class="flex h-9 w-9 items-center justify-center rounded-lg bg-blue-100 text-xl">📄</div>
                      <h2 class="text-[24px] font-black text-[#071433]">入力内容</h2>
                  </div>

                  @if ($isAiQuestionTraining)
                      <div class="space-y-5">
                          <div class="rounded-[18px] border border-[#DDE6F5] bg-white p-6">
                              <h3 class="mb-3 flex items-center gap-3 text-[18px] font-black text-[#0D4FE8]">
                                  <span>📘</span>
                                  問題タイトル
                              </h3>
                              <p class="text-[20px] font-black leading-relaxed text-[#071433]">{{ $questionTitle }}</p>
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
                                  回答
                              </h3>
                              <div class="whitespace-pre-wrap rounded-[16px] bg-[#F8FAFF] p-5 text-[18px] font-bold leading-[2] text-[#1B2540]">{{ $answerBody }}</div>
                          </div>
                      </div>
                  @elseif ($type === 'diary')
                      <div class="rounded-[18px] border border-[#DDE6F5] bg-white px-8 py-7">
                          <h3 class="mb-7 flex items-center gap-3 text-[18px] font-black text-[#0D4FE8]">
                              <span>📘</span>
                              今日の日記
                          </h3>
                          <div class="whitespace-pre-wrap text-[25px] font-bold leading-[1.85] tracking-[0.04em] text-[#111827]">{{ $diaryBody }}</div>
                      </div>
                  @elseif ($type === 'challenge')
                      <div class="grid grid-cols-2 gap-4">
                          <div class="rounded-[18px] border border-[#DDE6F5] bg-white p-5">
                              <h3 class="mb-2 text-[16px] font-black text-[#0D4FE8]">今日チャレンジしたこと</h3>
                              <p class="whitespace-pre-wrap text-[18px] font-bold leading-8 text-[#1B2540]">{{ trim($training->challenged_thing ?? '') }}</p>
                          </div>
                          <div class="rounded-[18px] border border-[#DDE6F5] bg-white p-5">
                              <h3 class="mb-2 text-[16px] font-black text-[#0D4FE8]">できたこと</h3>
                              <p class="whitespace-pre-wrap text-[18px] font-bold leading-8 text-[#1B2540]">{{ trim($training->completed_thing ?? '') }}</p>
                          </div>
                          <div class="rounded-[18px] border border-[#DDE6F5] bg-white p-5">
                              <h3 class="mb-2 text-[16px] font-black text-[#0D4FE8]">難しかったこと</h3>
                              <p class="whitespace-pre-wrap text-[18px] font-bold leading-8 text-[#1B2540]">{{ trim($training->difficult_thing ?? '') }}</p>
                          </div>
                          <div class="rounded-[18px] border border-[#DDE6F5] bg-white p-5">
                              <h3 class="mb-2 text-[16px] font-black text-[#0D4FE8]">次に改善したいこと</h3>
                              <p class="whitespace-pre-wrap text-[18px] font-bold leading-8 text-[#1B2540]">{{ trim($training->next_improvement ?? '') }}</p>
                          </div>
                      </div>
                  @endif

                  <div class="mt-6 flex flex-wrap gap-3">
                      <span class="inline-flex items-center gap-2 rounded-lg border border-purple-200 bg-purple-50 px-4 py-2 text-[15px] font-black text-[#2246D2]">
                          📘 {{ $training->typeLabel() }}
                      </span>
                      <span class="inline-flex items-center gap-2 rounded-lg border border-emerald-200 bg-emerald-50 px-4 py-2 text-[15px] font-black text-emerald-700">
                          ✅ 採点済み
                      </span>
                      <span class="inline-flex items-center gap-2 rounded-lg border border-blue-200 bg-blue-50 px-4 py-2 text-[15px] font-black text-[#0D4FE8]">
                          ⭐ 本日の成長記録
                      </span>
                  </div>
              </section>

              <section class="mt-4 rounded-[18px] border border-[#DDE6F5] bg-white p-5 shadow-[0_12px_30px_rgba(15,43,95,0.06)]">
                  <h2 class="mb-4 flex items-center gap-2 text-[20px] font-black text-[#071433]">
                      <span>✨</span>
                      次におすすめのトレーニング
                  </h2>
                  <div class="grid grid-cols-2 gap-5">
                      <a href="{{ route('trainings.index') }}" class="group flex items-center gap-5 rounded-[16px] border border-[#DDE6F5] bg-white p-5 shadow-sm transition hover:-translate-y-0.5 hover:shadow-md">
                          <div class="flex h-[70px] w-[70px] shrink-0 items-center justify-center rounded-full bg-orange-100 text-[38px]">🔥</div>
                          <div class="min-w-0 flex-1">
                              <h3 class="text-[18px] font-black text-[#071433]">今日のチャレンジ</h3>
                              <p class="mt-1 text-[14px] font-bold leading-relaxed text-[#46516B]">挑戦・改善したことを振り返り、成長の一歩を記録しましょう。</p>
                              <span class="mt-3 inline-flex w-[220px] items-center justify-center rounded-lg bg-[#0D4FE8] px-4 py-2 text-[15px] font-black text-white shadow-[0_6px_12px_rgba(13,79,232,0.22)]">開始する ▶</span>
                          </div>
                          <span class="text-[34px] font-light text-[#334155]">›</span>
                      </a>

                      <a href="{{ route('trainings.index') }}" class="group flex items-center gap-5 rounded-[16px] border border-[#DDE6F5] bg-white p-5 shadow-sm transition hover:-translate-y-0.5 hover:shadow-md">
                          <div class="flex h-[70px] w-[70px] shrink-0 items-center justify-center rounded-full bg-purple-100 text-[38px]">📖</div>
                          <div class="min-w-0 flex-1">
                              <h3 class="text-[18px] font-black text-[#071433]">要約力トレーニング</h3>
                              <p class="mt-1 text-[14px] font-bold leading-relaxed text-[#46516B]">文章を要約してまとめる力をさらに伸ばしましょう。</p>
                              <span class="mt-3 inline-flex w-[220px] items-center justify-center rounded-lg bg-[#0D4FE8] px-4 py-2 text-[15px] font-black text-white shadow-[0_6px_12px_rgba(13,79,232,0.22)]">開始する ▶</span>
                          </div>
                          <span class="text-[34px] font-light text-[#334155]">›</span>
                      </a>
                  </div>
              </section>

              <div class="mt-4 grid grid-cols-3 gap-5">
                  <a href="{{ route('trainings.index') }}" class="flex h-[62px] items-center justify-center gap-3 rounded-lg border-2 border-[#0D4FE8] bg-white text-[18px] font-black text-[#0D4FE8]">
                      <span class="text-[26px]">‹</span>
                      一覧に戻る
                  </a>
                  <a href="{{ route('trainings.ranking') }}" class="flex h-[62px] items-center justify-center gap-3 rounded-lg border-2 border-amber-400 bg-white text-[18px] font-black text-amber-700">
                      <span>🏆</span>
                      ランキングを見る
                  </a>
                  <a href="{{ route('trainings.index') }}" class="flex h-[62px] items-center justify-center gap-3 rounded-lg bg-[#0D4FE8] text-[20px] font-black text-white shadow-[0_10px_18px_rgba(13,79,232,0.28)]">
                      次のトレーニングへ
                      <span>▶</span>
                  </a>
              </div>
          </main>

          <aside class="space-y-4">
              <section id="scoreResultSection" class="relative overflow-hidden rounded-[18px] bg-gradient-to-br from-[#1D66F3] to-[#0648D8] p-6 text-white shadow-[0_16px_32px_rgba(13,79,232,0.28)]">
                  <div class="absolute right-8 top-8 text-[26px] text-white/80">✦</div>
                  <div class="absolute left-8 top-20 text-[22px] text-yellow-300">✦</div>
                  <div class="inline-flex rounded-full bg-[#FFE866] px-6 py-2 text-[16px] font-black text-[#071433] shadow-sm">採点結果</div>
                  <div class="mt-2 flex items-end justify-center gap-4">
                      <p class="text-[92px] font-black leading-none tracking-tight drop-shadow-[0_4px_0_rgba(0,0,0,0.13)]">{{ $totalScore ?? '-' }}</p>
                      <p class="mb-3 text-[32px] font-black">点</p>
                      <div class="mb-5 rounded-2xl bg-white px-5 py-3 text-[22px] font-black text-orange-500">+{{ $earnedPoints }}pt</div>
                  </div>
                  <p class="text-center text-[18px] font-black tracking-wide text-blue-50">{{ $scoreMessage }}</p>

                  <div class="mt-5 grid grid-cols-3 rounded-2xl bg-white px-4 py-4 text-[#071433]">
                      <div class="flex items-center justify-center gap-3 border-r border-[#DDE6F5]">
                          <span class="text-[38px]">🔥</span>
                          <div><p class="text-[14px] font-black text-[#0D4FE8]">連続</p><p class="text-[28px] font-black">{{ $continuousDays }}<span class="text-[15px]">日</span></p></div>
                      </div>
                      <div class="flex items-center justify-center gap-3 border-r border-[#DDE6F5]">
                          <span class="text-[38px]">🏅</span>
                          <div><p class="text-[14px] font-black text-[#0D4FE8]">月間</p><p class="text-[28px] font-black">{{ $monthlyRank }}<span class="text-[15px]">位</span></p></div>
                      </div>
                      <div class="flex items-center justify-center gap-3">
                          <span class="text-[38px]">🪙</span>
                          <div><p class="text-[14px] font-black text-[#0D4FE8]">総pt</p><p class="text-[28px] font-black">{{ $totalPoints }}<span class="text-[15px]">pt</span></p></div>
                      </div>
                  </div>
              </section>

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
                          <div class="grid grid-cols-[100px_1fr_70px] items-center gap-3">
                              <span class="text-[15px] font-black text-[#1B2540]">{{ $row['label'] }}</span>
                              <div class="h-3 overflow-hidden rounded-full bg-[#E8EDF6]">
                                  <div class="h-full rounded-full bg-[#0D4FE8]" style="width: {{ $width }}%;"></div>
                              </div>
                              <span class="text-right text-[15px] font-black text-[#0D4FE8]">{{ $score ?? '-' }} <span class="text-[#46516B]">/ 25</span></span>
                          </div>
                      @endforeach
                  </div>
              </section>

              <section class="rounded-[18px] border border-emerald-300 bg-emerald-50/60 p-6 shadow-sm">
                  <h2 class="mb-3 flex items-center gap-3 text-[20px] font-black text-emerald-700"><span class="text-[28px]">👍</span>良い点</h2>
                  <p class="whitespace-pre-wrap pl-12 text-[15px] font-bold leading-8 text-[#1B2540]">{{ trim($training->good_point ?? '') ?: '未採点です。' }}</p>
              </section>

              <section class="rounded-[18px] border border-amber-300 bg-amber-50/70 p-6 shadow-sm">
                  <h2 class="mb-3 flex items-center gap-3 text-[20px] font-black text-amber-700"><span class="text-[28px]">💡</span>改善点</h2>
                  <p class="whitespace-pre-wrap pl-12 text-[15px] font-bold leading-8 text-[#1B2540]">{{ trim($training->improvement_point ?? '') ?: '未採点です。' }}</p>
              </section>

              <section class="rounded-[18px] border border-blue-300 bg-blue-50/70 p-6 shadow-sm">
                  <h2 class="mb-3 flex items-center gap-3 text-[20px] font-black text-[#0D4FE8]"><span class="text-[30px]">🎯</span>次回の課題</h2>
                  <p class="whitespace-pre-wrap pl-12 text-[15px] font-bold leading-8 text-[#1B2540]">{{ trim($training->next_task ?? '') ?: '未採点です。' }}</p>
              </section>
          </aside>
      </div>
  </div>
</div>
