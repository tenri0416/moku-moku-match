@csrf

<div class="space-y-6">
    <div>
        <label for="title" class="mb-2 block text-sm font-bold text-slate-700">
            記事タイトル <span class="text-rose-500">必須</span>
        </label>

        <input type="text" id="title" name="title" value="{{ old('title', $article->title ?? '') }}"
            class="block w-full rounded-xl border-slate-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">

        @error('title')
        <p class="mt-2 text-sm font-semibold text-rose-600">{{ $message }}</p>
        @enderror
    </div>

    <div class="grid gap-6 md:grid-cols-2">
        <div>
            <label for="slug" class="mb-2 block text-sm font-bold text-slate-700">
                通常URLスラッグ <span class="text-rose-500">必須</span>
            </label>

            <input type="text" id="slug" name="slug" value="{{ old('slug', $article->slug ?? '') }}"
                placeholder="例：nara-freelance-work-partner"
                class="block w-full rounded-xl border-slate-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">

            <p class="mt-2 text-xs text-slate-500">
                URLは /articles/スラッグ になります。
            </p>

            @error('slug')
            <p class="mt-2 text-sm font-semibold text-rose-600">{{ $message }}</p>
            @enderror
        </div>

        <div>
            <label for="short_slug" class="mb-2 block text-sm font-bold text-slate-700">
                短縮URLスラッグ
            </label>

            <input type="text" id="short_slug" name="short_slug"
                value="{{ old('short_slug', $article->short_slug ?? '') }}" placeholder="例：nara"
                class="block w-full rounded-xl border-slate-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">

            <p class="mt-2 text-xs text-slate-500">
                入力すると /nara のようなURLでも表示できます。
            </p>

            @error('short_slug')
            <p class="mt-2 text-sm font-semibold text-rose-600">{{ $message }}</p>
            @enderror
        </div>
    </div>

    <div>
        <label for="prefecture_id" class="mb-2 block text-sm font-bold text-slate-700">
            対象都道府県
        </label>

        <select id="prefecture_id" name="prefecture_id"
            class="block w-full rounded-xl border-slate-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
            <option value="">全国向け</option>
            @foreach ($prefectures as $prefecture)
            <option value="{{ $prefecture->id }}" @selected((string) old('prefecture_id', $article->prefecture_id ?? '')
                === (string) $prefecture->id)
                >
                {{ $prefecture->name }}
            </option>
            @endforeach
        </select>

        @error('prefecture_id')
        <p class="mt-2 text-sm font-semibold text-rose-600">{{ $message }}</p>
        @enderror
    </div>

    <div class="grid gap-6 md:grid-cols-2">
        <div>
            <label for="seo_title" class="mb-2 block text-sm font-bold text-slate-700">
                SEOタイトル
            </label>

            <input type="text" id="seo_title" name="seo_title" value="{{ old('seo_title', $article->seo_title ?? '') }}"
                placeholder="例：奈良でフリーランスが作業仲間を探す方法"
                class="block w-full rounded-xl border-slate-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">

            @error('seo_title')
            <p class="mt-2 text-sm font-semibold text-rose-600">{{ $message }}</p>
            @enderror
        </div>

        <div>
            <label for="h1_title" class="mb-2 block text-sm font-bold text-slate-700">
                H1見出し
            </label>

            <input type="text" id="h1_title" name="h1_title" value="{{ old('h1_title', $article->h1_title ?? '') }}"
                placeholder="空の場合は記事タイトルを使います"
                class="block w-full rounded-xl border-slate-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">

            @error('h1_title')
            <p class="mt-2 text-sm font-semibold text-rose-600">{{ $message }}</p>
            @enderror
        </div>
    </div>

    <div>
        <label for="seo_description" class="mb-2 block text-sm font-bold text-slate-700">
            SEOディスクリプション
        </label>

        <textarea id="seo_description" name="seo_description" rows="3"
            class="block w-full rounded-xl border-slate-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">{{ old('seo_description', $article->seo_description ?? '') }}</textarea>

        @error('seo_description')
        <p class="mt-2 text-sm font-semibold text-rose-600">{{ $message }}</p>
        @enderror
    </div>

    <div>
        <label for="excerpt" class="mb-2 block text-sm font-bold text-slate-700">
            記事概要
        </label>

        <textarea id="excerpt" name="excerpt" rows="3"
            class="block w-full rounded-xl border-slate-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">{{ old('excerpt', $article->excerpt ?? '') }}</textarea>

        @error('excerpt')
        <p class="mt-2 text-sm font-semibold text-rose-600">{{ $message }}</p>
        @enderror
    </div>

    <div>
        <label for="thumbnail" class="mb-2 block text-sm font-bold text-slate-700">
            サムネイル画像
        </label>

        <input type="file" id="thumbnail" name="thumbnail" accept="image/jpeg,image/png,image/webp">

        @if ($article->thumbnail_path)
        <div class="mt-3">
            <img src="{{ asset('storage/' . $article->thumbnail_path) }}" alt="サムネイル画像"
                class="h-32 rounded-xl object-cover">
        </div>
        @endif

        @error('thumbnail')
        <p class="mt-2 text-sm font-semibold text-rose-600">{{ $message }}</p>
        @enderror
    </div>

    <div>
        <label class="mb-2 block text-sm font-bold text-slate-700">
            本文 <span class="text-rose-500">必須</span>
        </label>

        <input type="hidden" name="body_html" id="body_html" value="{{ old('body_html', $article->body_html ?? '') }}">

        <div id="article-editor" class="min-h-[700px] rounded-xl bg-white">
            {!! old('body_html', $article->body_html ?? '') !!}
        </div>

        @error('body_html')
        <p class="mt-2 text-sm font-semibold text-rose-600">{{ $message }}</p>
        @enderror
    </div>

    <div class="grid gap-6 md:grid-cols-2">
        <div>
            <label for="status" class="mb-2 block text-sm font-bold text-slate-700">
                公開状態
            </label>

            <select id="status" name="status"
                class="block w-full rounded-xl border-slate-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                <option value="1" @selected((string) old('status', $article->status ?? 1) === '1')>
                    下書き
                </option>
                <option value="2" @selected((string) old('status', $article->status ?? 1) === '2')>
                    公開
                </option>
                <option value="3" @selected((string) old('status', $article->status ?? 1) === '3')>
                    非公開
                </option>
            </select>

            @error('status')
            <p class="mt-2 text-sm font-semibold text-rose-600">{{ $message }}</p>
            @enderror
        </div>

        <div>
            <label for="published_at" class="mb-2 block text-sm font-bold text-slate-700">
                公開日時
            </label>

            <input type="datetime-local" id="published_at" name="published_at"
                value="{{ old('published_at', isset($article) && $article->published_at ? $article->published_at->format('Y-m-d\TH:i') : now()->format('Y-m-d\TH:i')) }}"
                class="block w-full rounded-xl border-slate-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">

            @error('published_at')
            <p class="mt-2 text-sm font-semibold text-rose-600">{{ $message }}</p>
            @enderror
        </div>
    </div>

    <div class="flex justify-end gap-3">
        <a href="{{ route('admin.articles.index') }}"
            class="inline-flex items-center justify-center rounded-xl border border-slate-300 bg-white px-5 py-3 text-sm font-bold text-slate-700 transition hover:bg-slate-50">
            戻る
        </a>

        <button type="submit"
            class="inline-flex items-center justify-center rounded-xl bg-indigo-600 px-5 py-3 text-sm font-bold text-white transition hover:bg-indigo-700">
            保存
        </button>
    </div>
</div>
@push('styles')
    <link href="https://cdn.jsdelivr.net/npm/quill@2/dist/quill.snow.css" rel="stylesheet">

    <style>
        #article-editor {
            min-height: 700px;
        }

        #article-editor .ql-editor {
            min-height: 700px;
            font-size: 16px;
            line-height: 1.9;
        }

        #article-editor .ql-container {
            min-height: 700px;
        }
    </style>
@endpush

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/quill@2/dist/quill.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function () {
            const hiddenInput = document.getElementById('body_html');
            const editorElement = document.getElementById('article-editor');

            const quill = new Quill(editorElement, {
                theme: 'snow',
                modules: {
                    toolbar: [
                        [{ header: [2, 3, false] }],
                        ['bold', 'italic', 'underline'],
                        [{ list: 'ordered' }, { list: 'bullet' }],
                        ['blockquote', 'link'],
                        ['clean']
                    ]
                }
            });

            const form = editorElement.closest('form');

            form.addEventListener('submit', function () {
                hiddenInput.value = quill.root.innerHTML;
            });
        });
</script>
@endpush
