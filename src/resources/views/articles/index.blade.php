@extends('layouts.article')

@section('title', '記事一覧 | MokuMoku Match')

@section('meta_description', 'フルリモート作業、もくもく会、作業仲間探しに役立つ記事一覧です。')

@section('content')
<div class="min-h-screen bg-slate-50">
    {{-- Header --}}
    <section class="bg-white">
        <div class="mx-auto max-w-7xl px-4 py-12 sm:px-6 lg:px-8">
            <div class="max-w-3xl">
                <p class="text-sm font-bold text-indigo-600">
                    ARTICLES
                </p>

                <h1 class="mt-2 text-3xl font-bold text-slate-900 sm:text-4xl">
                    お役立ち記事一覧
                </h1>

                <p class="mt-4 leading-7 text-slate-600">
                    フルリモート作業、もくもく会、作業仲間探し、フリーランスの働き方に役立つ記事をまとめています。
                </p>
            </div>
        </div>
    </section>

    {{-- Articles --}}
    <section class="mx-auto max-w-7xl px-4 py-12 sm:px-6 lg:px-8">
        <div class="grid gap-6 md:grid-cols-2 lg:grid-cols-3">
            @forelse ($articles as $article)
                @php
                    $articleUrl = $article->short_slug
                        ? route('articles.short-show', $article->short_slug)
                        : route('articles.show', $article);

                    $thumbnailUrl = $article->thumbnail_path
                        ? asset('storage/' . $article->thumbnail_path)
                        : asset('images/default-avatar.png');

                    $description = $article->excerpt
                        ?: \Illuminate\Support\Str::limit(strip_tags($article->body_html), 120);
                @endphp

                <article class="group overflow-hidden rounded-2xl bg-white shadow-sm ring-1 ring-slate-200 transition hover:-translate-y-1 hover:shadow-md">
                    <a href="{{ $articleUrl }}" class="block">
                        <div class="aspect-[16/9] overflow-hidden bg-slate-100">
                            <img
                                src="{{ $thumbnailUrl }}"
                                alt="{{ $article->title }}"
                                class="h-full w-full object-cover transition duration-300 group-hover:scale-105"
                            >
                        </div>
                    </a>

                    <div class="p-6">
                        <div class="mb-3 flex flex-wrap gap-2">
                            <span class="rounded-full bg-indigo-50 px-3 py-1 text-xs font-bold text-indigo-700">
                                記事
                            </span>

                            @if ($article->prefecture)
                                <span class="rounded-full bg-emerald-50 px-3 py-1 text-xs font-bold text-emerald-700">
                                    {{ $article->prefecture->name }}
                                </span>
                            @endif

                            @if ($article->published_at)
                                <span class="rounded-full bg-slate-100 px-3 py-1 text-xs font-bold text-slate-600">
                                    {{ $article->published_at->format('Y/m/d') }}
                                </span>
                            @endif
                        </div>

                        <h2 class="text-lg font-bold leading-7 text-slate-900">
                            <a href="{{ $articleUrl }}" class="hover:text-indigo-600">
                                {{ $article->title }}
                            </a>
                        </h2>

                        <p class="mt-3 text-sm leading-7 text-slate-600">
                            {{ $description }}
                        </p>

                        <div class="mt-5">
                            <a
                                href="{{ $articleUrl }}"
                                class="text-sm font-bold text-indigo-600 hover:text-indigo-700"
                            >
                                記事を読む →
                            </a>
                        </div>
                    </div>
                </article>
            @empty
                <div class="rounded-2xl bg-white p-8 text-center shadow-sm ring-1 ring-slate-200 md:col-span-2 lg:col-span-3">
                    <p class="text-slate-600">
                        現在、公開中の記事はありません。
                    </p>
                </div>
            @endforelse
        </div>

        @if ($articles->hasPages())
            <div class="mt-10">
                {{ $articles->links() }}
            </div>
        @endif
    </section>
</div>
@endsection
