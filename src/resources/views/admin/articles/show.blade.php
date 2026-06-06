@extends('layouts.admin')

@section('title', '記事詳細')

@push('styles')
    <style>
        /*
         * 管理者詳細画面でも、実際の記事本文に近い見た目で確認するための基本CSS
         */
        .article-preview-body {
            color: #334155;
            font-size: 16px;
            line-height: 1.95;
        }

        .article-preview-body h2 {
            margin-top: 48px;
            margin-bottom: 20px;
            padding-left: 16px;
            border-left: 5px solid #C9825D;
            color: #0B1548;
            font-size: 28px;
            font-weight: 900;
            line-height: 1.5;
        }

        .article-preview-body h3 {
            margin-top: 36px;
            margin-bottom: 16px;
            padding-left: 14px;
            border-left: 4px solid #6F8FAF;
            color: #0B1548;
            font-size: 22px;
            font-weight: 900;
            line-height: 1.6;
        }

        .article-preview-body p {
            margin-top: 18px;
        }

        .article-preview-body ul,
        .article-preview-body ol {
            margin-top: 20px;
            padding-left: 1.5em;
        }

        .article-preview-body ul {
            list-style-type: disc;
        }

        .article-preview-body ol {
            list-style-type: decimal;
        }

        .article-preview-body li {
            margin-top: 10px;
            line-height: 1.9;
        }

        .article-preview-body blockquote {
            margin: 32px 0;
            padding: 20px 24px;
            border-left: 5px solid #6F8FAF;
            background: #F7F3EA;
            color: #334155;
            font-weight: 700;
        }

        .article-preview-body a {
            color: #4F46E5;
            font-weight: 700;
            text-decoration: underline;
            text-underline-offset: 3px;
        }

        .article-preview-body img {
            max-width: 100%;
            height: auto;
            border-radius: 16px;
        }
    </style>

    @if (!empty($article->body_css))
        <style>
            /*
             * 記事専用CSSを管理画面プレビューにも反映する
             * show.blade.php側では .article-body として表示している想定のCSSも効くように、
             * プレビュー側の本文にも article-body クラスを付けています。
             */
            {!! $article->body_css !!}
        </style>
    @endif
@endpush

