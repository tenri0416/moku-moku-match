{{-- SP版：resources/views/trainings/challenge-create_sp.blade.php --}}
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

      {{-- ヒーロー --}}
      <section class="mb-6">
          <div class="mb-4 inline-flex rounded-full bg-orange-50 px-4 py-2 text-[16px] font-bold text-orange-600">
              今日の振り返り
          </div>

          <div class="flex items-center gap-3">
              <div class="shrink-0 text-[46px] leading-none">
                  🔥
              </div>

              <div class="min-w-0 flex-1">
                  <h1 class="text-[30px] font-black leading-tight tracking-[0.01em] text-[#071433]">
                      今日のチャレンジ
                  </h1>
              </div>
          </div>

          <div class="mt-4 flex items-start justify-between gap-3">
              <p class="min-w-0 flex-1 text-[16px] font-bold leading-[1.9] text-[#334155]">
                  今日の挑戦を短く整理して、明日の行動につなげましょう。
              </p>

              {{-- SP用ヒーロー画像：横長画像を小さく表示 --}}
              <img
                  src="{{ asset('images/training-top.png') }}"
                  alt="今日のチャレンジ"
                  class="-mt-7 h-[108px] w-[168px] shrink-0 object-contain"
                  loading="eager"
              >
          </div>
      </section>

      <form method="POST"
          action="{{ route('trainings.challenge.store') }}"
          data-ai-loading="true"
          data-ai-loading-type="score">
          @csrf

          {{-- 日付 --}}
          <section class="mb-4 rounded-[18px] border border-[#DDE6F5] bg-white px-5 py-5 shadow-[0_8px_22px_rgba(15,43,95,0.06)]">
              <label class="mb-4 flex items-center gap-3 text-[21px] font-black text-[#071433]">
                  <span class="text-[25px]">🗓️</span>
                  日付
              </label>

              <input
                  type="date"
                  name="training_date"
                  value="{{ old('training_date', now()->format('Y-m-d')) }}"
                  class="h-[58px] w-full rounded-[12px] border border-[#CBD7EA] bg-white px-4 pr-12 text-[20px] font-bold text-[#071433] outline-none focus:border-[#0D4FE8] focus:ring-4 focus:ring-blue-100"
              >

              @error('training_date')
                  <p class="mt-3 rounded-xl bg-red-50 px-3 py-2 text-[14px] font-bold text-red-600">
                      {{ $message }}
                  </p>
              @enderror
          </section>

          {{-- 1. 今日チャレンジしたこと --}}
          <section class="mb-4 rounded-[18px] border border-[#DDE6F5] bg-white px-5 py-5 shadow-[0_8px_22px_rgba(15,43,95,0.06)]">
              <div class="mb-4 flex items-center gap-3">
                  <span class="flex h-11 w-11 shrink-0 items-center justify-center rounded-full bg-orange-100 text-[22px] font-black text-orange-600">
                      1
                  </span>

                  <h2 class="min-w-0 text-[21px] font-black text-[#071433]">
                      1. 今日チャレンジしたこと
                  </h2>
              </div>

              <textarea
                  name="challenged_thing"
                  rows="3"
                  maxlength="200"
                  data-training-textarea
                  data-count-target="spChallengedThingCount"
                  class="min-h-[78px] w-full resize-y rounded-[12px] border border-[#CBD7EA] bg-white px-4 py-4 text-[17px] font-bold leading-[1.8] text-[#071433] outline-none placeholder:text-[#7B879E] focus:border-[#0D4FE8] focus:ring-4 focus:ring-blue-100"
                  placeholder="例：今日は〇〇に挑戦しました。"
              >{{ old('challenged_thing') }}</textarea>

              <div class="mt-2 text-right text-[16px] font-bold text-[#64748B]">
                  <span id="spChallengedThingCount">0</span> / 200文字
              </div>

              @error('challenged_thing')
                  <p class="mt-3 rounded-xl bg-red-50 px-3 py-2 text-[14px] font-bold text-red-600">
                      {{ $message }}
                  </p>
              @enderror
          </section>

          {{-- 2. できたこと --}}
          <section class="mb-4 rounded-[18px] border border-[#DDE6F5] bg-white px-5 py-5 shadow-[0_8px_22px_rgba(15,43,95,0.06)]">
              <div class="mb-4 flex items-center gap-3">
                  <span class="flex h-11 w-11 shrink-0 items-center justify-center rounded-full bg-emerald-100 text-[22px] font-black text-emerald-600">
                      2
                  </span>

                  <h2 class="text-[21px] font-black text-[#071433]">
                      2. できたこと
                  </h2>
              </div>

              <textarea
                  name="completed_thing"
                  rows="3"
                  maxlength="200"
                  data-training-textarea
                  data-count-target="spCompletedThingCount"
                  class="min-h-[78px] w-full resize-y rounded-[12px] border border-[#CBD7EA] bg-white px-4 py-4 text-[17px] font-bold leading-[1.8] text-[#071433] outline-none placeholder:text-[#7B879E] focus:border-[#0D4FE8] focus:ring-4 focus:ring-blue-100"
                  placeholder="例：△△ができました。"
              >{{ old('completed_thing') }}</textarea>

              <div class="mt-2 text-right text-[16px] font-bold text-[#64748B]">
                  <span id="spCompletedThingCount">0</span> / 200文字
              </div>

              @error('completed_thing')
                  <p class="mt-3 rounded-xl bg-red-50 px-3 py-2 text-[14px] font-bold text-red-600">
                      {{ $message }}
                  </p>
              @enderror
          </section>

          {{-- 3. 難しかったこと --}}
          <section class="mb-4 rounded-[18px] border border-[#DDE6F5] bg-white px-5 py-5 shadow-[0_8px_22px_rgba(15,43,95,0.06)]">
              <div class="mb-4 flex items-center gap-3">
                  <span class="flex h-11 w-11 shrink-0 items-center justify-center rounded-full bg-violet-100 text-[22px] font-black text-violet-600">
                      3
                  </span>

                  <h2 class="text-[21px] font-black text-[#071433]">
                      3. 難しかったこと
                  </h2>
              </div>

              <textarea
                  name="difficult_thing"
                  rows="3"
                  maxlength="200"
                  data-training-textarea
                  data-count-target="spDifficultThingCount"
                  class="min-h-[78px] w-full resize-y rounded-[12px] border border-[#CBD7EA] bg-white px-4 py-4 text-[17px] font-bold leading-[1.8] text-[#071433] outline-none placeholder:text-[#7B879E] focus:border-[#0D4FE8] focus:ring-4 focus:ring-blue-100"
                  placeholder="例：□□が難しかったです。"
              >{{ old('difficult_thing') }}</textarea>

              <div class="mt-2 text-right text-[16px] font-bold text-[#64748B]">
                  <span id="spDifficultThingCount">0</span> / 200文字
              </div>

              @error('difficult_thing')
                  <p class="mt-3 rounded-xl bg-red-50 px-3 py-2 text-[14px] font-bold text-red-600">
                      {{ $message }}
                  </p>
              @enderror
          </section>

          {{-- 4. 次に改善したいこと --}}
          <section class="mb-6 rounded-[18px] border border-[#DDE6F5] bg-white px-5 py-5 shadow-[0_8px_22px_rgba(15,43,95,0.06)]">
              <div class="mb-4 flex items-center gap-3">
                  <span class="flex h-11 w-11 shrink-0 items-center justify-center rounded-full bg-orange-100 text-[22px] font-black text-orange-600">
                      4
                  </span>

                  <h2 class="min-w-0 text-[21px] font-black text-[#071433]">
                      4. 次に改善したいこと
                  </h2>
              </div>

              <textarea
                  name="next_improvement"
                  rows="3"
                  maxlength="200"
                  data-training-textarea
                  data-count-target="spNextImprovementCount"
                  class="min-h-[78px] w-full resize-y rounded-[12px] border border-[#CBD7EA] bg-white px-4 py-4 text-[17px] font-bold leading-[1.8] text-[#071433] outline-none placeholder:text-[#7B879E] focus:border-[#0D4FE8] focus:ring-4 focus:ring-blue-100"
                  placeholder="例：次は××を意識して取り組みたいです。"
              >{{ old('next_improvement') }}</textarea>

              <div class="mt-2 text-right text-[16px] font-bold text-[#64748B]">
                  <span id="spNextImprovementCount">0</span> / 200文字
              </div>

              @error('next_improvement')
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
