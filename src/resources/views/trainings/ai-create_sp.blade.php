{{-- SP版：resources/views/trainings/ai-create_sp.blade.php --}}
<div class="block md:hidden min-h-screen w-full overflow-x-hidden bg-[#F8FAFF] text-[#071433]">
  <div class="mx-auto min-h-screen w-full max-w-[430px] overflow-x-hidden bg-[#F8FAFF] px-4 pb-28 pt-4">

      {{-- 上部ナビ --}}
      <div class="mb-8 grid grid-cols-[88px_1fr_60px] items-center gap-2">
          <a href="{{ route('trainings.index') }}"
              class="inline-flex min-w-0 items-center gap-1 truncate text-[16px] font-bold text-[#0D4FE8]">
              <span class="text-[28px] leading-none">‹</span>
              前の画面へ
          </a>

          <div class="flex items-center justify-center">
              <div class="flex items-center">
                  @for ($i = 1; $i <= 5; $i++)
                      <div class="flex items-center">
                          <div class="{{ $i <= 3 ? 'bg-[#0D4FE8] text-white' : 'bg-[#E4EAF3] text-[#334155]' }} flex h-9 w-9 items-center justify-center rounded-full text-[17px] font-black">
                              {{ $i }}
                          </div>

                          @if ($i < 5)
                              <div class="{{ $i < 3 ? 'bg-[#0D4FE8]' : 'bg-[#D8E0EC]' }} h-[3px] w-5"></div>
                          @endif
                      </div>
                  @endfor
              </div>
          </div>

          <a href="{{ route('trainings.index') }}"
              class="min-w-0 truncate text-right text-[16px] font-bold text-[#0D4FE8]">
              閉じる
          </a>
      </div>

      @if (session('error'))
          <div class="mb-4 rounded-2xl border border-red-200 bg-red-50 px-4 py-3 text-[14px] font-bold text-red-700">
              {{ session('error') }}
          </div>
      @endif

      {{-- タイトル --}}
      <section class="mb-8 text-center">
          <div class="mb-5 inline-flex rounded-full border border-[#0D4FE8] bg-white px-5 py-2 text-[18px] font-black text-[#0D4FE8]">
              問題
          </div>

          <h1 class="text-[32px] font-black leading-tight tracking-[0.02em] text-[#071433]">
              {{ $typeLabel }}
          </h1>

          <p class="mt-4 text-[17px] font-bold leading-relaxed text-[#334155]">
              問題を見ながら、あなたの考えを入力してください。
          </p>
      </section>

      {{-- 問題 --}}
      <section class="mb-5 rounded-[18px] border border-[#DDE6F5] bg-white px-5 py-5 shadow-[0_8px_22px_rgba(15,43,95,0.06)]">
          <h2 class="mb-5 flex items-center gap-3 text-[24px] font-black text-[#071433]">
              <span class="flex h-11 w-11 shrink-0 items-center justify-center rounded-[10px] bg-[#0D4FE8] text-[24px] text-white">
                  📄
              </span>
              本日の問題
          </h2>

          <div class="max-h-[260px] overflow-y-auto whitespace-pre-wrap rounded-[14px] border border-[#CBD7EA] bg-[#F9FBFF] px-4 py-4 text-[18px] font-bold leading-[2] text-[#1B2540]">{{ $questionBody }}</div>

          <div class="max-h-[260px] overflow-y-auto whitespace-pre-wrap rounded-[14px] border border-[#CBD7EA] bg-[#F9FBFF] px-4 py-4 text-[18px] font-bold leading-[2] text-[#1B2540]">
              {{ $questionBody }}
          </div>

          <p class="mt-4 text-[15px] font-bold leading-relaxed text-[#334155]">
              ※ 問題文が長い場合は、この枠内で確認できます。
          </p>
      </section>

      <form method="POST"
          action="{{ $storeRoute }}"
          data-ai-loading="true"
          data-ai-loading-type="score">
          @csrf

          {{-- 回答 --}}
          <section class="mb-5 rounded-[18px] border border-[#DDE6F5] bg-white px-5 py-5 shadow-[0_8px_22px_rgba(15,43,95,0.06)]">
              <div class="mb-4 flex items-center gap-3">
                  <span class="flex h-11 w-11 shrink-0 items-center justify-center rounded-[10px] bg-[#0D4FE8] text-[24px] text-white">
                      ✎
                  </span>

                  <h2 class="text-[24px] font-black text-[#071433]">
                      あなたの回答
                  </h2>

                  <span class="text-[16px] font-bold text-[#3B82F6]">
                      短くても大丈夫です
                  </span>
              </div>

              <textarea
                  name="answer_body"
                  rows="5"
                  maxlength="{{ $answerMaxLength }}"
                  data-ai-answer-textarea
                  data-count-target="spAiAnswerCount"
                  class="min-h-[150px] w-full resize-y rounded-[14px] border border-[#CBD7EA] bg-white px-4 py-4 text-[18px] font-bold leading-[1.9] text-[#071433] outline-none placeholder:text-[#7B879E] focus:border-[#0D4FE8] focus:ring-4 focus:ring-blue-100"
                  placeholder="ここに回答を入力してください。"
              >{{ old('answer_body', $training->answer_body) }}</textarea>

              <div class="mt-3 text-right text-[16px] font-bold text-[#64748B]">
                  <span id="spAiAnswerCount">0</span> / {{ $answerMaxLength }}文字
              </div>

              @error('answer_body')
                  <p class="mt-3 rounded-xl bg-red-50 px-3 py-2 text-[14px] font-bold text-red-600">
                      {{ $message }}
                  </p>
              @enderror
          </section>

          {{-- 採点項目 --}}
          <section class="mb-6 rounded-[18px] border border-[#DDE6F5] bg-white px-5 py-5 shadow-[0_8px_22px_rgba(15,43,95,0.06)]">
              <div class="mb-5 flex items-center gap-3">
                  <span class="text-[38px] leading-none text-[#0D4FE8]">★</span>

                  <h2 class="text-[25px] font-black text-[#071433]">
                      採点項目
                  </h2>

                  <span class="rounded-full border border-[#8DB3FF] bg-white px-4 py-1 text-[16px] font-black text-[#0D4FE8]">
                      各25点
                  </span>
              </div>

              <div class="grid grid-cols-2 gap-3">
                  @foreach ($scoreLabels as $label)
                      <div class="flex h-[54px] items-center justify-center rounded-full border border-[#8DB3FF] bg-white text-[18px] font-black text-[#0D4FE8]">
                          {{ $label }}
                      </div>
                  @endforeach
              </div>
          </section>

          {{-- 下部ボタン --}}
          <div class="grid grid-cols-2 gap-3">
              <a href="{{ route('trainings.index') }}"
                  class="flex h-[62px] items-center justify-center gap-2 rounded-[14px] border-2 border-[#0D4FE8] bg-white text-[22px] font-black text-[#071433] shadow-[0_8px_20px_rgba(15,43,95,0.06)]">
                  <span class="text-[26px]">←</span>
                  戻る
              </a>

              <button type="submit"
                  class="flex h-[62px] items-center justify-center rounded-[14px] bg-[#0D4FE8] text-[22px] font-black text-white shadow-[0_12px_22px_rgba(13,79,232,0.28)] active:scale-[0.99]">
                  投稿する
              </button>
          </div>
      </form>
  </div>
</div>
