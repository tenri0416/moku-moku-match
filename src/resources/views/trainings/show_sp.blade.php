{{-- SP版：resources/views/trainings/show_sp.blade.php --}}
<div class="block md:hidden min-h-screen w-full overflow-x-hidden bg-[#F8FAFF] text-[#071433]">
  <div class="mx-auto min-h-screen w-full max-w-[430px] overflow-x-hidden bg-[#F8FAFF] px-3 pb-28 pt-3">

      {{-- 上部ナビ --}}
      <div class="mb-4 grid grid-cols-3 items-center text-[13px] font-bold">
          <a href="{{ route('trainings.index') }}" class="min-w-0 truncate text-left text-[#0D4FE8]">
              ‹ 前の画面へ
          </a>

          <div class="flex min-w-0 items-center justify-center gap-1 text-[#46516B]">
              <span class="text-[15px]">🗓️</span>
              <span class="truncate">{{ $training->training_date->format('Y-m-d') }}</span>
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

      @if (session('error'))
          <div class="mb-3 rounded-2xl border border-red-200 bg-red-50 px-4 py-3 text-[13px] font-bold text-red-700">
              {{ session('error') }}
          </div>
      @endif

      {{-- タイトル --}}
      <header class="mb-4 flex items-start gap-3">
          <div class="flex h-[52px] w-[52px] shrink-0 items-center justify-center rounded-[16px] bg-blue-50 text-[32px] shadow-[inset_0_0_0_1px_rgba(37,99,235,0.12)]">
              📘
          </div>

          <div class="min-w-0 flex-1">
              <h1 class="break-words text-[24px] font-black leading-tight tracking-[0.01em] text-[#071433]">
                  {{ $training->typeLabel() }} 詳細
              </h1>

              <p class="mt-1 text-[14px] font-bold leading-relaxed text-[#46516B]">
                  今日の振り返りが、明日の成長につながります。
              </p>
          </div>
      </header>

      {{-- 採点結果 --}}
      <section id="scoreResultSection"
          class="relative mb-4 overflow-hidden rounded-[18px] bg-gradient-to-br from-[#1D66F3] to-[#0648D8] px-3 pb-4 pt-4 text-white shadow-[0_12px_26px_rgba(13,79,232,0.28)]">

          {{-- 装飾 --}}
          <div class="pointer-events-none absolute right-4 top-4 text-[22px] text-yellow-300">★</div>
          <div class="pointer-events-none absolute right-24 top-6 h-2 w-2 rotate-45 bg-orange-300"></div>
          <div class="pointer-events-none absolute right-14 top-28 h-2 w-2 rotate-45 bg-sky-300"></div>

          {{-- 上段：点数・ポイント・画像 --}}
          <div class="relative z-10 flex w-full items-start justify-between gap-2">
              <div class="min-w-0 flex-1">
                  <p class="text-[18px] font-black leading-none">
                      採点結果
                  </p>

                  <div class="mt-3 flex min-w-0 items-center gap-2">
                      <div class="flex shrink-0 items-end gap-1">
                          <span class="text-[54px] font-black leading-none drop-shadow-[0_4px_0_rgba(0,0,0,0.14)]">
                              {{ $totalScore ?? '-' }}
                          </span>
                          <span class="mb-1 text-[23px] font-black">
                              点
                          </span>
                      </div>

                      <div class="w-[82px] shrink-0 rounded-[10px] bg-white px-2 py-2.5 text-center shadow-sm">
                          <p class="text-[10px] font-black leading-tight text-[#071433]">
                              獲得<br>ポイント
                          </p>
                          <p class="mt-1 text-[21px] font-black leading-none text-orange-500">
                              +{{ $earnedPoints }}pt
                          </p>
                      </div>
                  </div>

                  <p class="mt-3 text-[14px] font-black leading-relaxed text-blue-50">
                      ✨ {{ $scoreMessage }}
                  </p>
              </div>

              {{-- SP用：喜んでいる青年画像 --}}
              <div class="flex h-[96px] w-[78px] shrink-0 items-center justify-center overflow-hidden">
                  <img
                      src="{{ asset('images/training-top_sp.png') }}"
                      alt="トレーニング結果"
                      class="h-[96px] w-[96px] object-contain"
                      loading="eager"
                  >
              </div>
          </div>

          {{-- 下段：連続・総pt・月間 --}}
          <div class="relative z-10 mt-4 grid w-full grid-cols-3 overflow-hidden rounded-[12px] bg-white/16 text-white backdrop-blur-sm">
              <div class="flex min-w-0 items-center justify-center gap-1 border-r border-white/25 px-1 py-3">
                  <span class="shrink-0 text-[24px]">🔥</span>
                  <div class="min-w-0">
                      <p class="text-[11px] font-black leading-none">連続</p>
                      <p class="mt-1 text-[22px] font-black leading-none">
                          {{ $continuousDays ?? 0 }}<span class="text-[12px]">日</span>
                      </p>
                  </div>
              </div>

              <div class="flex min-w-0 items-center justify-center gap-1 border-r border-white/25 px-1 py-3">
                  <span class="shrink-0 text-[22px]">🏅</span>
                  <div class="min-w-0">
                      <p class="text-[11px] font-black leading-none">総pt</p>
                      <p class="mt-1 text-[20px] font-black leading-none">
                          {{ $myTotalPoints ?? 0 }}<span class="text-[11px]">pt</span>
                      </p>
                  </div>
              </div>

              <div class="flex min-w-0 items-center justify-center gap-1 px-1 py-3">
                  <span class="shrink-0 text-[22px]">🏅</span>
                  <div class="min-w-0">
                      <p class="text-[11px] font-black leading-none">月間</p>
                      <p class="mt-1 text-[22px] font-black leading-none">
                          {{ $monthlyRank ?? '-' }}<span class="text-[12px]">位</span>
                      </p>
                  </div>
              </div>
          </div>
      </section>

      {{-- 評価 --}}
      <section class="mb-3 overflow-hidden rounded-[16px] border border-[#DDE6F5] bg-white px-3 py-4 shadow-[0_8px_22px_rgba(15,43,95,0.06)]">
          <h2 class="mb-3 text-[20px] font-black text-[#071433]">
              評価
          </h2>

          <div class="space-y-3">
              @foreach ($scoreRows as $row)
                  @php
                      $score = $row['score'];
                      $width = $score !== null ? min(100, max(0, ($score / 25) * 100)) : 0;
                      $icon = $row['icon'] ?? '📘';
                  @endphp

                  <div class="grid w-full grid-cols-[26px_76px_minmax(0,1fr)_54px] items-center gap-2 border-b border-[#E8EDF6] pb-3 last:border-b-0 last:pb-0">
                      <span class="text-[22px] leading-none">
                          {{ $icon }}
                      </span>

                      <span class="min-w-0 truncate text-[15px] font-bold text-[#1B2540]">
                          {{ $row['label'] }}
                      </span>

                      <div class="h-3 min-w-0 overflow-hidden rounded-full bg-[#E8EDF6]">
                          <div class="h-full rounded-full bg-[#0D4FE8]" style="width: {{ $width }}%;"></div>
                      </div>

                      <span class="text-right text-[16px] font-black text-[#0D4FE8]">
                          {{ $score ?? '-' }}
                          <span class="text-[12px] text-[#46516B]">/25</span>
                      </span>
                  </div>
              @endforeach
          </div>
      </section>

      {{-- 良い点 --}}
      <section class="mb-3 rounded-[14px] border border-emerald-300 bg-emerald-50/80 px-3 py-3">
          <div class="flex gap-3">
              <div class="flex h-11 w-11 shrink-0 items-center justify-center rounded-full border border-emerald-200 bg-white text-[24px]">
                  👍
              </div>

              <div class="min-w-0 flex-1">
                  <h2 class="text-[19px] font-black text-emerald-700">
                      良い点
                  </h2>

                  <p class="mt-1 whitespace-pre-wrap break-words text-[14px] font-bold leading-7 text-[#1B2540]">{{ trim($training->good_point ?? '') ?: '未採点です。' }}
                  </p>
              </div>
          </div>
      </section>

      {{-- 改善点 --}}
      <section class="mb-3 rounded-[14px] border border-amber-300 bg-amber-50/90 px-3 py-3">
          <div class="flex gap-3">
              <div class="flex h-11 w-11 shrink-0 items-center justify-center rounded-full border border-amber-200 bg-white text-[24px]">
                  ↗️
              </div>

              <div class="min-w-0 flex-1">
                  <h2 class="text-[19px] font-black text-orange-600">
                      改善点
                  </h2>

                  <p class="mt-1 whitespace-pre-wrap break-words text-[14px] font-bold leading-7 text-[#1B2540]">{{ trim($training->improvement_point ?? '') ?: '未採点です。' }}
                  </p>
              </div>
          </div>
      </section>

      {{-- 次回の課題 --}}
      <section class="mb-3 rounded-[14px] border border-blue-300 bg-blue-50/80 px-3 py-3">
          <div class="flex gap-3">
              <div class="flex h-11 w-11 shrink-0 items-center justify-center rounded-full border border-blue-200 bg-white text-[24px]">
                  🎯
              </div>

              <div class="min-w-0 flex-1">
                  <h2 class="text-[19px] font-black text-[#0D4FE8]">
                      次回の課題
                  </h2>

                  <p class="mt-1 whitespace-pre-wrap break-words text-[14px] font-bold leading-7 text-[#1B2540]">{{ trim($training->next_task ?? '') ?: '未採点です。' }}
                  </p>
              </div>
          </div>
      </section>

      {{-- 入力内容 --}}
      <section class="mb-3 overflow-hidden rounded-[16px] border border-[#DDE6F5] bg-white px-3 py-4 shadow-[0_8px_22px_rgba(15,43,95,0.06)]">
          <h2 class="mb-3 text-[21px] font-black text-[#071433]">
              入力内容
          </h2>

          @if ($isAiQuestionTraining)
              <div class="space-y-3">
                  <div class="rounded-[12px] border border-[#DDE6F5] bg-white p-3">
                      <h3 class="mb-1 text-[14px] font-black text-[#0D4FE8]">
                          問題タイトル
                      </h3>
                      <p class="break-words text-[14px] font-bold leading-7 text-[#1B2540]">
                          {{ $questionTitle }}
                      </p>
                  </div>

                  <div class="rounded-[12px] border border-[#DDE6F5] bg-white p-3">
                      <h3 class="mb-1 text-[14px] font-black text-[#0D4FE8]">
                          問題本文
                      </h3>
                      <p class="whitespace-pre-wrap break-words text-[14px] font-bold leading-7 text-[#1B2540]">{{ trim($questionBody) }}
                      </p>
                  </div>

                  <div class="rounded-[12px] border border-[#DDE6F5] bg-white p-3">
                      <h3 class="mb-1 text-[14px] font-black text-[#0D4FE8]">
                          回答
                      </h3>
                      <p class="whitespace-pre-wrap break-words text-[14px] font-bold leading-7 text-[#1B2540]">{{ trim($answerBody) }}
                      </p>
                  </div>
              </div>
          @elseif ($type === 'diary')
              <div class="flex gap-3 rounded-[12px] border border-[#DDE6F5] bg-white p-3">
                  <div class="flex h-11 w-11 shrink-0 items-center justify-center rounded-lg bg-blue-50 text-[28px]">
                      📘
                  </div>

                  <p class="min-w-0 flex-1 whitespace-pre-wrap break-words text-[14px] font-bold leading-7 text-[#1B2540]">{{ trim($diaryBody) }}
                  </p>
              </div>
          @elseif ($type === 'challenge')
              <div class="space-y-3">
                  <div class="rounded-[12px] border border-[#DDE6F5] bg-white p-3">
                      <h3 class="mb-1 text-[14px] font-black text-[#0D4FE8]">
                          今日チャレンジしたこと
                      </h3>
                      <p class="whitespace-pre-wrap break-words text-[14px] font-bold leading-7 text-[#1B2540]">{{ trim($training->challenged_thing ?? '') }}
                      </p>
                  </div>

                  <div class="rounded-[12px] border border-[#DDE6F5] bg-white p-3">
                      <h3 class="mb-1 text-[14px] font-black text-[#0D4FE8]">
                          できたこと
                      </h3>
                      <p class="whitespace-pre-wrap break-words text-[14px] font-bold leading-7 text-[#1B2540]">{{ trim($training->completed_thing ?? '') }}
                      </p>
                  </div>

                  <div class="rounded-[12px] border border-[#DDE6F5] bg-white p-3">
                      <h3 class="mb-1 text-[14px] font-black text-[#0D4FE8]">
                          難しかったこと
                      </h3>
                      <p class="whitespace-pre-wrap break-words text-[14px] font-bold leading-7 text-[#1B2540]">{{ trim($training->difficult_thing ?? '') }}
                      </p>
                  </div>

                  <div class="rounded-[12px] border border-[#DDE6F5] bg-white p-3">
                      <h3 class="mb-1 text-[14px] font-black text-[#0D4FE8]">
                          次に改善したいこと
                      </h3>
                      <p class="whitespace-pre-wrap break-words text-[14px] font-bold leading-7 text-[#1B2540]">{{ trim($training->next_improvement ?? '') }}
                      </p>
                  </div>
              </div>
          @endif
      </section>

      {{-- 次におすすめ --}}
      <section class="mb-4 overflow-hidden rounded-[16px] border border-[#DDE6F5] bg-white px-3 py-4 shadow-[0_8px_22px_rgba(15,43,95,0.06)]">
          <h2 class="mb-3 flex items-center gap-2 text-[18px] font-black text-[#071433]">
              <span>✨</span>
              次におすすめ
          </h2>

          <div class="space-y-2">
              <a href="{{ route('trainings.index') }}"
                  class="flex min-w-0 items-center gap-2 rounded-[12px] border border-[#DDE6F5] bg-white px-3 py-2">
                  <span class="flex h-11 w-11 shrink-0 items-center justify-center rounded-full bg-orange-100 text-[26px]">
                      🔥
                  </span>

                  <span class="min-w-0 flex-1">
                      <span class="block truncate text-[15px] font-black text-[#071433]">
                          今日のチャレンジ
                      </span>
                      <span class="block truncate text-[12px] font-bold text-[#46516B]">
                          挑戦を振り返る
                      </span>
                  </span>

                  <span class="shrink-0 rounded-lg bg-[#0D4FE8] px-5 py-2 text-[15px] font-black text-white">
                      開始
                  </span>

                  <span class="shrink-0 text-[24px] text-[#64748B]">
                      ›
                  </span>
              </a>

              <a href="{{ route('trainings.index') }}"
                  class="flex min-w-0 items-center gap-2 rounded-[12px] border border-[#DDE6F5] bg-white px-3 py-2">
                  <span class="flex h-11 w-11 shrink-0 items-center justify-center rounded-full bg-purple-100 text-[26px]">
                      📖
                  </span>

                  <span class="min-w-0 flex-1">
                      <span class="block truncate text-[15px] font-black text-[#071433]">
                          要約力トレーニング
                      </span>
                      <span class="block truncate text-[12px] font-bold text-[#46516B]">
                          文章を短くまとめる力を鍛える
                      </span>
                  </span>

                  <span class="shrink-0 rounded-lg bg-[#0D4FE8] px-5 py-2 text-[15px] font-black text-white">
                      開始
                  </span>

                  <span class="shrink-0 text-[24px] text-[#64748B]">
                      ›
                  </span>
              </a>
          </div>
      </section>

      {{-- 下部ボタン --}}
      <div class="space-y-2">
          <a href="{{ route('trainings.index') }}"
              class="flex h-[50px] w-full items-center justify-center rounded-lg border-2 border-[#0D4FE8] bg-white text-[17px] font-black text-[#0D4FE8]">
              一覧に戻る
          </a>

          <a href="{{ route('trainings.ranking') }}"
              class="flex h-[50px] w-full items-center justify-center gap-2 rounded-lg bg-gradient-to-r from-amber-300 to-amber-500 text-[17px] font-black text-orange-700 shadow-sm">
              🏆 ランキングを見る
          </a>

          <a href="{{ route('trainings.index') }}"
              class="flex h-[54px] w-full items-center justify-center gap-3 rounded-lg bg-[#0D4FE8] text-[18px] font-black text-white shadow-[0_10px_18px_rgba(13,79,232,0.25)]">
              次のトレーニングへ
              <span class="text-[24px]">›</span>
          </a>
      </div>
  </div>
</div>