@section('content')
<div class="min-h-screen bg-slate-50">
    <div class="mx-auto max-w-7xl px-4 py-10 sm:px-6 lg:px-8">
        <div class="mb-8 flex flex-col gap-4 sm:flex-row sm:items-end sm:justify-between">
            <div>
                <p class="text-sm font-bold text-indigo-600">
                    ARTICLE DETAIL
                </p>

                <h1 class="mt-2 text-3xl font-bold text-slate-900">
                    記事詳細
                </h1>

                <p class="mt-3 text-slate-600">
                    作成した記事の内容・SEO設定・公開状態・本文デザインを確認できます。
                </p>
            </div>

            <div class="flex flex-wrap gap-3">
                <a
                    href="{{ route('admin.articles.index') }}"
                    class="inline-flex items-center justify-center rounded-xl border border-slate-300 bg-white px-5 py-3 text-sm font-bold text-slate-700 transition hover:bg-slate-50"
                >
                    一覧へ戻る
                </a>

                <a
                    href="{{ route('admin.articles.edit', $article) }}"
                    class="inline-flex items-center justify-center rounded-xl bg-indigo-600 px-5 py-3 text-sm font-bold text-white transition hover:bg-indigo-700"
                >
                    編集する
                </a>
            </div>
        </div>

        @php
            $statusLabel = match ((int) $article->status) {
                1 => '下書き',
                2 => '公開',
                3 => '非公開',
                default => '不明',
            };

            $statusClass = match ((int) $article->status) {
                1 => 'bg-slate-100 text-slate-700',
                2 => 'bg-emerald-50 text-emerald-700',
                3 => 'bg-rose-50 text-rose-700',
                default => 'bg-slate-100 text-slate-700',
            };

            $publicUrl = $article->short_slug
                ? route('articles.short-show', $article->short_slug)
                : route('articles.show', $article);
        @endphp

        <div class="grid gap-6 lg:grid-cols-[minmax(0,1fr)_340px]">
            {{-- Main --}}
            <div class="space-y-6">
                <div class="rounded-2xl bg-white p-6 shadow-sm ring-1 ring-slate-200 sm:p-8">
                    <div class="mb-4 flex flex-wrap gap-2">
                        <span class="inline-flex rounded-full px-3 py-1 text-xs font-bold {{ $statusClass }}">
                            {{ $statusLabel }}
                        </span>

                        @if ($article->prefecture)
                            <span class="inline-flex rounded-full bg-emerald-50 px-3 py-1 text-xs font-bold text-emerald-700">
                                {{ $article->prefecture->name }}
                            </span>
                        @else
                            <span class="inline-flex rounded-full bg-slate-100 px-3 py-1 text-xs font-bold text-slate-600">
                                全国向け
                            </span>
                        @endif
                    </div>

                    <h2 class="text-2xl font-bold leading-tight text-slate-900">
                        {{ $article->title }}
                    </h2>

                    @if ($article->excerpt)
                        <p class="mt-4 rounded-xl bg-slate-50 p-4 text-sm leading-7 text-slate-700">
                            {{ $article->excerpt }}
                        </p>
                    @endif

                    @if ($article->thumbnail_path)
                        <div class="mt-6">
                            <img
                                src="{{ asset('storage/' . $article->thumbnail_path) }}"
                                alt="{{ $article->title }}"
                                class="w-full rounded-2xl object-cover"
                            >
                        </div>
                    @endif
                </div>

                {{-- 実際の記事プレビュー --}}
                <div class="overflow-hidden rounded-2xl bg-white shadow-sm ring-1 ring-slate-200">
                    <div class="border-b border-slate-200 bg-slate-900 px-6 py-4 sm:px-8">
                        <div class="flex flex-col gap-2 sm:flex-row sm:items-center sm:justify-between">
                            <div>
                                <p class="text-xs font-bold tracking-[0.18em] text-indigo-200">
                                    ARTICLE PREVIEW
                                </p>

                                <h3 class="mt-1 text-lg font-bold text-white">
                                    実際の記事表示プレビュー
                                </h3>
                            </div>

                            @if ((int) $article->status === 2)
                                <a
                                    href="{{ $publicUrl }}"
                                    target="_blank"
                                    rel="noopener noreferrer"
                                    class="inline-flex w-fit items-center justify-center rounded-xl bg-white px-4 py-2 text-xs font-bold text-slate-900 transition hover:bg-slate-100"
                                >
                                    公開ページを別タブで確認
                                </a>
                            @endif
                        </div>
                    </div>

                    <article class="px-6 py-8 sm:px-10">
                        <div class="mb-4 flex flex-wrap gap-2">
                            <span class="rounded-full bg-indigo-50 px-3 py-1 text-xs font-bold text-indigo-700">
                                記事
                            </span>

                            @if ($article->prefecture)
                                <span class="rounded-full bg-emerald-50 px-3 py-1 text-xs font-bold text-emerald-700">
                                    {{ $article->prefecture->name }}
                                </span>
                            @endif
                        </div>

                        <h1 class="text-3xl font-bold leading-tight text-slate-900">
                            {{ $article->h1_title ?: $article->title }}
                        </h1>

                        <p class="mt-4 text-sm text-slate-500">
                            公開日：{{ $article->published_at?->format('Y/m/d') ?? '未設定' }}
                        </p>

                        @if ($article->thumbnail_path)
                            <img
                                src="{{ asset('storage/' . $article->thumbnail_path) }}"
                                alt="{{ $article->title }}"
                                class="mt-8 w-full rounded-2xl object-cover"
                            >
                        @endif

                        @if ($article->excerpt)
                            <p class="mt-8 rounded-xl bg-slate-50 p-4 leading-7 text-slate-700">
                                {{ $article->excerpt }}
                            </p>
                        @endif

                        <div class="article-body article-preview-body mt-8">
                            {!! $article->body_html !!}
                        </div>

                        <div class="mt-10 rounded-2xl bg-indigo-50 p-5">
                            <p class="font-bold text-indigo-900">
                                作業仲間を探してみませんか？
                            </p>

                            <p class="mt-2 text-sm leading-7 text-indigo-800">
                                MokuMoku Matchでは、フルリモートで働く人や学習中の人が、黙々作業・勉強・情報交換できる相手を探せます。
                            </p>

                            <div class="mt-4">
                                <a
                                    href="{{ route('home') }}"
                                    class="inline-flex items-center justify-center rounded-xl bg-indigo-600 px-5 py-3 text-sm font-bold text-white transition hover:bg-indigo-700"
                                >
                                    募集を見る
                                </a>
                            </div>
                        </div>
                    </article>
                </div>

                {{-- HTML/CSS確認用 --}}
                <div class="grid gap-6 xl:grid-cols-2">
                    <div class="rounded-2xl bg-white p-6 shadow-sm ring-1 ring-slate-200">
                        <h3 class="text-lg font-bold text-slate-900">
                            保存されているHTML
                        </h3>

                        <pre class="mt-5 max-h-[500px] overflow-auto rounded-xl bg-slate-950 p-4 text-xs leading-6 text-slate-100"><code>{{ $article->body_html }}</code></pre>
                    </div>

                    <div class="rounded-2xl bg-white p-6 shadow-sm ring-1 ring-slate-200">
                        <h3 class="text-lg font-bold text-slate-900">
                            保存されている記事専用CSS
                        </h3>

                        <pre class="mt-5 max-h-[500px] overflow-auto rounded-xl bg-slate-950 p-4 text-xs leading-6 text-slate-100"><code>{{ $article->body_css ?: '記事専用CSSは未設定です。' }}</code></pre>
                    </div>
                </div>
            </div>

            {{-- Side --}}
            <div class="space-y-6">
                <div class="rounded-2xl bg-white p-6 shadow-sm ring-1 ring-slate-200">
                    <h3 class="text-lg font-bold text-slate-900">
                        公開情報
                    </h3>

                    <dl class="mt-5 space-y-4 text-sm">
                        <div>
                            <dt class="font-bold text-slate-500">公開状態</dt>
                            <dd class="mt-1">
                                <span class="inline-flex rounded-full px-3 py-1 text-xs font-bold {{ $statusClass }}">
                                    {{ $statusLabel }}
                                </span>
                            </dd>
                        </div>

                        <div>
                            <dt class="font-bold text-slate-500">公開日時</dt>
                            <dd class="mt-1 text-slate-800">
                                {{ $article->published_at ? $article->published_at->format('Y/m/d H:i') : '未設定' }}
                            </dd>
                        </div>

                        <div>
                            <dt class="font-bold text-slate-500">作成管理者</dt>
                            <dd class="mt-1 text-slate-800">
                                {{ $article->admin?->name ?? '未設定' }}
                            </dd>
                        </div>

                        <div>
                            <dt class="font-bold text-slate-500">作成日時</dt>
                            <dd class="mt-1 text-slate-800">
                                {{ $article->created_at?->format('Y/m/d H:i') }}
                            </dd>
                        </div>

                        <div>
                            <dt class="font-bold text-slate-500">更新日時</dt>
                            <dd class="mt-1 text-slate-800">
                                {{ $article->updated_at?->format('Y/m/d H:i') }}
                            </dd>
                        </div>
                    </dl>
                </div>

                <div class="rounded-2xl bg-white p-6 shadow-sm ring-1 ring-slate-200">
                    <h3 class="text-lg font-bold text-slate-900">
                        URL
                    </h3>

                    <div class="mt-5 space-y-4 text-sm">
                        <div>
                            <p class="font-bold text-slate-500">通常URL</p>
                            <p class="mt-1 break-all text-indigo-700">
                                /articles/{{ $article->slug }}
                            </p>
                        </div>

                        <div>
                            <p class="font-bold text-slate-500">短縮URL</p>
                            <p class="mt-1 break-all text-indigo-700">
                                {{ $article->short_slug ? '/' . $article->short_slug : '未設定' }}
                            </p>
                        </div>

                        @if ((int) $article->status === 2)
                            <a
                                href="{{ $publicUrl }}"
                                target="_blank"
                                rel="noopener noreferrer"
                                class="inline-flex w-full items-center justify-center rounded-xl border border-slate-300 bg-white px-4 py-3 text-sm font-bold text-slate-700 transition hover:bg-slate-50"
                            >
                                公開ページを見る
                            </a>
                        @endif
                    </div>
                </div>

                <div class="rounded-2xl bg-white p-6 shadow-sm ring-1 ring-slate-200">
                    <h3 class="text-lg font-bold text-slate-900">
                        SEO設定
                    </h3>

                    <dl class="mt-5 space-y-4 text-sm">
                        <div>
                            <dt class="font-bold text-slate-500">SEOタイトル</dt>
                            <dd class="mt-1 leading-6 text-slate-800">
                                {{ $article->seo_title ?: '未設定' }}
                            </dd>
                        </div>

                        <div>
                            <dt class="font-bold text-slate-500">H1見出し</dt>
                            <dd class="mt-1 leading-6 text-slate-800">
                                {{ $article->h1_title ?: '未設定' }}
                            </dd>
                        </div>

                        <div>
                            <dt class="font-bold text-slate-500">SEOディスクリプション</dt>
                            <dd class="mt-1 leading-6 text-slate-800">
                                {{ $article->seo_description ?: '未設定' }}
                            </dd>
                        </div>
                    </dl>
                </div>

                <div class="rounded-2xl bg-white p-6 shadow-sm ring-1 ring-slate-200">
                    <div class="flex items-start justify-between gap-4">
                        <div>
                            <h3 class="text-lg font-bold text-slate-900">
                                検索確認キーワード候補
                            </h3>
                
                            <p class="mt-2 text-sm leading-6 text-slate-600">
                                Google検索で記事を確認するときに使いやすいキーワード候補です。
                                上にあるほど確認しやすく、下にいくほど一般検索に近い候補です。
                            </p>
                        </div>
                
                        <span class="shrink-0 rounded-full bg-indigo-50 px-3 py-1 text-xs font-bold text-indigo-700">
                            SEO確認用
                        </span>
                    </div>
                
                    <div class="mt-5 space-y-3">
                        @forelse (($seoKeywordSuggestions ?? []) as $index => $suggestion)
                            @php
                                $keyword = $suggestion['keyword'] ?? '';
                                $googleSearchUrl = 'https://www.google.com/search?q=' . urlencode($keyword);
                
                                $strengthClass = match ($suggestion['strength'] ?? '') {
                                    '高' => 'bg-emerald-50 text-emerald-700',
                                    '中' => 'bg-amber-50 text-amber-700',
                                    '低' => 'bg-slate-100 text-slate-600',
                                    default => 'bg-slate-100 text-slate-600',
                                };
                            @endphp
                
                            <div class="rounded-2xl border border-slate-200 bg-slate-50 p-4">
                                <div class="flex items-start justify-between gap-3">
                                    <div class="min-w-0">
                                        <div class="flex flex-wrap items-center gap-2">
                                            <span class="flex h-7 w-7 items-center justify-center rounded-full bg-indigo-600 text-xs font-black text-white">
                                                {{ $index + 1 }}
                                            </span>
                
                                            <span class="text-sm font-bold text-slate-900">
                                                {{ $suggestion['label'] ?? '検索候補' }}
                                            </span>
                
                                            <span class="rounded-full px-2.5 py-1 text-xs font-bold {{ $strengthClass }}">
                                                {{ $suggestion['strength'] ?? '-' }}
                                            </span>
                                        </div>
                
                                        <p class="mt-3 break-all rounded-xl bg-white px-3 py-2 text-sm font-bold leading-6 text-indigo-700 ring-1 ring-slate-200">
                                            {{ $keyword }}
                                        </p>
                
                                        <p class="mt-2 text-xs leading-5 text-slate-500">
                                            {{ $suggestion['note'] ?? '' }}
                                        </p>
                                    </div>
                
                                    <a
                                        href="{{ $googleSearchUrl }}"
                                        target="_blank"
                                        rel="noopener noreferrer"
                                        class="shrink-0 rounded-xl bg-slate-900 px-3 py-2 text-xs font-bold text-white transition hover:bg-slate-700"
                                    >
                                        検索
                                    </a>
                                </div>
                            </div>
                        @empty
                            <p class="rounded-xl bg-slate-50 p-4 text-sm text-slate-500">
                                キーワード候補を作成できませんでした。
                            </p>
                        @endforelse
                    </div>
                
                    <div class="mt-5 rounded-2xl bg-amber-50 p-4 text-xs leading-6 text-amber-800">
                        <p class="font-bold">
                            注意
                        </p>
                        <p class="mt-1">
                            ここに表示されるキーワードは、Google検索で確認しやすくするための候補です。
                            検索結果への表示や上位表示を保証するものではありません。
                            公開直後は、Google Search ConsoleのURL検査でインデックス状況を確認してください。
                        </p>
                    </div>
                </div>
                <div class="rounded-2xl bg-white p-6 shadow-sm ring-1 ring-slate-200">
                    <h3 class="text-lg font-bold text-rose-700">
                        削除
                    </h3>

                    <p class="mt-3 text-sm leading-6 text-slate-600">
                        削除すると一覧やユーザー画面には表示されません。
                    </p>

                    <form
                        method="POST"
                        action="{{ route('admin.articles.destroy', $article) }}"
                        class="mt-5"
                        onsubmit="return confirm('この記事を削除しますか？');"
                    >
                        @csrf
                        @method('DELETE')

                        <button
                            type="submit"
                            class="inline-flex w-full items-center justify-center rounded-xl bg-rose-600 px-4 py-3 text-sm font-bold text-white transition hover:bg-rose-700"
                        >
                            削除する
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
