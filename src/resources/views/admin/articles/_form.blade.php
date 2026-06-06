@csrf

@php
    $selectedAuthorUser = old('author_user_id')
        ? $authorUsers->firstWhere('id', (int) old('author_user_id'))
        : ($article->authorUser ?? null);

    $selectedAuthorProfile = $selectedAuthorUser?->profile;

    $selectedAuthorName = $selectedAuthorProfile?->display_name
        ?: $selectedAuthorUser?->name
        ?: '未選択';

    $selectedAuthorAvatar = $selectedAuthorProfile?->avatar_path
        ? asset('storage/' . $selectedAuthorProfile->avatar_path)
        : asset('images/default-avatar.png');
@endphp

<div class="space-y-8">
    {{-- 著者ユーザー --}}
    <section class="rounded-2xl bg-slate-50 p-5 ring-1 ring-slate-200">
        <div class="flex items-start justify-between gap-4">
            <div>
                <h2 class="text-lg font-bold text-slate-900">
                    著者ユーザー
                </h2>

                <p class="mt-1 text-sm leading-6 text-slate-600">
                    記事に表示するユーザーを選択できます。未選択の場合は「YomuWorks編集部」として表示されます。
                </p>
            </div>

            <button
                type="button"
                class="shrink-0 rounded-xl bg-indigo-600 px-4 py-2 text-sm font-bold text-white hover:bg-indigo-700"
                data-author-modal-open
            >
                ユーザーを選択
            </button>
        </div>

        <input
            type="hidden"
            name="author_user_id"
            id="author_user_id"
            value="{{ old('author_user_id', $article->author_user_id) }}"
        >

        <div class="mt-4 flex items-center gap-4 rounded-2xl bg-white p-4 ring-1 ring-slate-200">
            <img
                id="selected_author_avatar"
                src="{{ $selectedAuthorAvatar }}"
                alt="著者アイコン"
                class="h-14 w-14 rounded-full object-cover ring-1 ring-slate-200"
            >

            <div class="min-w-0 flex-1">
                <p id="selected_author_name" class="truncate text-sm font-bold text-slate-900">
                    {{ $selectedAuthorName }}
                </p>

                <p id="selected_author_email" class="mt-1 truncate text-xs text-slate-500">
                    {{ $selectedAuthorUser?->email ?? 'ユーザーを選択してください' }}
                </p>
            </div>
        </div>

        @error('author_user_id')
            <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
        @enderror
    </section>

    {{-- 基本情報 --}}
    <section>
        <h2 class="text-lg font-bold text-slate-900">
            基本情報
        </h2>

        <div class="mt-5 grid gap-5">
            <div>
                <label for="title" class="block text-sm font-bold text-slate-700">
                    記事タイトル
                </label>

                <input
                    id="title"
                    type="text"
                    name="title"
                    value="{{ old('title', $article->title) }}"
                    class="mt-2 w-full rounded-xl border-slate-300"
                    required
                >

                @error('title')
                    <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <div class="grid gap-5 md:grid-cols-2">
                <div>
                    <label for="slug" class="block text-sm font-bold text-slate-700">
                        通常スラッグ
                    </label>

                    <input
                        id="slug"
                        type="text"
                        name="slug"
                        value="{{ old('slug', $article->slug) }}"
                        class="mt-2 w-full rounded-xl border-slate-300"
                        placeholder="例：remote-work-loneliness"
                    >

                    @error('slug')
                        <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="short_slug" class="block text-sm font-bold text-slate-700">
                        短縮URL
                    </label>

                    <input
                        id="short_slug"
                        type="text"
                        name="short_slug"
                        value="{{ old('short_slug', $article->short_slug) }}"
                        class="mt-2 w-full rounded-xl border-slate-300"
                        placeholder="例：no"
                    >

                    @error('short_slug')
                        <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <div>
                <label for="excerpt" class="block text-sm font-bold text-slate-700">
                    記事概要
                </label>

                <textarea
                    id="excerpt"
                    name="excerpt"
                    rows="4"
                    class="mt-2 w-full rounded-xl border-slate-300"
                >{{ old('excerpt', $article->excerpt) }}</textarea>

                @error('excerpt')
                    <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <div class="grid gap-5 md:grid-cols-3">
                <div>
                    <label for="reading_minutes" class="block text-sm font-bold text-slate-700">
                        読了時間（分）
                    </label>

                    <input
                        id="reading_minutes"
                        type="number"
                        name="reading_minutes"
                        min="1"
                        max="120"
                        value="{{ old('reading_minutes', $article->reading_minutes ?? 3) }}"
                        class="mt-2 w-full rounded-xl border-slate-300"
                        required
                    >

                    @error('reading_minutes')
                        <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="category_id" class="block text-sm font-bold text-slate-700">
                        カテゴリー
                    </label>

                    <select
                        id="category_id"
                        name="category_id"
                        class="mt-2 w-full rounded-xl border-slate-300"
                    >
                        <option value="">未選択</option>
                        @foreach (($categories ?? collect()) as $category)
                            <option value="{{ $category->id }}" @selected((string) old('category_id', $article->category_id) === (string) $category->id)>
                                {{ $category->name }}
                            </option>
                        @endforeach
                    </select>

                    @error('category_id')
                        <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="prefecture_id" class="block text-sm font-bold text-slate-700">
                        対象地域
                    </label>

                    <select
                        id="prefecture_id"
                        name="prefecture_id"
                        class="mt-2 w-full rounded-xl border-slate-300"
                    >
                        <option value="">全国向け</option>
                        @foreach (($prefectures ?? collect()) as $prefecture)
                            <option value="{{ $prefecture->id }}" @selected((string) old('prefecture_id', $article->prefecture_id) === (string) $prefecture->id)>
                                {{ $prefecture->name }}
                            </option>
                        @endforeach
                    </select>

                    @error('prefecture_id')
                        <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <div>
                <label for="thumbnail" class="block text-sm font-bold text-slate-700">
                    サムネイル画像
                </label>

                <input
                    id="thumbnail"
                    type="file"
                    name="thumbnail"
                    accept="image/*"
                    class="mt-2 w-full rounded-xl border border-slate-300 p-3"
                >

                @if ($article->thumbnail_path)
                    <img
                        src="{{ asset('storage/' . $article->thumbnail_path) }}"
                        alt="{{ $article->title }}"
                        class="mt-4 max-h-60 rounded-2xl object-cover"
                    >
                @endif

                @error('thumbnail')
                    <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>
        </div>
    </section>

    {{-- 本文 --}}
    <section>
        <h2 class="text-lg font-bold text-slate-900">
            本文
        </h2>

        <div class="mt-5 grid gap-5">
            <div>
                <label for="body_html" class="block text-sm font-bold text-slate-700">
                    本文HTML
                </label>

                <textarea
                    id="body_html"
                    name="body_html"
                    rows="18"
                    class="mt-2 w-full rounded-xl border-slate-300 font-mono text-sm"
                >{{ old('body_html', $article->body_html) }}</textarea>

                @error('body_html')
                    <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label for="body_css" class="block text-sm font-bold text-slate-700">
                    記事専用CSS
                </label>

                <textarea
                    id="body_css"
                    name="body_css"
                    rows="10"
                    class="mt-2 w-full rounded-xl border-slate-300 font-mono text-sm"
                >{{ old('body_css', $article->body_css) }}</textarea>

                @error('body_css')
                    <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>
        </div>
    </section>

    {{-- SEO --}}
    <section>
        <h2 class="text-lg font-bold text-slate-900">
            SEO設定
        </h2>

        <div class="mt-5 grid gap-5">
            <div>
                <label for="seo_title" class="block text-sm font-bold text-slate-700">
                    SEOタイトル
                </label>

                <input
                    id="seo_title"
                    type="text"
                    name="seo_title"
                    value="{{ old('seo_title', $article->seo_title) }}"
                    class="mt-2 w-full rounded-xl border-slate-300"
                >

                @error('seo_title')
                    <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label for="h1_title" class="block text-sm font-bold text-slate-700">
                    H1見出し
                </label>

                <input
                    id="h1_title"
                    type="text"
                    name="h1_title"
                    value="{{ old('h1_title', $article->h1_title) }}"
                    class="mt-2 w-full rounded-xl border-slate-300"
                >

                @error('h1_title')
                    <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label for="seo_description_text" class="block text-sm font-bold text-slate-700">
                    SEOディスクリプション
                </label>

                <textarea
                    id="seo_description_text"
                    name="seo_description_text"
                    rows="4"
                    class="mt-2 w-full rounded-xl border-slate-300"
                >{{ old('seo_description_text', $article->seo_description_text ?? $article->seo_description) }}</textarea>

                @error('seo_description_text')
                    <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>
        </div>
    </section>

    {{-- 公開設定 --}}
    <section>
        <h2 class="text-lg font-bold text-slate-900">
            公開設定
        </h2>

        <div class="mt-5 grid gap-5 md:grid-cols-2">
            <div>
                <label for="status" class="block text-sm font-bold text-slate-700">
                    公開状態
                </label>

                <select
                    id="status"
                    name="status"
                    class="mt-2 w-full rounded-xl border-slate-300"
                    required
                >
                    <option value="1" @selected((string) old('status', $article->status ?? 1) === '1')>下書き</option>
                    <option value="2" @selected((string) old('status', $article->status ?? 1) === '2')>公開</option>
                    <option value="3" @selected((string) old('status', $article->status ?? 1) === '3')>非公開</option>
                </select>

                @error('status')
                    <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label for="published_at" class="block text-sm font-bold text-slate-700">
                    公開日時
                </label>

                <input
                    id="published_at"
                    type="datetime-local"
                    name="published_at"
                    value="{{ old('published_at', $article->published_at ? $article->published_at->format('Y-m-d\TH:i') : now()->format('Y-m-d\TH:i')) }}"
                    class="mt-2 w-full rounded-xl border-slate-300"
                >

                @error('published_at')
                    <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>
        </div>
    </section>

    {{-- タグ --}}
    @if (isset($tags))
        <section>
            <h2 class="text-lg font-bold text-slate-900">
                タグ
            </h2>

            <div class="mt-5 flex flex-wrap gap-3">
                @foreach ($tags as $tag)
                    <label class="inline-flex items-center gap-2 rounded-full bg-slate-100 px-4 py-2 text-sm font-bold text-slate-700">
                        <input
                            type="checkbox"
                            name="tag_ids[]"
                            value="{{ $tag->id }}"
                            @checked(in_array($tag->id, old('tag_ids', $article->tags?->pluck('id')->all() ?? [])))
                        >
                        #{{ $tag->name }}
                    </label>
                @endforeach
            </div>
        </section>
    @endif

    <div class="flex flex-col-reverse gap-3 border-t border-slate-200 pt-6 sm:flex-row sm:items-center sm:justify-between">
        <a
            href="{{ route('admin.articles.index') }}"
            class="inline-flex items-center justify-center rounded-xl border border-slate-300 bg-white px-5 py-3 text-sm font-bold text-slate-700 hover:bg-slate-50"
        >
            一覧へ戻る
        </a>

        <button
            type="submit"
            class="inline-flex items-center justify-center rounded-xl bg-indigo-600 px-6 py-3 text-sm font-bold text-white hover:bg-indigo-700"
        >
            保存する
        </button>
    </div>
