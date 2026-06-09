{{-- PC版：resources/views/trainings/imagination/show_pc.blade.php --}}
<div class="hidden md:block min-h-screen bg-[#F8FAFF] px-8 py-8 text-[#071433]">
  <div class="mx-auto max-w-[1200px]">
      <div class="rounded-3xl bg-white p-8 shadow-sm ring-1 ring-slate-200">
          <h1 class="text-3xl font-black text-[#071433]">
              {{ $typeLabel }} 詳細
          </h1>

          <p class="mt-2 text-sm font-bold text-slate-500">
              {{ optional($training->training_date)->format('Y-m-d') }} ・ {{ $training->difficulty_label }} ・ {{ $training->question_type }}
          </p>

          <div class="mt-8 grid grid-cols-[1fr_360px] gap-6">
              <main class="space-y-5">
                  <section class="rounded-2xl border border-slate-200 bg-slate-50 p-5">
                      <h2 class="text-lg font-black text-[#0D4FE8]">問題本文</h2>
                      <p class="mt-3 whitespace-pre-wrap text-base font-bold leading-8 text-slate-800">{{ $questionBody }}</p>
                  </section>

                  <section class="rounded-2xl border border-slate-200 bg-white p-5">
                      <h2 class="text-lg font-black text-[#0D4FE8]">あなたの回答</h2>
                      <p class="mt-3 whitespace-pre-wrap text-base font-bold leading-8 text-slate-800">{{ $answerBody }}</p>
                  </section>

                  <section class="rounded-2xl border border-emerald-200 bg-emerald-50 p-5">
                      <h2 class="text-lg font-black text-emerald-800">模範解答例</h2>

                      @if (filled($answerPoint))
                          <div class="mt-4 rounded-2xl bg-white p-4">
                              <p class="text-sm font-black text-emerald-700">回答のポイント</p>
                              <p class="mt-2 whitespace-pre-wrap text-sm font-bold leading-7 text-slate-800">{{ $answerPoint }}</p>
                          </div>
                      @endif

                      @if (filled($modelAnswer))
                          <div class="mt-4 rounded-2xl bg-white p-4">
                              <p class="text-sm font-black text-emerald-700">模範解答例</p>
                              <p class="mt-2 whitespace-pre-wrap text-sm font-bold leading-7 text-slate-800">{{ $modelAnswer }}</p>
                          </div>
                      @endif

                      @if (filled($alternativeAnswer))
                          <div class="mt-4 rounded-2xl bg-white p-4">
                              <p class="text-sm font-black text-emerald-700">別解例</p>
                              <p class="mt-2 whitespace-pre-wrap text-sm font-bold leading-7 text-slate-800">{{ $alternativeAnswer }}</p>
                          </div>
                      @endif
                  </section>
              </main>

              <aside class="space-y-4">
                  <section class="rounded-3xl bg-gradient-to-br from-[#1D66F3] to-[#0648D8] p-6 text-white">
                      <p class="text-sm font-black">採点結果</p>
                      <div class="mt-3 flex items-end justify-center gap-2">
                          <span class="text-6xl font-black">{{ $totalScore ?? '-' }}</span>
                          <span class="mb-1 text-2xl font-black">点</span>
                      </div>
                      <p class="mt-3 text-center text-sm font-bold">+{{ $earnedPoints }}pt</p>
                      <p class="mt-3 text-center text-sm font-bold">{{ $scoreMessage }}</p>
                  </section>

                  <section class="rounded-2xl border border-slate-200 bg-white p-5">
                      <h2 class="text-lg font-black text-[#071433]">評価</h2>

                      <div class="mt-4 space-y-4">
                          @foreach ($scoreRows as $row)
                              @php
                                  $score = $row['score'];
                                  $width = $score !== null ? min(100, max(0, ($score / 25) * 100)) : 0;
                              @endphp

                              <div>
                                  <div class="mb-1 flex justify-between text-sm font-black">
                                      <span>{{ $row['icon'] }} {{ $row['label'] }}</span>
                                      <span class="text-[#0D4FE8]">{{ $score ?? '-' }}/25</span>
                                  </div>
                                  <div class="h-2.5 overflow-hidden rounded-full bg-slate-200">
                                      <div class="h-full rounded-full bg-[#0D4FE8]" style="width: {{ $width }}%;"></div>
                                  </div>
                              </div>
                          @endforeach
                      </div>
                  </section>

                  <section class="rounded-2xl border border-emerald-200 bg-emerald-50 p-5">
                      <h2 class="text-base font-black text-emerald-700">良い点</h2>
                      <p class="mt-2 whitespace-pre-wrap text-sm font-bold leading-7">{{ trim($training->good_point ?? '') ?: '未採点です。' }}</p>
                  </section>

                  <section class="rounded-2xl border border-amber-200 bg-amber-50 p-5">
                      <h2 class="text-base font-black text-amber-700">改善点</h2>
                      <p class="mt-2 whitespace-pre-wrap text-sm font-bold leading-7">{{ trim($training->improvement_point ?? '') ?: '未採点です。' }}</p>
                  </section>

                  <section class="rounded-2xl border border-blue-200 bg-blue-50 p-5">
                      <h2 class="text-base font-black text-[#0D4FE8]">次回の課題</h2>
                      <p class="mt-2 whitespace-pre-wrap text-sm font-bold leading-7">{{ trim($training->next_task ?? '') ?: '未採点です。' }}</p>
                  </section>
              </aside>
          </div>

          <div class="mt-8 flex justify-between">
              <a href="{{ route('trainings.index') }}"
                  class="rounded-2xl border-2 border-[#0D4FE8] bg-white px-8 py-3 text-sm font-black text-[#0D4FE8]">
                  一覧に戻る
              </a>

              <a href="{{ route('trainings.ranking') }}"
                  class="rounded-2xl bg-[#0D4FE8] px-8 py-3 text-sm font-black text-white">
                  ランキングを見る
              </a>
          </div>
      </div>
  </div>
</div>
