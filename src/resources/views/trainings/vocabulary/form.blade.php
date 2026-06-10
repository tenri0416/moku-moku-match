<div class="min-h-screen bg-[#F8FAFF] px-3 pb-28 pt-5 text-[#071433] md:px-8 md:py-10">
  <div class="mx-auto max-w-[760px] rounded-3xl bg-white p-5 shadow-sm ring-1 ring-[#DDE6F5] md:p-8">
      <div class="mb-6">
          <a href="{{ route('trainings.vocabulary.index') }}" class="text-sm font-black text-[#0D4FE8]">
              ‹ 一覧に戻る
          </a>

          <h1 class="mt-4 text-2xl font-black text-[#071433] md:text-3xl">
              {{ $isEdit ? 'ボキャブラリー編集' : 'ボキャブラリー登録' }}
          </h1>

          <p class="mt-2 text-sm font-bold leading-7 text-[#64748B]">
              読書や学習で調べた言葉を登録して、後から復習できるようにしましょう。
          </p>
      </div>

      @if ($errors->any())
          <div class="mb-5 rounded-2xl border border-red-200 bg-red-50 px-4 py-3 text-sm font-bold text-red-700">
              @foreach ($errors->all() as $error)
                  <p>{{ $error }}</p>
              @endforeach
          </div>
      @endif

      <form method="POST" action="{{ $formRoute }}" class="space-y-5">
          @csrf
          @if ($formMethod === 'PUT')
              @method('PUT')
          @endif

          <div>
              <label class="text-sm font-black text-[#071433]">言葉</label>
              <input
                  type="text"
                  name="word"
                  value="{{ old('word', $word?->word) }}"
                  class="mt-2 w-full rounded-2xl border border-[#CBD7EA] px-4 py-3 text-base font-bold outline-none focus:border-[#0D4FE8]"
                  placeholder="例：示唆"
              >
          </div>

          <div>
              <label class="text-sm font-black text-[#071433]">意味</label>
              <textarea
                  name="meaning"
                  rows="4"
                  class="mt-2 w-full rounded-2xl border border-[#CBD7EA] px-4 py-3 text-base font-bold leading-8 outline-none focus:border-[#0D4FE8]"
                  placeholder="例：物事を直接言わず、それとなく気づかせること。"
              >{{ old('meaning', $word?->meaning) }}</textarea>
          </div>

          <div>
              <label class="text-sm font-black text-[#071433]">例文</label>
              <textarea
                  name="example_sentence"
                  rows="4"
                  class="mt-2 w-full rounded-2xl border border-[#CBD7EA] px-4 py-3 text-base font-bold leading-8 outline-none focus:border-[#0D4FE8]"
                  placeholder="例：この結果は、今後の学習方法を見直す必要があることを示唆している。"
              >{{ old('example_sentence', $word?->example_sentence) }}</textarea>
          </div>

          <div class="grid gap-4 md:grid-cols-3">
              <div>
                  <label class="text-sm font-black text-[#071433]">カテゴリー</label>
                  <input
                      type="text"
                      name="category"
                      value="{{ old('category', $word?->category) }}"
                      class="mt-2 w-full rounded-2xl border border-[#CBD7EA] px-4 py-3 text-base font-bold outline-none focus:border-[#0D4FE8]"
                      placeholder="読書"
                  >
              </div>

              <div>
                  <label class="text-sm font-black text-[#071433]">出典</label>
                  <input
                      type="text"
                      name="source"
                      value="{{ old('source', $word?->source) }}"
                      class="mt-2 w-full rounded-2xl border border-[#CBD7EA] px-4 py-3 text-base font-bold outline-none focus:border-[#0D4FE8]"
                      placeholder="本のタイトルなど"
                  >
              </div>

              <div>
                  <label class="text-sm font-black text-[#071433]">重要度</label>
                  <select
                      name="importance"
                      class="mt-2 w-full rounded-2xl border border-[#CBD7EA] px-4 py-3 text-base font-bold outline-none focus:border-[#0D4FE8]"
                  >
                      @for ($i = 1; $i <= 5; $i++)
                          <option value="{{ $i }}" @selected((int) old('importance', $word?->importance ?? 3) === $i)>
                              {{ $i }}
                          </option>
                      @endfor
                  </select>
              </div>
          </div>

          <div>
              <label class="text-sm font-black text-[#071433]">メモ</label>
              <textarea
                  name="memo"
                  rows="3"
                  class="mt-2 w-full rounded-2xl border border-[#CBD7EA] px-4 py-3 text-base font-bold leading-8 outline-none focus:border-[#0D4FE8]"
                  placeholder="覚えたい理由や補足メモ"
              >{{ old('memo', $word?->memo) }}</textarea>
          </div>

          <label class="flex items-center gap-3 rounded-2xl bg-[#F8FAFF] px-4 py-3">
              <input
                  type="checkbox"
                  name="is_review_target"
                  value="1"
                  class="h-5 w-5 rounded border-[#CBD7EA]"
                  @checked(old('is_review_target', $word?->is_review_target ?? true))
              >
              <span class="text-sm font-black text-[#071433]">
                  復習対象にする
              </span>
          </label>

          <button
              type="submit"
              class="flex h-[56px] w-full items-center justify-center rounded-2xl bg-[#0D4FE8] text-base font-black text-white shadow-[0_10px_18px_rgba(13,79,232,0.25)]"
          >
              {{ $isEdit ? '更新する' : '登録する' }}
          </button>
      </form>
  </div>
</div>
