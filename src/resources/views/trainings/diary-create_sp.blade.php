{{-- SP版：resources/views/trainings/diary-create_sp.blade.php --}}
<div class="block md:hidden min-h-screen w-full overflow-x-hidden bg-[#F8FAFF] text-[#071433]">
  <div class="mx-auto min-h-screen w-full max-w-[430px] overflow-x-hidden bg-[#F8FAFF] px-4 pb-28 pt-4">

      {{-- 上部ナビ --}}
      <div class="mb-6 grid grid-cols-[88px_1fr_60px] items-center gap-2">
          <a href="{{ route('trainings.index') }}"
              class="inline-flex min-w-0 items-center gap-1 truncate text-[16px] font-bold text-[#0D4FE8]">
              <span class="text-[28px] leading-none">‹</span>
              前の画面へ
          </a>

          <div class="flex items-center justify-center">
              <div class="flex items-center">
                  @for ($i = 1; $i <= 5; $i++)
                      <div class="flex items-center">
                          <div class="{{ $i <= 2 ? 'bg-[#0D4FE8] text-white' : 'bg-[#E4EAF3] text-[#334155]' }} flex h-9 w-9 items-center justify-center rounded-full text-[17px] font-black">
                              {{ $i }}
                          </div>

                          @if ($i < 5)
                              <div class="{{ $i < 2 ? 'bg-[#0D4FE8]' : 'bg-[#D8E0EC]' }} h-[3px] w-5"></div>
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

      {{-- ヒーロー --}}
      <section class="mb-6">
          <div class="mb-4 inline-flex rounded-full bg-[#EAF1FF] px-4 py-2 text-[16px] font-bold text-[#0D4FE8]">
              1日1回の振り返り
          </div>

          <div class="flex items-center gap-3">
              <div class="flex h-[58px] w-[58px] shrink-0 items-center justify-center rounded-[16px] bg-blue-50 text-[38px] shadow-[inset_0_0_0_1px_rgba(37,99,235,0.12)]">
                  📘
              </div>

              <div class="min-w-0 flex-1">
                  <h1 class="text-[28px] font-black leading-tight tracking-[0.01em] text-[#071433]">
                      日記トレーニング
                  </h1>
              </div>
          </div>

          <div class="mt-4 flex items-start justify-between gap-3">
              <p class="min-w-0 flex-1 text-[17px] font-bold leading-[1.9] text-[#334155]">
                  今日の出来事を短く書いて、<br>
                  文章力と振り返る力を育てます。
              </p>

              <img
                  src="{{ asset('images/training-top.png') }}"
                  alt="日記トレーニング"
                  class="mt-[-18px] h-[120px] w-[160px] shrink-0 object-contain"
                  loading="eager"
              >
          </div>
      </section>

      <form method="POST"
          action="{{ route('trainings.diary.store') }}"
          data-ai-loading="true"
          data-ai-loading-type="score">
          @csrf

          {{-- 日付 --}}
          <section class="mb-4 rounded-[18px] border border-[#DDE6F5] bg-white px-5 py-5 shadow-[0_8px_22px_rgba(15,43,95,0.06)]">
              <label class="mb-4 flex items-center gap-3 text-[21px] font-black text-[#071433]">
                  <span class="text-[25px]">🗓️</span>
                  日付
              </label>

              <div class="relative">
                  <input
                      type="date"
                      name="training_date"
                      value="{{ old('training_date', now()->format('Y-m-d')) }}"
                      class="h-[58px] w-full rounded-[12px] border border-[#CBD7EA] bg-white px-4 pr-12 text-[20px] font-bold text-[#071433] outline-none focus:border-[#0D4FE8] focus:ring-4 focus:ring-blue-100"
                  >
              </div>

              @error('training_date')
                  <p class="mt-3 rounded-xl bg-red-50 px-3 py-2 text-[14px] font-bold text-red-600">
                      {{ $message }}
                  </p>
              @enderror
          </section>

          {{-- 今日の日記 --}}
          <section class="mb-6 rounded-[18px] border border-[#DDE6F5] bg-white px-5 py-5 shadow-[0_8px_22px_rgba(15,43,95,0.06)]">
              <div class="mb-4 flex items-center gap-3">
                  <div class="flex h-11 w-11 shrink-0 items-center justify-center rounded-[10px] bg-[#0D4FE8] text-[25px] text-white shadow-[0_8px_16px_rgba(13,79,232,0.22)]">
                      📝
                  </div>

                  <div>
                      <h2 class="text-[22px] font-black text-[#071433]">
                          今日の日記
                      </h2>
                  </div>
              </div>

              <textarea
                  name="diary_body"
                  rows="9"
                  maxlength="500"
                  data-diary-textarea
                  data-count-target="spDiaryCount"
                  class="min-h-[300px] w-full resize-none rounded-[14px] border border-[#CBD7EA] bg-white px-4 py-4 text-[17px] font-bold leading-[1.9] text-[#071433] outline-none placeholder:text-[#64748B] focus:border-[#0D4FE8] focus:ring-4 focus:ring-blue-100"
                  placeholder="例：今日は募集ページの改善を進めました。&#10;最初は大変でしたが、作業を小さく分けたことで&#10;進めやすくなりました。明日は理由や学びも&#10;意識して書きたいです。"
              >{{ old('diary_body') }}</textarea>

              <div class="mt-3 text-right text-[16px] font-bold text-[#64748B]">
                  <span id="spDiaryCount">0</span> / 500 文字
              </div>

              @error('diary_body')
                  <p class="mt-3 rounded-xl bg-red-50 px-3 py-2 text-[14px] font-bold text-red-600">
                      {{ $message }}
                  </p>
              @enderror
          </section>

          {{-- 下部ボタン --}}
          <div class="grid grid-cols-2 gap-3">
              <a href="{{ route('trainings.index') }}"
                  class="flex h-[62px] items-center justify-center gap-2 rounded-[14px] border border-[#CBD7EA] bg-white text-[22px] font-black text-[#1B2540] shadow-[0_8px_20px_rgba(15,43,95,0.06)]">
                  <span class="text-[26px]">←</span>
                  戻る
              </a>

              <button type="submit"
                  class="flex h-[62px] items-center justify-center gap-2 rounded-[14px] bg-[#0D4FE8] text-[22px] font-black text-white shadow-[0_12px_22px_rgba(13,79,232,0.28)] active:scale-[0.99]">
                  <span>✨</span>
                  AI採点する
              </button>
          </div>
      </form>
  </div>
</div>
