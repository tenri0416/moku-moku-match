@extends('layouts.admin')

@section('title', '記事管理')

@section('content')
<div class="min-h-screen bg-slate-50">
    <div class="mx-auto max-w-7xl px-4 py-10 sm:px-6 lg:px-8">
        <div class="mb-8 flex flex-col gap-4 sm:flex-row sm:items-end sm:justify-between">
            <div>
                <p class="text-sm font-bold text-indigo-600">
                    ARTICLES
                </p>

                <h1 class="mt-2 text-3xl font-bold text-slate-900">
                    記事管理
                </h1>

                <p class="mt-3 text-slate-600">
                    SEO記事の作成・編集・削除・公開管理を行います。
                </p>
            </div>

            <a
                href="{{ route('admin.articles.create') }}"
                class="inline-flex items-center justify-center rounded-xl bg-indigo-600 px-5 py-3 text-sm font-bold text-white shadow-sm transition hover:bg-indigo-700"
            >
                記事を作成する
            </a>
        </div>

        @if (session('success'))
            <div class="mb-6 rounded-2xl border border-emerald-200 bg-emerald-50 px-5 py-4 text-sm font-bold text-emerald-700">
                {{ session('success') }}
            </div>
        @endif

        <div class="mb-6 rounded-2xl bg-white p-5 shadow-sm ring-1 ring-slate-200">
            <form method="GET" action="{{ route('admin.articles.index') }}" class="grid gap-4 md:grid-cols-4">
                <div class="md:col-span-2">
                    <label for="keyword" class="mb-2 block text-sm font-bold text-slate-700">
                        キーワード
                    </label>

                    <input
                        type="text"
                        id="keyword"
                        name="keyword"
                        value="{{ request('keyword') }}"
                        placeholder="記事タイトル・スラッグで検索"
                        class="block w-full rounded-xl border-slate-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                    >
                </div>

                <div>
                    <label for="status" class="mb-2 block text-sm font-bold text-slate-700">
                        公開状態
                    </label>

                    <select
                        id="status"
                        name="status"
                        class="block w-full rounded-xl border-slate-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                    >
                        <option value="">すべて</option>
                        <option value="1" @selected((string) request('status') === '1')>下書き</option>
                        <option value="2" @selected((string) request('status') === '2')>公開</option>
                        <option value="3" @selected((string) request('status') === '3')>非公開</option>
                    </select>
                </div>

                <div>
                    <label for="article_category_id" class="mb-2 block text-sm font-bold text-slate-700">
                        カテゴリー
                    </label>

                    <select
                        id="article_category_id"
                        name="article_category_id"
                        class="block w-full rounded-xl border-slate-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                    >
                        <option value="">すべて</option>

                        @foreach (($categories ?? collect()) as $category)
                            <option value="{{ $category->id }}" @selected((string) request('article_category_id') === (string) $category->id)>
                                {{ $category->name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="flex items-end gap-3 md:col-span-4">
                    <button
                        type="submit"
                        class="inline-flex items-center justify-center rounded-xl bg-slate-900 px-5 py-3 text-sm font-bold text-white transition hover:bg-slate-800"
                    >
                        検索する
                    </button>

                    <a
                        href="{{ route('admin.articles.index') }}"
                        class="inline-flex items-center justify-center rounded-xl border border-slate-300 bg-white px-5 py-3 text-sm font-bold text-slate-700 transition hover:bg-slate-50"
                    >
                        リセット
                    </a>
                </div>
            </form>
        </div>

        <div class="overflow-hidden rounded-2xl bg-white shadow-sm ring-1 ring-slate-200">
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-slate-200">
                    <thead class="bg-slate-50">
                        <tr>
                            <th class="whitespace-nowrap px-5 py-3 text-left text-xs font-bold uppercase tracking-wide text-slate-500">
                                ID
                            </th>
                            <th class="whitespace-nowrap px-5 py-3 text-left text-xs font-bold uppercase tracking-wide text-slate-500">
                                記事
                            </th>
                            <th class="whitespace-nowrap px-5 py-3 text-left text-xs font-bold uppercase tracking-wide text-slate-500">
                                カテゴリー
                            </th>
                            <th class="whitespace-nowrap px-5 py-3 text-left text-xs font-bold uppercase tracking-wide text-slate-500">
                                状態
                            </th>
                            <th class="whitespace-nowrap px-5 py-3 text-left text-xs font-bold uppercase tracking-wide text-slate-500">
                                数値
                            </th>
                            <th class="whitespace-nowrap px-5 py-3 text-left text-xs font-bold uppercase tracking-wide text-slate-500">
                                公開日時
                            </th>
                            <th class="whitespace-nowrap px-5 py-3 text-left text-xs font-bold uppercase tracking-wide text-slate-500">
                                作成者
                            </th>
                            <th class="whitespace-nowrap px-5 py-3 text-right text-xs font-bold uppercase tracking-wide text-slate-500">
                                操作
                            </th>
                        </tr>
                    </thead>

                    <tbody class="divide-y divide-slate-100 bg-white">
                        @forelse ($articles as $article)
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

                                $likeCount = $article->likes_count ?? 0;
                                $viewCount = $article->view_count ?? 0;
                            @endphp

                            <tr class="hover:bg-slate-50">
                                <td class="whitespace-nowrap px-5 py-4 text-sm font-semibold text-slate-700">
                                    {{ $article->id }}
                                </td>

                                <td class="px-5 py-4">
                                    <div class="max-w-md">
                                        <p class="font-bold text-slate-900">
                                            {{ $article->title }}
                                        </p>

                                        <p class="mt-1 text-xs text-slate-500">
                                            /articles/{{ $article->slug }}
                                        </p>

                                        @if ($article->short_slug)
                                            <p class="mt-1 text-xs text-indigo-600">
                                                短縮URL：/{{ $article->short_slug }}
                                            </p>
                                        @endif

                                        @if ($article->prefecture)
                                            <p class="mt-2 inline-flex rounded-full bg-emerald-50 px-2.5 py-1 text-xs font-bold text-emerald-700">
                                                {{ $article->prefecture->name }}
                                            </p>
                                        @endif
                                    </div>
                                </td>

                                <td class="whitespace-nowrap px-5 py-4 text-sm text-slate-700">
                                    {{ $article->category?->name ?? '未設定' }}
                                </td>

                                <td class="whitespace-nowrap px-5 py-4">
                                    <span class="inline-flex rounded-full px-3 py-1 text-xs font-bold {{ $statusClass }}">
                                        {{ $statusLabel }}
                                    </span>
                                </td>

                                <td class="whitespace-nowrap px-5 py-4 text-sm text-slate-700">
                                    <div class="space-y-1">
                                        <p>👁 {{ number_format($viewCount) }}</p>
                                        <p>♡ {{ number_format($likeCount) }}</p>
                                    </div>
                                </td>

                                <td class="whitespace-nowrap px-5 py-4 text-sm text-slate-700">
                                    {{ $article->published_at ? $article->published_at->format('Y/m/d H:i') : '未設定' }}
                                </td>

                                <td class="whitespace-nowrap px-5 py-4 text-sm text-slate-700">
                                    {{ $article->admin?->name ?? '未設定' }}
                                </td>

                                <td class="whitespace-nowrap px-5 py-4 text-right">
                                    <div class="flex justify-end gap-2">
                                        @if ((int) $article->status === 2)
                                            <a
                                                href="{{ $publicUrl }}"
                                                target="_blank"
                                                rel="noopener noreferrer"
                                                class="rounded-lg border border-slate-300 bg-white px-3 py-2 text-xs font-bold text-slate-700 transition hover:bg-slate-50"
                                            >
                                                表示
                                            </a>
                                        @endif

                                        <a
                                            href="{{ route('admin.articles.show', $article) }}"
                                            class="rounded-lg border border-slate-300 bg-white px-3 py-2 text-xs font-bold text-slate-700 transition hover:bg-slate-50"
                                        >
                                            詳細
                                        </a>

                                        <a
                                            href="{{ route('admin.articles.edit', $article) }}"
                                            class="rounded-lg bg-indigo-600 px-3 py-2 text-xs font-bold text-white transition hover:bg-indigo-700"
                                        >
                                            編集
                                        </a>

                                        <form
                                            method="POST"
                                            action="{{ route('admin.articles.destroy', $article) }}"
                                            onsubmit="return confirm('この記事を削除しますか？');"
                                        >
                                            @csrf
                                            @method('DELETE')

                                            <button
                                                type="submit"
                                                class="rounded-lg bg-rose-600 px-3 py-2 text-xs font-bold text-white transition hover:bg-rose-700"
                                            >
                                                削除
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="px-5 py-10 text-center text-sm text-slate-500">
                                    記事がありません。
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        @if ($articles->hasPages())
            <div class="mt-8">
                {{ $articles->links() }}
            </div>
        @endif
    </div>
</div>
@endsection
