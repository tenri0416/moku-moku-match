<form method="POST" action="{{ $action }}" class="space-y-6">
  @csrf

  @if ($method !== 'POST')
      @method($method)
  @endif

  <div class="rounded-2xl bg-white p-6 shadow-sm ring-1 ring-slate-200">
      <div>
          <label for="name" class="block text-sm font-bold text-slate-700">
              タグ名 <span class="text-rose-600">*</span>
          </label>

          <input
              id="name"
              type="text"
              name="name"
              value="{{ old('name', $tag->name) }}"
              class="mt-2 block w-full rounded-xl border-slate-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
              required
          >

          @error('name')
              <p class="mt-2 text-sm font-bold text-rose-600">{{ $message }}</p>
          @enderror
      </div>

      <div class="mt-6">
          <label for="slug" class="block text-sm font-bold text-slate-700">
              スラッグ
          </label>

          <input
              id="slug"
              type="text"
              name="slug"
              value="{{ old('slug', $tag->slug) }}"
              placeholder="freelance"
              class="mt-2 block w-full rounded-xl border-slate-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
          >

          <p class="mt-2 text-xs font-semibold text-slate-500">
              URLに使われます。半角英数字とハイフンのみ使用できます。
          </p>

          @error('slug')
              <p class="mt-2 text-sm font-bold text-rose-600">{{ $message }}</p>
          @enderror
      </div>

      <div class="mt-6">
          <label for="description" class="block text-sm font-bold text-slate-700">
              説明文
          </label>

          <textarea
              id="description"
              name="description"
              rows="4"
              class="mt-2 block w-full rounded-xl border-slate-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
          >{{ old('description', $tag->description) }}</textarea>

          @error('description')
              <p class="mt-2 text-sm font-bold text-rose-600">{{ $message }}</p>
          @enderror
      </div>

      <div class="mt-6 grid gap-6 sm:grid-cols-2">
          <div>
              <label for="sort_order" class="block text-sm font-bold text-slate-700">
                  並び順
              </label>

              <input
                  id="sort_order"
                  type="number"
                  name="sort_order"
                  value="{{ old('sort_order', $tag->sort_order ?? 0) }}"
                  min="0"
                  class="mt-2 block w-full rounded-xl border-slate-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
              >

              @error('sort_order')
                  <p class="mt-2 text-sm font-bold text-rose-600">{{ $message }}</p>
              @enderror
          </div>

          <div class="flex items-end">
              <label class="flex items-center gap-3 rounded-xl border border-slate-200 bg-slate-50 px-4 py-3 text-sm font-bold text-slate-700">
                  <input
                      type="checkbox"
                      name="is_active"
                      value="1"
                      class="rounded border-slate-300 text-indigo-600 focus:ring-indigo-500"
                      @checked(old('is_active', $tag->is_active ?? true))
                  >

                  <span>有効にする</span>
              </label>
          </div>
      </div>
  </div>

  <div class="flex flex-wrap gap-3">
      <button
          type="submit"
          class="rounded-xl bg-indigo-600 px-5 py-3 text-sm font-bold text-white transition hover:bg-indigo-700"
      >
          {{ $buttonText }}
      </button>

      <a
          href="{{ route('admin.article-tags.index') }}"
          class="rounded-xl border border-slate-300 bg-white px-5 py-3 text-sm font-bold text-slate-700 transition hover:bg-slate-50"
      >
          一覧へ戻る
      </a>
  </div>
</form>
