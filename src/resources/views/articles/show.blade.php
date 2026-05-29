@extends('layouts.article')

@section('title', $article->seo_title)

@section('meta_description', $article->seo_description_text)

@section('content')
<div class="min-h-screen bg-slate-50">
    <article class="mx-auto max-w-4xl px-4 py-12 sm:px-6 lg:px-8">
        <div class="rounded-2xl bg-white p-6 shadow-sm ring-1 ring-slate-200 sm:p-10">
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
                公開日：{{ $article->published_at?->format('Y/m/d') }}
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

            <div class="article-body mt-8 leading-8 text-slate-700">
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
                        href="{{ route('work-posts.index') }}"
                        class="inline-flex items-center justify-center rounded-xl bg-indigo-600 px-5 py-3 text-sm font-bold text-white transition hover:bg-indigo-700"
                    >
                        募集を見る
                    </a>
                </div>
            </div>
        </div>
    </article>
</div>
@endsection
