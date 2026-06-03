{{-- PC版：resources/views/trainings/diary-create_pc.blade.php --}}
<div class="hidden md:block min-h-screen bg-[#F8FAFF] text-[#071433]">
  <div class="mx-auto w-full max-w-[1440px] px-8 py-10">

      @if (session('error'))
          <div class="mb-6 rounded-2xl border border-red-200 bg-red-50 px-5 py-4 text-[15px] font-bold text-red-700">
              {{ session('error') }}
          </div>
      @endif

      <form method="POST"
          action="{{ route('trainings.diary.store') }}"
          data-ai-loading="true"
          data-ai-loading-type="score">
          @csrf

          <div class="grid grid-cols-[1fr_400px] gap-8">

              {{-- 左側メイン --}}
              <main>
                  {{-- ヒーロー --}}
                  <section class="mb-6">
                      <div class="grid grid-cols-[1fr_430px] items-center gap-6">
                          <div>
                              <div class="mb-5 inline-flex rounded-full bg-[#EAF1FF] px-4 py-2 text-[16px] font-bold text-[#0D4FE8]">
                                  1日1回の振り返り
                              </div>

                              <div class="flex items-center gap-4">
                                  <div class="flex h-[70px] w-[70px] shrink-0 items-center justify-center rounded-[18px] bg-blue-50 text-[46px] shadow-[inset_0_0_0_1px_rgba(37,99,235,0.12)]">
                                      📘
                                  </div>

                                  <h1 class="text-[52px] font-black leading-tight tracking-[0.01em] text-[#071433]">
                                      日記トレーニング
                                  </h1>
                              </div>

                              <p class="mt-5 text-[18px] font-bold leading-relaxed text-[#334155]">
                                  今日の出来事を短く書いて、文章力と振り返る力を育てます。
                              </p>

                              <div class="mt-7 flex items-center gap-3 text-[18px] font-bold text-[#334155]">
                                  <span class="text-[24px]">🗓️</span>
                                  <span>{{ old('training_date', now()->format('Y-m-d')) }}</span>
                              </div>
                          </div>

                          <div class="flex justify-center">
                              <img
                                  src="{{ asset('images/training-top.png') }}"
                                  alt="日記トレーニング"
                                  class="h-[220px] w-full max-w-[430px] object-contain"
                                  loading="eager"
                              >
                          </div>
                      </div>
                  </section>

                  {{-- 日付・書き方の流れ --}}
                  <section class="mb-6 grid grid-cols-[380px_1fr] gap-6">
                      <div class="rounded-[18px] border border-[#DDE6F5] bg-white px-6 py-6 shadow-[0_8px_22px_rgba(15,43,95,0.06)]">
                          <label class="mb-5 flex items-center gap-3 text-[22px] font-black text-[#071433]">
                              <span class="text-[25px]">🗓️</span>
                              日付
                          </label>

                          <input
                              type="date"
                              name="training_date"
                              value="{{ old('training_date', now()->format('Y-m-d')) }}"
                              class="h-[58px] w-full rounded-[12px] border border-[#CBD7EA] bg-white px-4 text-[18px] font-bold text-[#071433] outline-none focus:border-[#0D4FE8] focus:ring-4 focus:ring-blue-100"
                          >

                          @error('training_date')
                              <p class="mt-3 rounded-xl bg-red-50 px-3 py-2 text-[14px] font-bold text-red-600">
                                  {{ $message }}
                              </p>
                          @enderror
                      </div>

                      <div class="rounded-[18px] border border-[#DDE6F5] bg-white px-6 py-6 shadow-[0_8px_22px_rgba(15,43,95,0.06)]">
                          <h2 class="mb-5 flex items-center gap-3 text-[22px] font-black text-[#071433]">
                              <span class="text-[25px] text-[#0D4FE8]">📖</span>
                              書き方の流れ
                          </h2>

                          <div class="grid grid-cols-4 gap-4">
                              <div class="flex items-center gap-3 rounded-[12px] bg-[#EAF1FF] px-4 py-3">
                                  <span class="flex h-9 w-9 items-center justify-center rounded-full bg-[#CFE0FF] text-[17px] font-black text-[#0D4FE8]">
                                      1
                                  </span>
                                  <span class="text-[17px] font-black text-[#0D4FE8]">出来事</span>
                              </div>

                              <div class="flex items-center gap-3 rounded-[12px] bg-emerald-50 px-4 py-3">
                                  <span class="flex h-9 w-9 items-center justify-center rounded-full bg-emerald-100 text-[17px] font-black text-emerald-700">
                                      2
                                  </span>
                                  <span class="text-[17px] font-black text-emerald-700">感情</span>
                              </div>

                              <div class="flex items-center gap-3 rounded-[12px] bg-violet-50 px-4 py-3">
                                  <span class="flex h-9 w-9 items-center justify-center rounded-full bg-violet-100 text-[17px] font-black text-violet-700">
                                      3
                                  </span>
                                  <span class="text-[17px] font-black text-violet-700">理由</span>
                              </div>

                              <div class="flex items-center gap-3 rounded-[12px] bg-amber-50 px-4 py-3">
                                  <span class="flex h-9 w-9 items-center justify-center rounded-full bg-amber-100 text-[17px] font-black text-amber-700">
                                      4
                                  </span>
                                  <span class="text-[17px] font-black text-amber-700">学び</span>
                              </div>
                          </div>
                      </div>
                  </section>

                  {{-- 今日の日記 --}}
                  <section class="rounded-[18px] border border-[#DDE6F5] bg-white px-6 py-6 shadow-[0_8px_22px_rgba(15,43,95,0.06)]">
                      <div class="mb-5 flex items-center gap-4">
                          <div class="flex h-12 w-12 shrink-0 items-center justify-center rounded-[10px] bg-[#0D4FE8] text-[28px] text-white shadow-[0_8px_16px_rgba(13,79,232,0.22)]">
                              📝
                          </div>

                          <div>
                              <h2 class="text-[26px] font-black text-[#071433]">
                                  今日の日記
                              </h2>

                              <p class="mt-1 text-[15px] font-bold text-[#64748B]">
                                  短くても大丈夫です
                              </p>
                          </div>
                      </div>

                      <textarea
                          name="diary_body"
                          rows="12"
                          maxlength="500"
                          data-diary-textarea
                          data-count-target="pcDiaryCount"
                          class="min-h-[270px] w-full resize-none rounded-[14px] border border-[#CBD7EA] bg-white px-5 py-5 text-[17px] font-bold leading-[1.9] text-[#071433] outline-none placeholder:text-[#64748B] focus:border-[#0D4FE8] focus:ring-4 focus:ring-blue-100"
                          placeholder="例：今日は募集ページの改善を進めました。最初は大変でしたが、&#10;作業を小さく分けたことで進めやすくなりました。&#10;明日は理由や学びも意識して書きたいです。"
                      >{{ old('diary_body') }}</textarea>

                      <div class="mt-3 text-right text-[15px] font-bold text-[#64748B]">
                          <span id="pcDiaryCount">0</span> / 500文字
                      </div>

                      @error('diary_body')
                          <p class="mt-3 rounded-xl bg-red-50 px-3 py-2 text-[14px] font-bold text-red-600">
                              {{ $message }}
                          </p>
                      @enderror
                  </section>

                  {{-- 下部ボタン --}}
                  <div class="mt-7 grid grid-cols-[390px_1fr] gap-6">
                      <a href="{{ route('trainings.index') }}"
                          class="flex h-[72px] items-center justify-center gap-3 rounded-[16px] border border-[#CBD7EA] bg-white text-[24px] font-black text-[#1B2540] shadow-[0_8px_20px_rgba(15,43,95,0.06)]">
                          <span class="text-[30px]">←</span>
                          戻る
                      </a>

                      <button type="submit"
                          class="flex h-[72px] items-center justify-center gap-4 rounded-[16px] bg-[#0D4FE8] text-[25px] font-black text-white shadow-[0_12px_22px_rgba(13,79,232,0.28)] active:scale-[0.99]">
                          <span>✨</span>
                          AI採点する
                          <span class="text-[30px]">›</span>
                      </button>
                  </div>
              </main>

              {{-- 右側サイド --}}
              <aside class="space-y-6">
                  {{-- AI採点でわかること --}}
                  <section class="rounded-[18px] border border-[#DDE6F5] bg-white px-6 py-6 shadow-[0_8px_22px_rgba(15,43,95,0.06)]">
                      <h2 class="mb-6 flex items-center gap-3 text-[22px] font-black text-[#071433]">
                          <span class="text-[#0D4FE8]">✨</span>
                          AI採点でわかること
                      </h2>

                      <div class="space-y-5">
                          <div class="flex gap-4">
                              <div class="flex h-14 w-14 shrink-0 items-center justify-center rounded-full bg-amber-100 text-[28px]">
                                  ⭐
                              </div>
                              <div>
                                  <h3 class="text-[18px] font-black text-[#071433]">総合点</h3>
                                  <p class="mt-1 text-[14px] font-bold leading-relaxed text-[#334155]">
                                      あなたの総合的な評価がわかります
                                  </p>
                              </div>
                          </div>

                          <div class="flex gap-4">
                              <div class="flex h-14 w-14 shrink-0 items-center justify-center rounded-full bg-emerald-100 text-[28px]">
                                  👍
                              </div>
                              <div>
                                  <h3 class="text-[18px] font-black text-[#071433]">良い点</h3>
                                  <p class="mt-1 text-[14px] font-bold leading-relaxed text-[#334155]">
                                      文章の中の良かったポイントを紹介
                                  </p>
                              </div>
                          </div>

                          <div class="flex gap-4">
                              <div class="flex h-14 w-14 shrink-0 items-center justify-center rounded-full bg-orange-100 text-[28px]">
                                  💡
                              </div>
                              <div>
                                  <h3 class="text-[18px] font-black text-[#071433]">改善点</h3>
                                  <p class="mt-1 text-[14px] font-bold leading-relaxed text-[#334155]">
                                      より良くするためのアドバイスを提示
                                  </p>
                              </div>
                          </div>

                          <div class="flex gap-4">
                              <div class="flex h-14 w-14 shrink-0 items-center justify-center rounded-full bg-blue-100 text-[28px]">
                                  🎯
                              </div>
                              <div>
                                  <h3 class="text-[18px] font-black text-[#071433]">次回の課題</h3>
                                  <p class="mt-1 text-[14px] font-bold leading-relaxed text-[#334155]">
                                      次に意識すると良いポイントを提案
                                  </p>
                              </div>
                          </div>
                      </div>
                  </section>

                  {{-- 今日の状況 --}}
                  <section class="rounded-[18px] border border-[#DDE6F5] bg-white px-6 py-6 shadow-[0_8px_22px_rgba(15,43,95,0.06)]">
                      <h2 class="mb-5 flex items-center gap-3 text-[22px] font-black text-[#071433]">
                          <span class="text-[#0D4FE8]">📊</span>
                          今日の状況
                      </h2>

                      <div class="grid grid-cols-3 gap-3">
                          <div class="rounded-[12px] border border-[#DDE6F5] bg-white px-3 py-4 text-center">
                              <div class="text-[32px]">🔥</div>
                              <p class="mt-2 text-[14px] font-black text-[#334155]">連続</p>
                              <p class="mt-1 text-[28px] font-black leading-none text-[#071433]">
                                  {{ $continuousDays}}<span class="text-[14px]">日</span>
                              </p>
                          </div>

                          <div class="rounded-[12px] border border-[#DDE6F5] bg-white px-3 py-4 text-center">
                              <div class="text-[32px]">🏅</div>
                              <p class="mt-2 text-[14px] font-black text-[#334155]">総pt</p>
                              <p class="mt-1 text-[25px] font-black leading-none text-[#071433]">
                                  {{ $myTotalPoints }}<span class="text-[13px]">pt</span>
                              </p>
                          </div>

                          <div class="rounded-[12px] border border-[#DDE6F5] bg-white px-3 py-4 text-center">
                              <div class="text-[32px]">🏅</div>
                              <p class="mt-2 text-[14px] font-black text-[#334155]">月間</p>
                              <p class="mt-1 text-[28px] font-black leading-none text-[#071433]">
                                  {{ $monthlyRank }}<span class="text-[14px]">位</span>
                              </p>
                          </div>
                      </div>
                  </section>

                  {{-- 書くコツ --}}
                  <section class="rounded-[18px] border border-[#DDE6F5] bg-white px-6 py-6 shadow-[0_8px_22px_rgba(15,43,95,0.06)]">
                      <h2 class="mb-5 flex items-center gap-3 text-[22px] font-black text-[#071433]">
                          <span>💡</span>
                          書くコツ
                      </h2>

                      <ul class="space-y-3 text-[15px] font-bold text-[#334155]">
                          <li class="flex items-start gap-3">
                              <span class="mt-0.5 text-emerald-500">●</span>
                              出来事→感情→理由→学びの順で書く
                          </li>
                          <li class="flex items-start gap-3">
                              <span class="mt-0.5 text-emerald-500">●</span>
                              1つの場面に絞ると書きやすい
                          </li>
                          <li class="flex items-start gap-3">
                              <span class="mt-0.5 text-emerald-500">●</span>
                              短くても継続することが大切
                          </li>
                      </ul>
                  </section>

                  {{-- 応援カード --}}
                  <section class="relative overflow-hidden rounded-[18px] border border-[#BFD6FF] bg-[#F0F7FF] px-6 py-6 shadow-[0_8px_22px_rgba(15,43,95,0.06)]">
                      <div class="pr-20">
                          <p class="text-[18px] font-black leading-relaxed text-[#0D4FE8]">
                              毎日の小さな積み重ねが、<br>
                              大きな成長につながります！
                          </p>

                          <p class="mt-4 text-[15px] font-bold leading-relaxed text-[#334155]">
                              完璧じゃなくて大丈夫。<br>
                              続けることがいちばんの力になります。
                          </p>
                      </div>

                      <div class="absolute bottom-5 right-5 text-[64px]">
                          🌱
                      </div>
                  </section>
              </aside>
          </div>
      </form>
  </div>
</div>
