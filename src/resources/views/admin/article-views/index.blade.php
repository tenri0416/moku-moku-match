@extends('layouts.admin')

@section('title', '記事閲覧数')

@section('content')
<div class="py-8">
    <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
        <div class="mb-6">
            <p class="text-sm font-bold text-indigo-600">
                ARTICLE VIEWS
            </p>

            <h1 class="mt-2 text-3xl font-black text-slate-900">
                記事閲覧数
            </h1>

            <p class="mt-2 text-sm leading-6 text-slate-600">
                公開した記事がどれだけ閲覧されているか確認できます。
            </p>
        </div>

        <div class="grid gap-4 sm:grid-cols-3">
            <div class="rounded-2xl bg-white p-5 shadow-sm ring-1 ring-slate-200">
                <p class="text-sm font-bold text-slate-500">総閲覧数</p>
                <p class="mt-2 text-3xl font-black text-slate-900">
                    {{ number_format($articles->sum('views_count')) }}
                </p>
            </div>

            <div class="rounded-2xl bg-white p-5 shadow-sm ring-1 ring-slate-200">
                <p class="text-sm font-bold text-slate-500">直近7日</p>
                <p class="mt-2 text-3xl font-black text-slate-900">
                    {{ number_format($articles->sum('views_last_7_days_count')) }}
                </p>
            </div>

            <div class="rounded-2xl bg-white p-5 shadow-sm ring-1 ring-slate-200">
                <p class="text-sm font-bold text-slate-500">直近30日</p>
                <p class="mt-2 text-3xl font-black text-slate-900">
                    {{ number_format($articles->sum('views_last_30_days_count')) }}
                </p>
            </div>
        </div>

        <div class="mt-6 overflow-hidden rounded-2xl bg-white shadow-sm ring-1 ring-slate-200">
            <table class="min-w-full divide-y divide-slate-200">
                <thead class="bg-slate-50">
                    <tr>
                        <th class="px-5 py-3 text-left text-xs font-black uppercase tracking-wider text-slate-500">
                            記事
                        </th>
                        <th class="px-5 py-3 text-left text-xs font-black uppercase tracking-wider text-slate-500">
                            カテゴリー
                        </th>
                        <th class="px-5 py-3 text-right text-xs font-black uppercase tracking-wider text-slate-500">
                            総閲覧数
                        </th>
                        <th class="px-5 py-3 text-right text-xs font-black uppercase tracking-wider text-slate-500">
                            直近7日
                        </th>
                        <th class="px-5 py-3 text-right text-xs font-black uppercase tracking-wider text-slate-500">
                            直近30日
                        </th>
                        <th class="px-5 py-3 text-right text-xs font-black uppercase tracking-wider text-slate-500">
                            操作
                        </th>
                    </tr>
                </thead>

                <tbody class="divide-y divide-slate-100 bg-white">
                    @forelse ($articles as $article)
                        <tr>
                            <td class="px-5 py-4">
                                <p class="font-bold text-slate-900">
                                    {{ $article->title }}
                                </p>

                                @if ($article->published_at)
                                    <p class="mt-1 text-xs text-slate-500">
                                        公開日：{{ $article->published_at->format('Y/m/d H:i') }}
                                    </p>
                                @endif
                            </td>

                            <td class="px-5 py-4 text-sm text-slate-600">
                                {{ $article->category?->name ?? '-' }}
                            </td>

                            <td class="px-5 py-4 text-right text-lg font-black text-slate-900">
                                {{ number_format($article->views_count) }}
                            </td>

                            <td class="px-5 py-4 text-right text-sm font-bold text-slate-700">
                                {{ number_format($article->views_last_7_days_count) }}
                            </td>

                            <td class="px-5 py-4 text-right text-sm font-bold text-slate-700">
                                {{ number_format($article->views_last_30_days_count) }}
                            </td>

                            <td class="px-5 py-4 text-right">
                                <a
                                    href="{{ route('admin.articles.edit', $article) }}"
                                    class="rounded-lg border border-slate-300 px-3 py-2 text-xs font-bold text-slate-700 transition hover:bg-slate-50"
                                >
                                    編集
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-5 py-12 text-center text-sm text-slate-500">
                                記事がありません。
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if ($articles->hasPages())
            <div class="mt-6">
                {{ $articles->links() }}
            </div>
        @endif
    </div>
</div>
@endsection