</div>

{{-- 著者ユーザー選択モーダル --}}
<div
    class="fixed inset-0 z-50 hidden items-center justify-center bg-black/40 p-4"
    data-author-modal
>
    <div class="max-h-[85vh] w-full max-w-3xl overflow-hidden rounded-2xl bg-white shadow-xl">
        <div class="flex items-center justify-between border-b border-slate-200 px-5 py-4">
            <div>
                <h2 class="text-lg font-bold text-slate-900">
                    著者ユーザーを選択
                </h2>

                <p class="mt-1 text-sm text-slate-500">
                    記事に表示するユーザーを選択してください。
                </p>
            </div>

            <button
                type="button"
                class="rounded-full bg-slate-100 px-3 py-1 text-lg font-bold text-slate-600 hover:bg-slate-200"
                data-author-modal-close
            >
                ×
            </button>
        </div>

        <div class="max-h-[65vh] overflow-y-auto p-5">
            <div class="grid gap-3">
                <button
                    type="button"
                    class="flex w-full items-center gap-4 rounded-2xl border border-slate-200 bg-white p-4 text-left transition hover:border-indigo-300 hover:bg-indigo-50"
                    data-author-clear
                >
                    <img
                        src="{{ asset('images/default-avatar.png') }}"
                        alt="YomuWorks編集部"
                        class="h-12 w-12 rounded-full object-cover ring-1 ring-slate-200"
                    >

                    <div class="min-w-0 flex-1">
                        <p class="truncate text-sm font-bold text-slate-900">
                            YomuWorks編集部
                        </p>

                        <p class="mt-1 truncate text-xs text-slate-500">
                            ユーザーを選択しない
                        </p>
                    </div>

                    <span class="rounded-full bg-slate-700 px-3 py-1 text-xs font-bold text-white">
                        選択
                    </span>
                </button>

                @foreach (($authorUsers ?? collect()) as $user)
                    @php
                        $profile = $user->profile;
                        $displayName = $profile?->display_name ?: $user->name;
                        $avatarUrl = $profile?->avatar_path
                            ? asset('storage/' . $profile->avatar_path)
                            : asset('images/default-avatar.png');
                    @endphp

                    <button
                        type="button"
                        class="flex w-full items-center gap-4 rounded-2xl border border-slate-200 bg-white p-4 text-left transition hover:border-indigo-300 hover:bg-indigo-50"
                        data-author-select
                        data-author-id="{{ $user->id }}"
                        data-author-name="{{ $displayName }}"
                        data-author-email="{{ $user->email }}"
                        data-author-avatar="{{ $avatarUrl }}"
                    >
                        <img
                            src="{{ $avatarUrl }}"
                            alt="{{ $displayName }}"
                            class="h-12 w-12 rounded-full object-cover ring-1 ring-slate-200"
                        >

                        <div class="min-w-0 flex-1">
                            <p class="truncate text-sm font-bold text-slate-900">
                                {{ $displayName }}
                            </p>

                            <p class="mt-1 truncate text-xs text-slate-500">
                                {{ $user->email }}
                            </p>
                        </div>

                        <span class="rounded-full bg-indigo-600 px-3 py-1 text-xs font-bold text-white">
                            選択
                        </span>
                    </button>
                @endforeach
            </div>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        const modal = document.querySelector('[data-author-modal]');
        const openButton = document.querySelector('[data-author-modal-open]');
        const closeButton = document.querySelector('[data-author-modal-close]');
        const selectButtons = document.querySelectorAll('[data-author-select]');
        const clearButton = document.querySelector('[data-author-clear]');

        const authorInput = document.getElementById('author_user_id');
        const selectedAuthorAvatar = document.getElementById('selected_author_avatar');
        const selectedAuthorName = document.getElementById('selected_author_name');
        const selectedAuthorEmail = document.getElementById('selected_author_email');

        if (!modal || !openButton || !closeButton || !authorInput) {
            return;
        }

        function closeModal() {
            modal.classList.add('hidden');
            modal.classList.remove('flex');
        }

        openButton.addEventListener('click', function () {
            modal.classList.remove('hidden');
            modal.classList.add('flex');
        });

        closeButton.addEventListener('click', closeModal);

        modal.addEventListener('click', function (event) {
            if (event.target === modal) {
                closeModal();
            }
        });

        if (clearButton) {
            clearButton.addEventListener('click', function () {
                authorInput.value = '';
                selectedAuthorAvatar.src = '{{ asset('images/default-avatar.png') }}';
                selectedAuthorName.textContent = 'YomuWorks編集部';
                selectedAuthorEmail.textContent = 'ユーザーを選択しない';
                closeModal();
            });
        }

        selectButtons.forEach(function (button) {
            button.addEventListener('click', function () {
                authorInput.value = button.dataset.authorId;
                selectedAuthorAvatar.src = button.dataset.authorAvatar;
                selectedAuthorName.textContent = button.dataset.authorName;
                selectedAuthorEmail.textContent = button.dataset.authorEmail;
                closeModal();
            });
        });
    });
</script>
