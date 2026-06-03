{{-- PC版：resources/views/trainings/challenge-create_pc.blade.php --}}
<div class="hidden md:block min-h-screen bg-[#F8FAFF] text-[#071433]">
  <div class="mx-auto w-full max-w-[1440px] px-8 py-10">

      @if (session('error'))
          <div class="mb-6 rounded-2xl border border-red-200 bg-red-50 px-5 py-4 text-[15px] font-bold text-red-700">
              {{ session('error') }}
          </div>
      @endif

      <form method="POST"
          action="{{ route('trainings.challenge.store') }}"
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
                              <div class="mb-5 inline-flex rounded-full bg-orange-50 px-4 py-2 text-[16px] font-bold text-orange-600">
                                  今日の振り返り
                              </div>

                              <div class="flex items-center gap-4">
                                  <div class="shrink-0 text-[62px] leading-none">
                                      🔥
                                  </div>

                                  <h1 class="text-[52px] font-black leading-tight tracking-[0.01em] text-[#071433]">
                                      今日のチャレンジ
                                  </h1>
                              </div>

                              <p class="mt-5 text-[18px] font-bold leading-relaxed text-[#334155]">
                                  今日の挑戦を短く整理して、明日の行動につなげましょう。
                              </p>

                              <div class="mt-7 flex items-center gap-3 text-[18px] font-bold text-[#334155]">
                                  <span class="text-[24px]">🗓️</span>
                                  <span>{{ old('training_date', now()->format('Y-m-d')) }}</span>
                              </div>
                          </div>

                          <div class="flex justify-center">
                              <img
                                  src="{{ asset('images/training-top.png') }}"
                                  alt="今日のチャレンジ"
                                  class="h-[220px] w-full max-w-[430px] object-contain"
                                  loading="eager"
                              >
                          </div>
                      </div>
                  </section>

                  {{-- 日付・振り返りの流れ --}}
                  <section class="mb-6 grid grid-cols-[270px_1fr] gap-6">
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
                              <span class="text-[25px]">🔥</span>
                              振り返りの流れ
                          </h2>

                          <div class="grid grid-cols-4 gap-3">
                              <div class="flex items-center gap-3 rounded-[12px] bg-orange-50 px-3 py-3">
                                  <span class="flex h-8 w-8 items-center justify-center rounded-full bg-orange-100 text-[15px] font-black text-orange-600">
                                      1
                                  </span>
                                  <span class="text-[15px] font-black text-orange-600">挑戦したこと</span>
                              </div>

                              <div class="flex items-center gap-3 rounded-[12px] bg-emerald-50 px-3 py-3">
                                  <span class="flex h-8 w-8 items-center justify-center rounded-full bg-emerald-100 text-[15px] font-black text-emerald-700">
                                      2
                                  </span>
                                  <span class="text-[15px] font-black text-emerald-700">できたこと</span>
                              </div>

                              <div class="flex items-center gap-3 rounded-[12px] bg-violet-50 px-3 py-3">
                                  <span class="flex h-8 w-8 items-center justify-center rounded-full bg-violet-100 text-[15px] font-black text-violet-700">
                                      3
                                  </span>
                                  <span class="text-[15px] font-black text-violet-700">難しかったこと</span>
                              </div>

                              <div class="flex items-center gap-3 rounded-[12px] bg-amber-50 px-3 py-3">
                                  <span class="flex h-8 w-8 items-center justify-center rounded-full bg-amber-100 text-[15px] font-black text-amber-700">
                                      4
                                  </span>
                                  <span class="text-[15px] font-black text-amber-700">次に改善したいこと</span>
                              </div>
                          </div>
                      </div>
                  </section>

                  {{-- 入力フォーム --}}
                  <section class="mb-6 grid grid-cols-2 gap-6">
                      <div class="rounded-[18px] border border-[#DDE6F5] bg-white px-5 py-5 shadow-[0_8px_22px_rgba(15,43,95,0.06)]">
                          <div class="mb-3 flex items-center justify-between gap-3">
                              <div class="flex items-center gap-3">
                                  <span class="flex h-10 w-10 shrink-0 items-center justify-center rounded-full bg-orange-100 text-[20px] font-black text-orange-600">
                                      1
                                  </span>
                                  <h2 class="text-[20px] font-black text-[#071433]">
                                      今日チャレンジしたこと
                                  </h2>
                              </div>
                              <span class="text-[22px]">✎</span>
                          </div>

                          <p class="mb-3 text-[14px] font-bold leading-relaxed text-[#334155]">
                              今日取り組んだ挑戦や、目標に向けて行動したことを書きましょう。
                          </p>

                          <textarea
                              name="challenged_thing"
                              rows="4"
                              maxlength="500"
                              data-training-textarea
                              data-count-target="pcChallengedThingCount"
                              class="min-h-[112px] w-full resize-none rounded-[14px] border border-[#CBD7EA] bg-white px-4 py-4 text-[16px] font-bold leading-[1.8] text-[#071433] outline-none placeholder:text-[#7B879E] focus:border-[#0D4FE8] focus:ring-4 focus:ring-blue-100"
                          >{{ old('challenged_thing') }}</textarea>

                          <div class="mt-2 text-right text-[14px] font-bold text-[#64748B]">
                              <span id="pcChallengedThingCount">0</span> / 500文字
                          </div>

                          @error('challenged_thing')
                              <p class="mt-3 rounded-xl bg-red-50 px-3 py-2 text-[14px] font-bold text-red-600">
                                  {{ $message }}
                              </p>
                          @enderror
                      </div>

                      <div class="rounded-[18px] border border-[#DDE6F5] bg-white px-5 py-5 shadow-[0_8px_22px_rgba(15,43,95,0.06)]">
                          <div class="mb-3 flex items-center gap-3">
                              <span class="flex h-10 w-10 shrink-0 items-center justify-center rounded-full bg-emerald-100 text-[20px] font-black text-emerald-600">
                                  2
                              </span>
                              <h2 class="text-[20px] font-black text-[#071433]">
                                  できたこと
                              </h2>
                          </div>

                          <p class="mb-3 text-[14px] font-bold leading-relaxed text-[#334155]">
                              うまくいったこと、達成できたことを書きましょう。
                          </p>

                          <textarea
                              name="completed_thing"
                              rows="4"
                              maxlength="500"
                              data-training-textarea
                              data-count-target="pcCompletedThingCount"
                              class="min-h-[112px] w-full resize-none rounded-[14px] border border-[#CBD7EA] bg-white px-4 py-4 text-[16px] font-bold leading-[1.8] text-[#071433] outline-none placeholder:text-[#7B879E] focus:border-[#0D4FE8] focus:ring-4 focus:ring-blue-100"
                          >{{ old('completed_thing') }}</textarea>

                          <div class="mt-2 text-right text-[14px] font-bold text-[#64748B]">
                              <span id="pcCompletedThingCount">0</span> / 500文字
                          </div>

                          @error('completed_thing')
                              <p class="mt-3 rounded-xl bg-red-50 px-3 py-2 text-[14px] font-bold text-red-600">
                                  {{ $message }}
                              </p>
                          @enderror
                      </div>

                      <div class="rounded-[18px] border border-[#DDE6F5] bg-white px-5 py-5 shadow-[0_8px_22px_rgba(15,43,95,0.06)]">
                          <div class="mb-3 flex items-center gap-3">
                              <span class="flex h-10 w-10 shrink-0 items-center justify-center rounded-full bg-violet-100 text-[20px] font-black text-violet-600">
                                  3
                              </span>
                              <h2 class="text-[20px] font-black text-[#071433]">
                                  難しかったこと
                              </h2>
                          </div>

                          <p class="mb-3 text-[14px] font-bold leading-relaxed text-[#334155]">
                              うまくいかなかったこと、つまずいたことを書きましょう。
                          </p>

                          <textarea
                              name="difficult_thing"
                              rows="4"
                              maxlength="500"
                              data-training-textarea
                              data-count-target="pcDifficultThingCount"
                              class="min-h-[112px] w-full resize-none rounded-[14px] border border-[#CBD7EA] bg-white px-4 py-4 text-[16px] font-bold leading-[1.8] text-[#071433] outline-none placeholder:text-[#7B879E] focus:border-[#0D4FE8] focus:ring-4 focus:ring-blue-100"
                          >{{ old('difficult_thing') }}</textarea>

                          <div class="mt-2 text-right text-[14px] font-bold text-[#64748B]">
                              <span id="pcDifficultThingCount">0</span> / 500文字
                          </div>

                          @error('difficult_thing')
                              <p class="mt-3 rounded-xl bg-red-50 px-3 py-2 text-[14px] font-bold text-red-600">
                                  {{ $message }}
                              </p>
                          @enderror
                      </div>

                      <div class="rounded-[18px] border border-[#DDE6F5] bg-white px-5 py-5 shadow-[0_8px_22px_rgba(15,43,95,0.06)]">
                          <div class="mb-3 flex items-center gap-3">
                              <span class="flex h-10 w-10 shrink-0 items-center justify-center rounded-full bg-orange-100 text-[20px] font-black text-orange-600">
                                  4
                              </span>
                              <h2 class="text-[20px] font-black text-[#071433]">
                                  次に改善したいこと
                              </h2>
                          </div>

                          <p class="mb-3 text-[14px] font-bold leading-relaxed text-[#334155]">
                              明日に向けて、改善したいことや試したいことを書きましょう。
                          </p>

                          <textarea
                              name="next_improvement"
                              rows="4"
                              maxlength="500"
                              data-training-textarea
                              data-count-target="pcNextImprovementCount"
                              class="min-h-[112px] w-full resize-none rounded-[14px] border border-[#CBD7EA] bg-white px-4 py-4 text-[16px] font-bold leading-[1.8] text-[#071433] outline-none placeholder:text-[#7B879E] focus:border-[#0D4FE8] focus:ring-4 focus:ring-blue-100"
                          >{{ old('next_improvement') }}</textarea>

                          <div class="mt-2 text-right text-[14px] font-bold text-[#64748B]">
                              <span id="pcNextImprovementCount">0</span> / 500文字
                          </div>

                          @error('next_improvement')
                              <p class="mt-3 rounded-xl bg-red-50 px-3 py-2 text-[14px] font-bold text-red-600">
                                  {{ $message }}
                              </p>
                          @enderror
                      </div>
                  </section>

                  {{-- 下部ボタン --}}
                  <div class="grid grid-cols-[390px_1fr] gap-6">
                      <a href="{{ route('trainings.index') }}"
                          class="flex h-[64px] items-center justify-center gap-3 rounded-[16px] border border-[#CBD7EA] bg-white text-[22px] font-black text-[#1B2540] shadow-[0_8px_20px_rgba(15,43,95,0.06)]">
                          <span class="text-[28px]">←</span>
                          戻る
                      </a>

                      <button type="submit"
                          class="flex h-[64px] items-center justify-center gap-4 rounded-[16px] bg-[#0D4FE8] text-[23px] font-black text-white shadow-[0_12px_22px_rgba(13,79,232,0.28)] active:scale-[0.99]">
                          <span>✨</span>
                          AI採点する
                          <span class="text-[28px]">›</span>
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
                                  {{ $continuousDays ?? 7 }}<span class="text-[14px]">日</span>
                              </p>
                          </div>

                          <div class="rounded-[12px] border border-[#DDE6F5] bg-white px-3 py-4 text-center">
                              <div class="text-[32px]">🏅</div>
                              <p class="mt-2 text-[14px] font-black text-[#334155]">総pt</p>
                              <p class="mt-1 text-[25px] font-black leading-none text-[#071433]">
                                  {{ $totalPoints ?? 1280 }}<span class="text-[13px]">pt</span>
                              </p>
                          </div>

                          <div class="rounded-[12px] border border-[#DDE6F5] bg-white px-3 py-4 text-center">
                              <div class="text-[32px]">🏅</div>
                              <p class="mt-2 text-[14px] font-black text-[#334155]">月間</p>
                              <p class="mt-1 text-[28px] font-black leading-none text-[#071433]">
                                  {{ $monthlyRank ?? 12 }}<span class="text-[14px]">位</span>
                              </p>
                          </div>
                      </div>
                  </section>

                  {{-- 振り返りのコツ --}}
                  <section class="rounded-[18px] border border-[#DDE6F5] bg-white px-6 py-6 shadow-[0_8px_22px_rgba(15,43,95,0.06)]">
                      <h2 class="mb-5 flex items-center gap-3 text-[22px] font-black text-[#071433]">
                          <span>💡</span>
                          振り返りのコツ
                      </h2>

                      <ul class="space-y-3 text-[15px] font-bold text-[#334155]">
                          <li class="flex items-start gap-3">
                              <span class="mt-0.5 text-emerald-500">●</span>
                              1つずつ短く書けばOK
                          </li>
                          <li class="flex items-start gap-3">
                              <span class="mt-0.5 text-emerald-500">●</span>
                              成功と失敗の両方を書く
                          </li>
                          <li class="flex items-start gap-3">
                              <span class="mt-0.5 text-emerald-500">●</span>
                              明日の行動につながる一言を残す
                          </li>
                      </ul>
                  </section>

                  {{-- 応援カード --}}
                  <section class="relative overflow-hidden rounded-[18px] border border-orange-200 bg-orange-50 px-6 py-6 shadow-[0_8px_22px_rgba(15,43,95,0.06)]">
                      <div class="pr-20">
                          <p class="text-[18px] font-black leading-relaxed text-orange-600">
                              小さな挑戦の積み重ねが、<br>
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
