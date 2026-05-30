@extends('layouts.article')

@section('title', $article->seo_title ?: $article->title)

@section('description', $article->seo_description_text ?: $article->excerpt)

@push('styles')
    @if ($article->body_css)
        <style>
            {!! $article->body_css !!}
        </style>
    @endif

    <style>
        .article-body {
            color: #334155;
            font-size: 16px;
            line-height: 1.95;
        }

        .article-body h2 {
            margin-top: 48px;
            margin-bottom: 20px;
            padding-left: 16px;
            border-left: 5px solid #C9825D;
            color: #0B1548;
            font-size: 28px;
            font-weight: 900;
            line-height: 1.5;
        }

        .article-body h3 {
            margin-top: 36px;
            margin-bottom: 16px;
            color: #0B1548;
            font-size: 22px;
            font-weight: 900;
            line-height: 1.6;
        }

        .article-body p {
            margin-top: 18px;
        }

        .article-body ul,
        .article-body ol {
            margin-top: 20px;
            padding-left: 1.5em;
        }

        .article-body ul {
            list-style-type: disc;
        }

        .article-body ol {
            list-style-type: decimal;
        }

        .article-body li {
            margin-top: 10px;
            line-height: 1.9;
        }

        .article-body blockquote {
            margin: 32px 0;
            padding: 20px 24px;
            border-left: 5px solid #6F8FAF;
            background: #F7F3EA;
            color: #334155;
            font-weight: 700;
        }

        .article-body a {
            color: #4F46E5;
            font-weight: 700;
            text-decoration: underline;
            text-underline-offset: 3px;
        }
    </style>
@endpush

@section('content')
<div class="min-h-screen bg-slate-50">
    <article class="mx-auto max-w-4xl px-4 py-12 sm:px-6 lg:px-8">
        <div class="rounded-2xl bg-white p-6 shadow-sm ring-1 ring-slate-200 sm:p-10">
            <div class="mb-4 flex flex-wrap gap-2">
                <span class="rounded-full bg-indigo-50 px-3 py-1 text-xs font-bold text-indigo-700">
                    記事
                </span>

                @if ($article->category)
                    <a
                        href="{{ route('articles.category', $article->category->slug) }}"
                        class="rounded-full bg-[#0B1548] px-3 py-1 text-xs font-bold text-white transition hover:bg-[#17215A]"
                    >
                        {{ $article->category->name }}
                    </a>
                @endif

                @if ($article->prefecture)
                    <span class="rounded-full bg-emerald-50 px-3 py-1 text-xs font-bold text-emerald-700">
                        {{ $article->prefecture->name }}
                    </span>
                @endif
            </div>

            <h1 class="text-3xl font-bold leading-tight text-slate-900">
                {{ $article->h1_title ?: $article->title }}
            </h1>

            <div class="mt-4 flex flex-wrap items-center gap-3 text-sm text-slate-500">
                @if ($article->published_at)
                    <span>
                        公開日：{{ $article->published_at->format('Y/m/d') }}
                    </span>
                @endif

                @if ($article->category)
                    <span class="hidden sm:inline">/</span>

                    <a
                        href="{{ route('articles.category', $article->category->slug) }}"
                        class="font-bold text-indigo-600 hover:text-indigo-700"
                    >
                        カテゴリー：{{ $article->category->name }}
                    </a>
                @endif
            </div>

            @if ($article->tags->isNotEmpty())
                <div class="mt-5 flex flex-wrap gap-2">
                    @foreach ($article->tags as $tag)
                        <a
                            href="{{ route('articles.tag', $tag->slug) }}"
                            class="rounded-full bg-[#EEF3F7] px-3 py-1 text-xs font-bold text-[#34506A] transition hover:bg-[#DDEAF2]"
                        >
                            #{{ $tag->name }}
                        </a>
                    @endforeach
                </div>
            @endif

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

            <div class="article-body mt-8">
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
