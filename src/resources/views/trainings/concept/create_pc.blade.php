{{-- PC版：resources/views/trainings/concept/create_pc.blade.php --}}
<div class="hidden md:block min-h-screen bg-[#F8FAFF] text-[#071433]">
  <div class="mx-auto w-full max-w-[1440px] px-8 py-10">

      @if (session('error'))
          <div class="mb-6 rounded-2xl border border-red-200 bg-red-50 px-5 py-4 text-[15px] font-bold text-red-700">
              {{ session('error') }}
          </div>
      @endif

      <form method="POST"
          action="{{ $storeRoute }}"
          data-ai-loading="true"
          data-ai-loading-type="score">
          @csrf

          <input type="hidden" name="training_id" value="{{ $training->id }}">

          <div class="grid grid-cols-[1fr_400px] gap-8">

              {{-- 左側メイン --}}
              <main>
                  {{-- ヒーロー --}}
                  <section class="mb-6">
                      <div class="grid grid-cols-[1fr_430px] items-center gap-6">
                          <div>
                              <div class="mb-5 inline-flex rounded-full border border-[#8DB3FF] bg-white px-5 py-2 text-[16px] font-black text-[#0D4FE8]">
                                  問題
                              </div>

                              <h1 class="text-[48px] font-black leading-tight tracking-[0.01em] text-[#071433]">
                                  {{ $typeLabel }}
                              </h1>

                              <p class="mt-5 text-[18px] font-bold leading-relaxed text-[#334155]">
                                  2つの言葉に共通する本質を、短い文章で考えてみましょう。
                              </p>
                          </div>

                          <div class="flex justify-center">
                              <img
                                  src="{{ asset('images/training-top.png') }}"
                                  alt="{{ $typeLabel }}"
                                  class="h-[220px] w-full max-w-[430px] object-contain"
                                  loading="eager"
                              >
                          </div>
                      </div>
                  </section>

                  {{-- 本日の問題 --}}
                  <section class="mb-6 rounded-[18px] border border-[#DDE6F5] bg-white px-6 py-6 shadow-[0_8px_22px_rgba(15,43,95,0.06)]">
                      <div class="mb-5 flex items-center justify-between gap-4">
                          <h2 class="flex items-center gap-3 text-[23px] font-black text-[#0D4FE8]">
                              <span class="flex h-10 w-10 items-center justify-center rounded-[8px] bg-[#0D4FE8] text-[22px] text-white">
                                  📄
                              </span>
                              本日の問題
                          </h2>

                          <span class="rounded-full border border-[#8DB3FF] bg-white px-4 py-1 text-[14px] font-black text-[#0D4FE8]">
                              {{ $training->difficulty_label }}
                          </span>
                      </div>

                      <div class="mb-5 rounded-[16px] border border-[#BFD6FF] bg-[#F0F7FF] px-5 py-5 text-center">
                          <p class="text-[15px] font-black text-[#0D4FE8]">テーマ</p>
                          <h3 class="mt-2 text-[34px] font-black leading-tight text-[#071433]">
                              {{ $training->theme_a }}
                              <span class="mx-3 text-[#94A3B8]">×</span>
                              {{ $training->theme_b }}
                          </h3>
                      </div>

                      <div class="whitespace-pre-wrap rounded-[12px] border border-[#CBD7EA] bg-[#F9FBFF] px-4 py-4 text-[16px] font-bold leading-[1.9] text-[#1B2540]">{{ $questionBody }}</div>

                      <p class="mt-4 text-[14px] font-bold text-[#334155]">
                          ※ 役割・目的・構造・感情・変化のどれかで見ると考えやすくなります。
                      </p>
                  </section>

                  {{-- 回答 --}}
                  <section class="mb-6 rounded-[18px] border border-[#DDE6F5] bg-white px-6 py-6 shadow-[0_8px_22px_rgba(15,43,95,0.06)]">
                      <div class="mb-4 flex items-center gap-3">
                          <span class="flex h-10 w-10 shrink-0 items-center justify-center rounded-[8px] bg-[#0D4FE8] text-[22px] text-white">
                              ✎
                          </span>

                          <h2 class="text-[23px] font-black text-[#0D4FE8]">
                              あなたの回答
                          </h2>

                          <span class="text-[15px] font-bold text-[#3B82F6]">
                              1〜3文でOK
                          </span>
                      </div>

                      <textarea
                          name="answer_body"
                          rows="5"
                          maxlength="{{ $answerMaxLength }}"
                          data-ai-answer-textarea
                          data-count-target="pcConceptAnswerCount"
                          class="min-h-[118px] w-full resize-y rounded-[14px] border border-[#CBD7EA] bg-white px-4 py-4 text-[16px] font-bold leading-[1.8] text-[#071433] outline-none placeholder:text-[#7B879E] focus:border-[#0D4FE8] focus:ring-4 focus:ring-blue-100"
                          placeholder="例：{{ $training->theme_a }}と{{ $training->theme_b }}は抽象化してみると、〇〇という意味で一緒だ。"
                      >{{ old('answer_body', $training->answer_body) }}</textarea>

                      <div class="mt-3 flex items-center justify-between gap-4">
                          <p class="text-[14px] font-bold text-[#64748B]">
                              正解は1つではありません。自分の見方で書いてみましょう。
                          </p>

                          <p class="shrink-0 text-[14px] font-bold text-[#64748B]">
                              <span id="pcConceptAnswerCount">0</span> / {{ $answerMaxLength }}文字
                          </p>
                      </div>

                      @error('answer_body')
                          <p class="mt-3 rounded-xl bg-red-50 px-3 py-2 text-[14px] font-bold text-red-600">
                              {{ $message }}
                          </p>
                      @enderror
                  </section>

                  {{-- 採点項目 --}}
                  <section class="mb-6 rounded-[18px] border border-[#DDE6F5] bg-white px-6 py-5 shadow-[0_8px_22px_rgba(15,43,95,0.06)]">
                      <div class="mb-5 flex items-center gap-3">
                          <span class="text-[34px] leading-none text-[#0D4FE8]">★</span>

                          <h2 class="text-[23px] font-black text-[#0D4FE8]">
                              採点項目
                          </h2>

                          <span class="rounded-full border border-[#8DB3FF] bg-white px-4 py-1 text-[14px] font-black text-[#0D4FE8]">
                              各25点
                          </span>
                      </div>

                      <div class="grid grid-cols-4 gap-6">
                          @foreach ($scoreLabels as $label)
                              <div class="flex h-[44px] items-center justify-center rounded-full border border-[#8DB3FF] bg-white px-3 text-center text-[15px] font-black text-[#0D4FE8]">
                                  {{ $label }}
                              </div>
                          @endforeach
                      </div>
                  </section>

                  {{-- 下部ボタン --}}
                  <div class="grid grid-cols-[400px_1fr] gap-8">
                      <a href="{{ route('trainings.index') }}"
                          class="flex h-[64px] items-center justify-center gap-3 rounded-[12px] border-2 border-[#0D4FE8] bg-white text-[20px] font-black text-[#071433] shadow-[0_8px_20px_rgba(15,43,95,0.06)]">
                          <span class="text-[24px]">←</span>
                          一覧に戻る
                      </a>

                      <button type="submit"
                          class="flex h-[64px] items-center justify-center gap-4 rounded-[12px] bg-[#0D4FE8] text-[22px] font-black text-white shadow-[0_12px_22px_rgba(13,79,232,0.28)] active:scale-[0.99]">
                          <span>✨</span>
                          保存してAI採点する
                      </button>
                  </div>
              </main>

              {{-- 右側サイド --}}
              <aside class="space-y-6">
                  {{-- 回答の型 --}}
                  <section class="rounded-[18px] border border-[#DDE6F5] bg-white px-6 py-6 shadow-[0_8px_22px_rgba(15,43,95,0.06)]">
                      <h2 class="mb-5 flex items-center gap-3 text-[22px] font-black text-[#071433]">
                          <span class="text-[#0D4FE8]">🧩</span>
                          回答の型
                      </h2>

                      <div class="space-y-4">
                          <div class="rounded-[14px] border border-[#BFD6FF] bg-[#F0F7FF] px-4 py-4">
                              <p class="text-[14px] font-black text-[#0D4FE8]">基本の型</p>
                              <p class="mt-2 text-[15px] font-bold leading-relaxed text-[#334155]">
                                  AとBは抽象化してみると、〇〇という意味で一緒だ。
                              </p>
                          </div>

                          <div class="rounded-[14px] border border-[#BFD6FF] bg-white px-4 py-4">
                              <p class="text-[14px] font-black text-[#0D4FE8]">別解の型</p>
                              <p class="mt-2 text-[15px] font-bold leading-relaxed text-[#334155]">
                                  AとBは△△という見方でも一緒だ。
                              </p>
                          </div>
                      </div>
                  </section>

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
                                      本質を捉えられているかを評価します
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
                                      視点や表現の良かったところを紹介
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
                                      より抽象度を上げるコツを提示
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
                                      次に意識すると良い視点を提案
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
                                  {{ $continuousDays }}<span class="text-[14px]">日</span>
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

                  {{-- コツ --}}
                  <section class="rounded-[18px] border border-[#DDE6F5] bg-white px-6 py-6 shadow-[0_8px_22px_rgba(15,43,95,0.06)]">
                      <h2 class="mb-5 flex items-center gap-3 text-[22px] font-black text-[#071433]">
                          <span>💡</span>
                          {{ $trainingThemeLabel }}
                      </h2>

                      <ul class="space-y-3 text-[15px] font-bold text-[#334155]">
                          @foreach ($tips as $tip)
                              <li class="flex items-start gap-3">
                                  <span class="mt-0.5 text-emerald-500">●</span>
                                  {{ $tip }}
                              </li>
                          @endforeach
                      </ul>

                      <div class="mt-5 rounded-[14px] border border-[#BFD6FF] bg-[#F0F7FF] px-4 py-4">
                          <p class="text-[14px] font-black text-[#0D4FE8]">今日のヒント</p>
                          <p class="mt-2 text-[15px] font-bold leading-relaxed text-[#334155]">
                              {{ $training->answer_point }}
                          </p>
                      </div>
                  </section>

                  {{-- 応援カード --}}
                  <section class="relative overflow-hidden rounded-[18px] border border-[#BFD6FF] bg-[#F0F7FF] px-6 py-6 shadow-[0_8px_22px_rgba(15,43,95,0.06)]">
                      <div class="pr-20">
                          <p class="text-[18px] font-black leading-relaxed text-[#0D4FE8]">
                              {{ $supportMessageTitle }}
                          </p>

                          <p class="mt-4 text-[15px] font-bold leading-relaxed text-[#334155]">
                              {{ $supportMessageBody }}
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
