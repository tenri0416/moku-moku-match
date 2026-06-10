<div class="block md:hidden min-h-screen bg-[#F8FAFF] px-3 pb-28 pt-4 text-[#071433]">
  <div class="mx-auto max-w-[430px]">

      @if (session('success'))
          <div class="mb-4 rounded-2xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-[13px] font-bold text-emerald-700">
              {{ session('success') }}
          </div>
      @endif

      @if (session('error'))
          <div class="mb-4 rounded-2xl border border-red-200 bg-red-50 px-4 py-3 text-[13px] font-bold text-red-700">
              {{ session('error') }}
          </div>
      @endif

      <section class="mb-4 rounded-[20px] bg-gradient-to-br from-[#1D66F3] to-[#0648D8] px-4 py-5 text-white shadow-[0_12px_24px_rgba(13,79,232,0.25)]">
          <p class="text-[13px] font-black text-blue-50">語彙を忘れないための復習</p>
          <h1 class="mt-1 text-[26px] font-black leading-tight">ボキャブラリー<br>トレーニング</h1>

          <div class="mt-4 grid grid-cols-4 gap-2 text-center">
              <div class="rounded-xl bg-white/15 px-2 py-2">
                  <p class="text-[10px] font-bold">登録</p>
                  <p class="mt-1 text-[18px] font-black">{{ $totalWords }}</p>
              </div>
              <div class="rounded-xl bg-white/15 px-2 py-2">
                  <p class="text-[10px] font-bold">復習対象</p>
                  <p class="mt-1 text-[18px] font-black">{{ $reviewTargetCount }}</p>
              </div>
              <div class="rounded-xl bg-white/15 px-2 py-2">
                  <p class="text-[10px] font-bold">苦手</p>
                  <p class="mt-1 text-[18px] font-black">{{ $weakCount }}</p>
              </div>
              <div class="rounded-xl bg-white/15 px-2 py-2">
                  <p class="text-[10px] font-bold">定着</p>
                  <p class="mt-1 text-[18px] font-black">{{ $masteredCount }}</p>
              </div>
          </div>
      </section>

      <div class="mb-4 grid grid-cols-2 gap-3">
          <a href="{{ route('trainings.vocabulary.create') }}"
              class="flex h-[54px] items-center justify-center rounded-[14px] bg-[#0D4FE8] text-[15px] font-black text-white shadow-[0_10px_18px_rgba(13,79,232,0.25)]">
              ＋ 言葉を登録
          </a>

          <a href="{{ route('trainings.vocabulary.review') }}"
              class="flex h-[54px] items-center justify-center rounded-[14px] border-2 border-[#0D4FE8] bg-white text-[15px] font-black text-[#0D4FE8]">
              復習する
          </a>
      </div>

      <form method="GET" action="{{ route('trainings.vocabulary.index') }}" class="mb-4 rounded-[16px] border border-[#DDE6F5] bg-white p-3 shadow-sm">
          <input
              type="text"
              name="keyword"
              value="{{ request('keyword') }}"
              class="w-full rounded-xl border border-[#CBD7EA] px-3 py-3 text-[15px] font-bold outline-none focus:border-[#0D4FE8]"
              placeholder="言葉・意味・例文を検索"
          >

          <button class="mt-3 flex h-[44px] w-full items-center justify-center rounded-xl bg-[#0D4FE8] text-[15px] font-black text-white">
              検索
          </button>
      </form>

      <section class="space-y-3">
          @forelse ($words as $word)
              <article class="rounded-[16px] border border-[#DDE6F5] bg-white p-4 shadow-sm">
                  <div class="flex items-start justify-between gap-3">
                      <div class="min-w-0">
                          <h2 class="break-words text-[22px] font-black text-[#071433]">
                              {{ $word->word }}
                          </h2>

                          <p class="mt-1 text-[12px] font-black text-[#0D4FE8]">
                              {{ $word->statusLabel() }} ・ 復習{{ $word->review_count }}回 ・ 正答率{{ $word->correctRate() }}%
                          </p>
                      </div>

                      <span class="shrink-0 rounded-full bg-orange-50 px-3 py-1 text-[12px] font-black text-orange-600">
                          重要度{{ $word->importance }}
                      </span>
                  </div>

                  <p class="mt-3 line-clamp-2 text-[14px] font-bold leading-7 text-[#334155]">
                      {{ $word->meaning }}
                  </p>

                  <p class="mt-2 line-clamp-2 rounded-xl bg-[#F8FAFF] px-3 py-2 text-[13px] font-bold leading-6 text-[#46516B]">
                      {{ $word->example_sentence }}
                  </p>

                  <div class="mt-4 grid grid-cols-3 gap-2">
                      <a href="{{ route('trainings.vocabulary.review', ['word_id' => $word->id]) }}"
                          class="flex h-[42px] items-center justify-center rounded-xl bg-[#0D4FE8] text-[13px] font-black text-white">
                          復習
                      </a>

                      <a href="{{ route('trainings.vocabulary.edit', $word) }}"
                          class="flex h-[42px] items-center justify-center rounded-xl border border-[#CBD7EA] bg-white text-[13px] font-black text-[#0D4FE8]">
                          編集
                      </a>

                      <form method="POST" action="{{ route('trainings.vocabulary.destroy', $word) }}">
                          @csrf
                          @method('DELETE')
                          <button class="flex h-[42px] w-full items-center justify-center rounded-xl border border-red-200 bg-red-50 text-[13px] font-black text-red-600">
                              削除
                          </button>
                      </form>
                  </div>
              </article>
          @empty
              <div class="rounded-[16px] border border-[#DDE6F5] bg-white px-4 py-8 text-center">
                  <p class="text-[14px] font-bold text-[#64748B]">
                      まだ登録された言葉がありません。
                  </p>
              </div>
          @endforelse
      </section>

      <div class="mt-5">
          {{ $words->links() }}
      </div>
  </div>
</div>
