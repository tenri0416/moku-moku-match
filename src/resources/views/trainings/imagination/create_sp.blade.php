{{-- SP版：resources/views/trainings/imagination/create_sp.blade.php --}}
<div class="block md:hidden min-h-screen w-full overflow-x-hidden bg-[#F8FAFF] text-[#071433]">
  <div class="mx-auto min-h-screen w-full max-w-[430px] overflow-x-hidden bg-[#F8FAFF] px-3 pb-28 pt-4">

      <div class="mb-4 grid grid-cols-[92px_1fr_56px] items-center gap-2">
          <a href="{{ route('trainings.index') }}" class="inline-flex min-w-0 items-center gap-1 truncate text-[15px] font-bold text-[#0D4FE8]">
              <span class="text-[26px] leading-none">‹</span>
              戻る
          </a>

          <div class="flex items-center justify-center">
              <div class="rounded-full border border-[#8DB3FF] bg-white px-4 py-1.5 text-[13px] font-black text-[#0D4FE8]">
                  1日1回
              </div>
          </div>

          <a href="{{ route('trainings.index') }}" class="min-w-0 truncate text-right text-[15px] font-bold text-[#0D4FE8]">
              閉じる
          </a>
      </div>

      @if ($errors->any())
          <div class="mb-3 rounded-2xl border border-red-200 bg-red-50 px-4 py-3 text-[13px] font-bold text-red-700">
              @foreach ($errors->all() as $error)
                  <p>{{ $error }}</p>
              @endforeach
          </div>
      @endif

      <section class="mb-3 rounded-[18px] border border-[#DDE6F5] bg-white px-4 py-3 shadow-[0_8px_22px_rgba(15,43,95,0.06)]">
          <div class="flex items-center justify-between gap-3">
              <div>
                  <p class="text-[12px] font-black text-[#0D4FE8]">
                      {{ $typeLabel }}
                  </p>
                  <h1 class="mt-1 text-[22px] font-black leading-tight text-[#071433]">
                      今日の場面を想像する
                  </h1>
              </div>

              <span class="shrink-0 rounded-full border border-[#8DB3FF] bg-[#F0F7FF] px-3 py-1 text-[12px] font-black text-[#0D4FE8]">
                  {{ $training->difficulty_label }}
              </span>
          </div>
      </section>

      <form method="POST" action="{{ $storeRoute }}" data-ai-loading="true" data-ai-loading-type="score">
          @csrf

          <input type="hidden" name="training_id" value="{{ $training->id }}">

          <section class="mb-3 rounded-[18px] border border-[#DDE6F5] bg-white px-4 py-4 shadow-[0_8px_22px_rgba(15,43,95,0.06)]">
              <div class="mb-3 flex items-center gap-2">
                  <span class="flex h-9 w-9 shrink-0 items-center justify-center rounded-[9px] bg-[#0D4FE8] text-[20px] text-white">
                      🌈
                  </span>

                  <h2 class="text-[20px] font-black text-[#071433]">
                      本日の問題
                  </h2>
              </div>

              <div class="mb-3 rounded-full bg-[#F0F7FF] px-3 py-1.5 text-center text-[12px] font-black text-[#0D4FE8]">
                  {{ $training->question_type }}
              </div>

              <div class="rounded-[12px] border border-[#CBD7EA] bg-[#F9FBFF] px-3 py-3 text-[14px] font-bold leading-[1.7] text-[#1B2540]">
                  {{ $questionBody }}
              </div>
          </section>

          <section class="mb-3 rounded-[18px] border border-[#DDE6F5] bg-white px-4 py-4 shadow-[0_8px_22px_rgba(15,43,95,0.06)]">
              <div class="mb-3 flex items-center gap-2">
                  <span class="flex h-9 w-9 shrink-0 items-center justify-center rounded-[9px] bg-[#0D4FE8] text-[20px] text-white">
                      ✎
                  </span>

                  <h2 class="text-[20px] font-black text-[#071433]">
                      あなたの回答
                  </h2>

                  <span class="text-[13px] font-bold text-[#3B82F6]">
                      短文OK
                  </span>
              </div>

              <textarea
                  name="answer_body"
                  rows="6"
                  maxlength="{{ $answerMaxLength }}"
                  data-ai-answer-textarea
                  data-count-target="spImaginationAnswerCount"
                  class="min-h-[140px] w-full resize-y rounded-[14px] border border-[#CBD7EA] bg-white px-3 py-3 text-[16px] font-bold leading-[1.7] text-[#071433] outline-none placeholder:text-[#7B879E] focus:border-[#0D4FE8] focus:ring-4 focus:ring-blue-100"
                  placeholder="例：私は、〇〇という状況だと想像しました。理由は△△だからです。"
              >{{ old('answer_body', $training->answer_body) }}</textarea>

              <div class="mt-2 flex items-center justify-between gap-3">
                  <p class="text-[12px] font-bold text-[#64748B]">
                      1〜5文でOK
                  </p>

                  <p class="text-[13px] font-bold text-[#64748B]">
                      <span id="spImaginationAnswerCount">0</span> / {{ $answerMaxLength }}文字
                  </p>
              </div>
          </section>

          <details class="mb-3 rounded-[18px] border border-[#DDE6F5] bg-white shadow-[0_8px_22px_rgba(15,43,95,0.06)]">
              <summary class="flex cursor-pointer list-none items-center justify-between px-4 py-4">
                  <span class="flex items-center gap-2 text-[16px] font-black text-[#071433]">
                      <span class="text-[#0D4FE8]">💡</span>
                      ヒントを見る
                  </span>
                  <span class="text-[13px] font-black text-[#0D4FE8]">開く</span>
              </summary>

              <div class="border-t border-[#E5EDF8] px-4 py-4">
                  <p class="rounded-[14px] border border-[#BFD6FF] bg-[#F0F7FF] px-3 py-3 text-[13px] font-bold leading-6 text-[#334155]">
                      {{ $training->answer_point }}
                  </p>

                  <ul class="mt-3 space-y-2 text-[13px] font-bold leading-6 text-[#334155]">
                      @foreach ($tips as $tip)
                          <li class="flex items-start gap-2">
                              <span class="mt-0.5 text-emerald-500">●</span>
                              <span>{{ $tip }}</span>
                          </li>
                      @endforeach
                  </ul>
              </div>
          </details>

          <button type="submit"
              class="flex h-[58px] w-full items-center justify-center rounded-[14px] bg-[#0D4FE8] text-[18px] font-black text-white shadow-[0_12px_22px_rgba(13,79,232,0.28)] active:scale-[0.99]">
              採点する
          </button>
      </form>
  </div>
</div>
