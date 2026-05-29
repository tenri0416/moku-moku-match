@extends('layouts.admin')

@section('title', '記事詳細')

@section('content')
<div class="min-h-screen bg-slate-50">
    <div class="mx-auto max-w-5xl px-4 py-10 sm:px-6 lg:px-8">
        <div class="mb-8 flex flex-col gap-4 sm:flex-row sm:items-end sm:justify-between">
            <div>
                <p class="text-sm font-bold text-indigo-600">
                    ARTICLE DETAIL
                </p>

                <h1 class="mt-2 text-3xl font-bold text-slate-900">
                    記事詳細
                </h1>

                <p class="mt-3 text-slate-600">
                    作成した記事の内容・SEO設定・公開状態を確認できます。
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

        <div class="grid gap-6 lg:grid-cols-3">
            {{-- Main --}}
            <div class="space-y-6 lg:col-span-2">
                <div class="rounded-2xl bg-white p-6 shadow-sm ring-1 ring-slate-200">
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

                <div class="rounded-2xl bg-white p-6 shadow-sm ring-1 ring-slate-200">
                    <h3 class="text-lg font-bold text-slate-900">
                        本文プレビュー
                    </h3>

                    <div class="article-body mt-5 leading-8 text-slate-700">
                        {!! $article->body_html !!}
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
