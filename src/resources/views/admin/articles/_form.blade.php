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
    <div class="rounded-2xl bg-white p-6 shadow-sm ring-1 ring-slate-200">
        <h2 class="text-lg font-black text-slate-900">
            カテゴリー・タグ
        </h2>
    
        <div class="mt-5">
            <label for="article_category_id" class="block text-sm font-bold text-slate-700">
                カテゴリー
            </label>
    
            <select
                id="article_category_id"
                name="article_category_id"
                class="mt-2 block w-full rounded-xl border-slate-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
            >
                <option value="">カテゴリーを選択しない</option>
    
                @foreach ($categories as $category)
                    <option
                        value="{{ $category->id }}"
                        @selected(old('article_category_id', $article->article_category_id ?? null) == $category->id)
                    >
                        {{ $category->displayName() }}
                    </option>
                @endforeach
            </select>
    
            @error('article_category_id')
                <p class="mt-2 text-sm font-bold text-rose-600">
                    {{ $message }}
                </p>
            @enderror
        </div>
    
        <div class="mt-6">
            <p class="block text-sm font-bold text-slate-700">
                タグ
            </p>
    
            <div class="mt-3 grid gap-3 sm:grid-cols-2 lg:grid-cols-3">
                @foreach ($tags as $tag)
                    <label class="flex items-center gap-2 rounded-xl border border-slate-200 bg-slate-50 px-4 py-3 text-sm font-bold text-slate-700">
                        <input
                            type="checkbox"
                            name="tag_ids[]"
                            value="{{ $tag->id }}"
                            class="rounded border-slate-300 text-indigo-600 focus:ring-indigo-500"
                            @checked(in_array($tag->id, old('tag_ids', isset($article) ? $article->tags->pluck('id')->toArray() : [])))
                        >
    
                        <span>
                            #{{ $tag->name }}
                        </span>
                    </label>
                @endforeach
            </div>
    
            @error('tag_ids')
                <p class="mt-2 text-sm font-bold text-rose-600">
                    {{ $message }}
                </p>
            @enderror
        </div>
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
                <option value="{{ $prefecture->id }}" @selected((string) old('prefecture_id', $article->prefecture_id ?? '') === (string) $prefecture->id)>
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

        @if (!empty($article->thumbnail_path))
            <div class="mt-3">
                <img src="{{ asset('storage/' . $article->thumbnail_path) }}" alt="サムネイル画像"
                    class="h-32 rounded-xl object-cover">
            </div>
        @endif

        @error('thumbnail')
            <p class="mt-2 text-sm font-semibold text-rose-600">{{ $message }}</p>
        @enderror
    </div>

    {{-- 本文：Quill + HTML直接編集 --}}
    <div>
        <label class="mb-2 block text-sm font-bold text-slate-700">
            本文 <span class="text-rose-500">必須</span>
        </label>

        {{-- 送信用。HTMLは長くなるため hidden input ではなく textarea にしています --}}
        <textarea name="body_html" id="body_html" class="hidden">{{ old('body_html', $article->body_html ?? '') }}</textarea>

        <div class="mb-3 flex flex-wrap items-center gap-2">
            <button type="button" id="visual-mode-button"
                class="rounded-lg bg-indigo-600 px-4 py-2 text-sm font-bold text-white">
                ビジュアル編集
            </button>

            <button type="button" id="html-mode-button"
                class="rounded-lg border border-slate-300 bg-white px-4 py-2 text-sm font-bold text-slate-700">
                HTML編集
            </button>

            <p class="text-xs text-slate-500">
                ライターはビジュアル編集、WebデザイナーはHTML編集を使えます。
            </p>
        </div>

        <div id="visual-editor-area">
            <div id="article-editor" class="min-h-[600px] rounded-xl bg-white"></div>

            <p class="mt-2 text-xs leading-6 text-slate-500">
                見出し、太字、リスト、引用、リンク、文字色、背景色をツールバーから設定できます。
            </p>
        </div>

        <div id="html-editor-area" class="hidden">
            <textarea id="body_html_textarea" rows="30"
                class="block w-full rounded-xl border-slate-300 font-mono text-sm leading-7 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">{{ old('body_html', $article->body_html ?? '') }}</textarea>

            <p class="mt-2 text-xs leading-6 text-slate-500">
                HTMLを直接入力できます。HTML編集後にビジュアル編集へ戻すと、Quillが対応していないHTMLやstyleが一部変換・削除される場合があります。
            </p>
        </div>

        @error('body_html')
            <p class="mt-2 text-sm font-semibold text-rose-600">{{ $message }}</p>
        @enderror
    </div>

    {{-- 記事専用CSS --}}
    <div>
        <label for="body_css" class="mb-2 block text-sm font-bold text-slate-700">
            記事専用CSS
        </label>

        <textarea id="body_css" name="body_css" rows="14"
            placeholder=".article-body .lead { font-size: 18px; color: #0B1548; }"
            class="block w-full rounded-xl border-slate-300 font-mono text-sm leading-7 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">{{ old('body_css', $article->body_css ?? '') }}</textarea>

        <p class="mt-2 text-xs leading-6 text-slate-500">
            この記事だけに適用したいCSSを入力できます。基本は <code>.article-body</code> 配下に限定して書くのがおすすめです。
        </p>

        @error('body_css')
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
            min-height: 600px;
        }

        #article-editor .ql-editor {
            min-height: 600px;
            font-size: 16px;
            line-height: 1.9;
        }

        #article-editor .ql-container {
            min-height: 600px;
        }

        #article-editor .ql-toolbar {
            border-top-left-radius: 0.75rem;
            border-top-right-radius: 0.75rem;
        }

        #article-editor .ql-container {
            border-bottom-left-radius: 0.75rem;
            border-bottom-right-radius: 0.75rem;
        }

        #html-editor-area textarea,
        #body_css {
            tab-size: 2;
        }
    </style>
