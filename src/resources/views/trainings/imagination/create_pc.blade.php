{{-- PC版：resources/views/trainings/imagination/create_pc.blade.php --}}
<div class="hidden md:block min-h-screen bg-[#F8FAFF] text-[#071433]">
  <div class="mx-auto w-full max-w-[1440px] px-8 py-10">

      <form method="POST" action="{{ $storeRoute }}" data-ai-loading="true" data-ai-loading-type="score">
          @csrf

          <input type="hidden" name="training_id" value="{{ $training->id }}">

          <div class="grid grid-cols-[1fr_400px] gap-8">
              <main>
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
                                  場面や気持ちを想像し、理由と一緒に文章にしてみましょう。
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

                  <section class="mb-6 rounded-[18px] border border-[#DDE6F5] bg-white px-6 py-6 shadow-[0_8px_22px_rgba(15,43,95,0.06)]">
                      <div class="mb-5 flex items-center justify-between gap-4">
                          <h2 class="flex items-center gap-3 text-[23px] font-black text-[#0D4FE8]">
                              <span class="flex h-10 w-10 items-center justify-center rounded-[8px] bg-[#0D4FE8] text-[22px] text-white">
                                  🌈
                              </span>
                              本日の問題
                          </h2>

                          <span class="rounded-full border border-[#8DB3FF] bg-white px-4 py-1 text-[14px] font-black text-[#0D4FE8]">
                              {{ $training->difficulty_label }}
                          </span>
                      </div>

                      <div class="mb-4 rounded-full bg-[#F0F7FF] px-4 py-2 text-center text-[15px] font-black text-[#0D4FE8]">
                          {{ $training->question_type }}
                      </div>

                      <div class="whitespace-pre-wrap rounded-[12px] border border-[#CBD7EA] bg-[#F9FBFF] px-4 py-4 text-[16px] font-bold leading-[1.9] text-[#1B2540]">{{ $questionBody }}</div>
                  </section>

                  <section class="mb-6 rounded-[18px] border border-[#DDE6F5] bg-white px-6 py-6 shadow-[0_8px_22px_rgba(15,43,95,0.06)]">
                      <div class="mb-4 flex items-center gap-3">
                          <span class="flex h-10 w-10 shrink-0 items-center justify-center rounded-[8px] bg-[#0D4FE8] text-[22px] text-white">
                              ✎
                          </span>

                          <h2 class="text-[23px] font-black text-[#0D4FE8]">
                              あなたの回答
                          </h2>

                          <span class="text-[15px] font-bold text-[#3B82F6]">
                              1〜5文でOK
                          </span>
                      </div>

                      <textarea
                          name="answer_body"
                          rows="6"
                          maxlength="{{ $answerMaxLength }}"
                          data-ai-answer-textarea
                          data-count-target="pcImaginationAnswerCount"
                          class="min-h-[140px] w-full resize-y rounded-[14px] border border-[#CBD7EA] bg-white px-4 py-4 text-[16px] font-bold leading-[1.8] text-[#071433] outline-none placeholder:text-[#7B879E] focus:border-[#0D4FE8] focus:ring-4 focus:ring-blue-100"
                          placeholder="例：私は、〇〇という状況だと想像しました。理由は△△だからです。"
                      >{{ old('answer_body', $training->answer_body) }}</textarea>

                      <div class="mt-3 flex items-center justify-between gap-4">
                          <p class="text-[14px] font-bold text-[#64748B]">
                              正解を当てる必要はありません。理由も添えてみましょう。
                          </p>

                          <p class="shrink-0 text-[14px] font-bold text-[#64748B]">
                              <span id="pcImaginationAnswerCount">0</span> / {{ $answerMaxLength }}文字
                          </p>
                      </div>
                  </section>

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

              <aside class="space-y-6">
                  <section class="rounded-[18px] border border-[#DDE6F5] bg-white px-6 py-6 shadow-[0_8px_22px_rgba(15,43,95,0.06)]">
                      <h2 class="mb-5 flex items-center gap-3 text-[22px] font-black text-[#071433]">
                          <span>💡</span>
                          想像力を高めるコツ
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

                  <section class="rounded-[18px] border border-[#DDE6F5] bg-white px-6 py-6 shadow-[0_8px_22px_rgba(15,43,95,0.06)]">
                      <h2 class="mb-5 flex items-center gap-3 text-[22px] font-black text-[#071433]">
                          <span>📊</span>
                          採点項目
                      </h2>

                      <div class="grid grid-cols-2 gap-3">
                          @foreach ($scoreLabels as $label)
                              <div class="flex h-[44px] items-center justify-center rounded-full border border-[#8DB3FF] bg-white px-3 text-center text-[14px] font-black text-[#0D4FE8]">
                                  {{ $label }}
                              </div>
                          @endforeach
                      </div>
                  </section>
              </aside>
          </div>
      </form>
  </div>
</div>
