{{-- SP版：resources/views/trainings/imagination/show_sp.blade.php --}}
<div class="block md:hidden min-h-screen w-full overflow-x-hidden bg-[#F8FAFF] text-[#071433]">
  <div class="mx-auto min-h-screen w-full max-w-[430px] overflow-x-hidden bg-[#F8FAFF] px-3 pb-28 pt-3">

      <div class="mb-3 grid grid-cols-3 items-center text-[12px] font-bold">
          <a href="{{ route('trainings.index') }}" class="min-w-0 truncate text-left text-[#0D4FE8]">
              ‹ 前の画面へ
          </a>

          <div class="flex min-w-0 items-center justify-center gap-1 text-[#46516B]">
              <span class="text-[13px]">🗓️</span>
              <span class="truncate">{{ optional($training->training_date)->format('Y-m-d') }}</span>
          </div>

          <a href="{{ route('trainings.index') }}" class="min-w-0 truncate text-right text-[#0D4FE8]">
              閉じる
          </a>
      </div>

      @if (session('success'))
          <div class="mb-3 rounded-2xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-[13px] font-bold text-emerald-700">
              {{ session('success') }}
          </div>
      @endif

      @if (session('info'))
          <div class="mb-3 rounded-2xl border border-sky-200 bg-sky-50 px-4 py-3 text-[13px] font-bold text-sky-700">
              {{ session('info') }}
          </div>
      @endif

      @if (session('error'))
          <div class="mb-3 rounded-2xl border border-red-200 bg-red-50 px-4 py-3 text-[13px] font-bold text-red-700">
              {{ session('error') }}
          </div>
      @endif

      <header class="mb-3 flex items-start gap-2">
          <div class="flex h-[38px] w-[38px] shrink-0 items-center justify-center rounded-[12px] bg-blue-50 text-[22px] shadow-[inset_0_0_0_1px_rgba(37,99,235,0.12)]">
              🌈
          </div>

          <div class="min-w-0 flex-1">
              <h1 class="text-[18px] font-black leading-tight text-[#071433]">
                  {{ $typeLabel }} 詳細
              </h1>

              <p class="mt-0.5 text-[12px] font-bold leading-snug text-[#46516B]">
                  想像した内容の記録です。
              </p>
          </div>
      </header>

      {{-- 採点結果 --}}
      <section class="mb-3 rounded-[16px] bg-gradient-to-br from-[#1D66F3] to-[#0648D8] px-3 py-3 text-white shadow-[0_8px_18px_rgba(13,79,232,0.22)]">
          <div class="mb-2 flex items-center justify-between">
              <p class="text-[15px] font-black">
                  採点結果
              </p>

              <span class="rounded-full bg-white/20 px-2.5 py-1 text-[10px] font-black">
                  +{{ $earnedPoints }}pt
              </span>
          </div>

          <div class="rounded-[13px] bg-white/12 px-3 py-3 text-center">
              <div class="flex items-end justify-center gap-1">
                  <span class="text-[48px] font-black leading-none">
                      {{ $totalScore ?? '-' }}
                  </span>
                  <span class="mb-1 text-[17px] font-black">
                      点
                  </span>
              </div>

              <p class="mt-2 text-[12px] font-black leading-5 text-blue-50">
                  ✨ {{ $scoreMessage }}
              </p>
          </div>

          <div class="mt-2 grid grid-cols-3 overflow-hidden rounded-[12px] bg-white text-[#071433]">
              <div class="flex flex-col items-center justify-center border-r border-[#DDE6F5] px-1 py-2">
                  <p class="text-[15px]">🔥</p>
                  <p class="mt-1 text-[9px] font-black text-[#0D4FE8]">連続</p>
                  <p class="text-[15px] font-black">{{ $continuousDays ?? 0 }}<span class="text-[9px]">日</span></p>
              </div>

              <div class="flex flex-col items-center justify-center border-r border-[#DDE6F5] px-1 py-2">
                  <p class="text-[15px]">🪙</p>
                  <p class="mt-1 text-[9px] font-black text-[#0D4FE8]">総pt</p>
                  <p class="text-[15px] font-black">{{ $myTotalPoints ?? 0 }}<span class="text-[9px]">pt</span></p>
              </div>

              <div class="flex flex-col items-center justify-center px-1 py-2">
                  <p class="text-[15px]">🏅</p>
                  <p class="mt-1 text-[9px] font-black text-[#0D4FE8]">月間</p>
                  <p class="text-[15px] font-black">{{ $monthlyRank ?? '-' }}<span class="text-[9px]">位</span></p>
              </div>
          </div>
      </section>

      {{-- 評価 --}}
      <section class="mb-3 rounded-[16px] border border-[#DDE6F5] bg-white px-3 py-3 shadow-[0_8px_22px_rgba(15,43,95,0.06)]">
          <div class="mb-2 flex items-center justify-between">
              <h2 class="text-[18px] font-black text-[#071433]">
                  評価
              </h2>

              <span class="rounded-full bg-[#F0F7FF] px-3 py-1 text-[11px] font-black text-[#0D4FE8]">
                  各25点
              </span>
          </div>

          <div class="grid grid-cols-2 gap-2">
              @foreach ($scoreRows as $row)
                  @php
                      $score = $row['score'];
                      $width = $score !== null ? min(100, max(0, ($score / 25) * 100)) : 0;
                      $icon = $row['icon'] ?? '🌈';
                  @endphp

                  <div class="rounded-[13px] border border-[#E1EAF7] bg-[#FBFDFF] px-2.5 py-2.5">
                      <div class="mb-2 flex items-center justify-between gap-2">
                          <div class="flex min-w-0 items-center gap-1.5">
                              <span class="shrink-0 text-[17px]">
                                  {{ $icon }}
                              </span>

                              <span
                                  tabindex="0"
                                  title="{{ $row['label'] }}"
                                  class="group relative min-w-0 cursor-help truncate text-[11px] font-black text-[#1B2540] outline-none"
                              >
                                  {{ $row['label'] }}

                                  <span class="pointer-events-none absolute left-0 top-[20px] z-[9999] hidden min-w-max max-w-[190px] rounded-lg bg-[#071433] px-2 py-1 text-[11px] font-bold leading-5 text-white shadow-lg group-hover:block group-focus:block">
                                      {{ $row['label'] }}
                                  </span>
                              </span>
                          </div>

                          <span class="shrink-0 text-[14px] font-black text-[#0D4FE8]">
                              {{ $score ?? '-' }}<span class="text-[10px] text-[#46516B]">/25</span>
                          </span>
                      </div>

                      <div class="h-2.5 overflow-hidden rounded-full bg-[#E8EDF6]">
                          <div class="h-full rounded-full bg-[#0D4FE8]" style="width: {{ $width }}%;"></div>
                      </div>
                  </div>
              @endforeach
          </div>
      </section>

      {{-- AIコメント --}}
      <section class="mb-3 rounded-[16px] border border-[#DDE6F5] bg-white px-3 py-3 shadow-[0_8px_22px_rgba(15,43,95,0.06)]">
          <h2 class="mb-2 text-[18px] font-black text-[#071433]">
              AIからのコメント
          </h2>

          <div class="space-y-2">
              <div class="rounded-[13px] border border-emerald-200 bg-emerald-50/80 px-3 py-2.5">
                  <h3 class="text-[15px] font-black text-emerald-700">👍 良い点</h3>
                  <p class="mt-1 whitespace-pre-wrap break-words text-[13px] font-bold leading-6 text-[#1B2540]">{{ trim($training->good_point ?? '') ?: '未採点です。' }}</p>
              </div>

              <div class="rounded-[13px] border border-amber-200 bg-amber-50/90 px-3 py-2.5">
                  <h3 class="text-[15px] font-black text-orange-600">↗️ 改善点</h3>
                  <p class="mt-1 whitespace-pre-wrap break-words text-[13px] font-bold leading-6 text-[#1B2540]">{{ trim($training->improvement_point ?? '') ?: '未採点です。' }}</p>
              </div>

              <div class="rounded-[13px] border border-blue-200 bg-blue-50/80 px-3 py-2.5">
                  <h3 class="text-[15px] font-black text-[#0D4FE8]">🎯 次回の課題</h3>
                  <p class="mt-1 whitespace-pre-wrap break-words text-[13px] font-bold leading-6 text-[#1B2540]">{{ trim($training->next_task ?? '') ?: '未採点です。' }}</p>
              </div>
          </div>
      </section>

      {{-- 入力内容 --}}
      <section class="mb-3 rounded-[16px] border border-[#DDE6F5] bg-white px-3 py-3 shadow-[0_8px_22px_rgba(15,43,95,0.06)]">
          <div class="mb-2 flex items-center justify-between gap-3">
              <h2 class="text-[18px] font-black text-[#071433]">
                  入力内容
              </h2>

              <span class="rounded-full border border-[#8DB3FF] bg-[#F0F7FF] px-3 py-1 text-[11px] font-black text-[#0D4FE8]">
                  {{ $training->difficulty_label }}
              </span>
          </div>

          <div class="rounded-[13px] border border-[#BFD6FF] bg-[#F0F7FF] px-3 py-2.5">
              <p class="text-[11px] font-black text-[#0D4FE8]">
                  問題タイプ
              </p>
              <p class="mt-1 text-[14px] font-black text-[#071433]">
                  {{ $training->question_type }}
              </p>
          </div>

          <div class="mt-2 grid gap-2">
              <div class="rounded-[13px] border border-[#E1EAF7] bg-white px-3 py-2.5">
                  <p class="mb-1 text-[12px] font-black text-[#0D4FE8]">
                      問題本文
                  </p>

                  <p class="whitespace-pre-wrap break-words text-[13px] font-bold leading-6 text-[#1B2540]">{{ trim($questionBody) }}</p>
              </div>

              <div class="rounded-[13px] border border-[#E1EAF7] bg-white px-3 py-2.5">
                  <p class="mb-1 text-[12px] font-black text-[#0D4FE8]">
                      あなたの回答
                  </p>

                  <p class="whitespace-pre-wrap break-words text-[13px] font-bold leading-6 text-[#1B2540]">{{ trim($answerBody) }}</p>
              </div>
          </div>
      </section>

      {{-- 模範解答 --}}
      <section class="mb-4 rounded-[16px] border border-emerald-200 bg-emerald-50/80 px-3 py-3 shadow-[0_8px_22px_rgba(15,43,95,0.06)]">
          <h2 class="text-[18px] font-black text-emerald-800">
              模範解答例
          </h2>

          <p class="mt-1 text-[12px] font-bold leading-5 text-emerald-700">
              回答は1つだけではありません。考え方の参考として確認してください。
          </p>

          <div class="mt-2 space-y-2">
              @if (filled($answerPoint))
                  <div class="rounded-[13px] bg-white px-3 py-2.5">
                      <p class="text-[12px] font-black text-emerald-700">回答のポイント</p>
                      <p class="mt-1 whitespace-pre-wrap break-words text-[13px] font-bold leading-6 text-[#1B2540]">{{ $answerPoint }}</p>
                  </div>
              @endif

              @if (filled($modelAnswer))
                  <div class="rounded-[13px] bg-white px-3 py-2.5">
                      <p class="text-[12px] font-black text-emerald-700">模範解答例</p>
                      <p class="mt-1 whitespace-pre-wrap break-words text-[13px] font-bold leading-6 text-[#1B2540]">{{ $modelAnswer }}</p>
                  </div>
              @endif

              @if (filled($alternativeAnswer))
                  <div class="rounded-[13px] bg-white px-3 py-2.5">
                      <p class="text-[12px] font-black text-emerald-700">別解例</p>
                      <p class="mt-1 whitespace-pre-wrap break-words text-[13px] font-bold leading-6 text-[#1B2540]">{{ $alternativeAnswer }}</p>
                  </div>
              @endif
          </div>
      </section>

      <div class="space-y-2">
          <a href="{{ route('trainings.index') }}"
              class="flex h-[48px] w-full items-center justify-center rounded-lg border-2 border-[#0D4FE8] bg-white text-[16px] font-black text-[#0D4FE8]">
              一覧に戻る
          </a>

          <a href="{{ route('trainings.ranking') }}"
              class="flex h-[48px] w-full items-center justify-center gap-2 rounded-lg bg-gradient-to-r from-amber-300 to-amber-500 text-[16px] font-black text-orange-700 shadow-sm">
              🏆 ランキングを見る
          </a>
      </div>
  </div>
</div>