@endpush

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/quill@2/dist/quill.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const hiddenBodyHtml = document.getElementById('body_html');
        const editorElement = document.getElementById('article-editor');
        const form = editorElement.closest('form');

        const visualModeButton = document.getElementById('visual-mode-button');
        const htmlModeButton = document.getElementById('html-mode-button');
        const visualEditorArea = document.getElementById('visual-editor-area');
        const htmlEditorArea = document.getElementById('html-editor-area');
        const htmlTextarea = document.getElementById('body_html_textarea');

        let currentMode = 'visual';

        const quill = new Quill(editorElement, {
            theme: 'snow',
            modules: {
                toolbar: [
                    [{ header: [2, 3, false] }],
                    ['bold', 'italic', 'underline'],
                    [{ color: [] }, { background: [] }],
                    [{ list: 'ordered' }, { list: 'bullet' }],
                    ['blockquote', 'link'],
                    ['clean']
                ]
            }
        });

        /**
         * 初期表示時に、DBまたはoldの本文HTMLをQuillへ反映する
         * ここで反映することで、HTMLタグがそのまま表示される問題を避ける
         */
        quill.clipboard.dangerouslyPasteHTML(hiddenBodyHtml.value || '');

        function setActiveButton(activeButton, inactiveButton) {
            activeButton.classList.add('bg-indigo-600', 'text-white');
            activeButton.classList.remove('border', 'border-slate-300', 'bg-white', 'text-slate-700');

            inactiveButton.classList.remove('bg-indigo-600', 'text-white');
            inactiveButton.classList.add('border', 'border-slate-300', 'bg-white', 'text-slate-700');
        }

        function activateVisualMode() {
            if (currentMode === 'visual') {
                return;
            }

            // HTML編集欄の内容をQuillへ反映
            hiddenBodyHtml.value = htmlTextarea.value;
            quill.clipboard.dangerouslyPasteHTML(htmlTextarea.value || '');

            visualEditorArea.classList.remove('hidden');
            htmlEditorArea.classList.add('hidden');

            setActiveButton(visualModeButton, htmlModeButton);
            currentMode = 'visual';
        }

        function activateHtmlMode() {
            if (currentMode === 'html') {
                return;
            }

            // Quillの内容をHTML編集欄へ反映
            const html = quill.root.innerHTML;
            hiddenBodyHtml.value = html;
            htmlTextarea.value = html;

            visualEditorArea.classList.add('hidden');
            htmlEditorArea.classList.remove('hidden');

            setActiveButton(htmlModeButton, visualModeButton);
            currentMode = 'html';
        }

        visualModeButton.addEventListener('click', activateVisualMode);
        htmlModeButton.addEventListener('click', activateHtmlMode);

        form.addEventListener('submit', function () {
            if (currentMode === 'html') {
                hiddenBodyHtml.value = htmlTextarea.value;
                return;
            }

            hiddenBodyHtml.value = quill.root.innerHTML;
        });
    });
</script>
@endpush
