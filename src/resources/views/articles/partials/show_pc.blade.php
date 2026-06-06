@php
    $articleImage = $article->thumbnail_path
        ? asset('storage/' . $article->thumbnail_path)
        : asset('images/articles/pc-top.png');

    $articleTitle = $article->h1_title ?: $article->title;

    $articleLead = $article->excerpt
        ?: 'この記事では、日々の働き方や学びを少し整えるためのヒントを紹介します。';

    $relatedArticles = $relatedArticles ?? collect();

    $authorUser = $article->authorUser;
    $authorProfile = $authorUser?->profile;

    $authorName = $authorProfile?->display_name
        ?: $authorUser?->name
        ?: 'YomuWorks編集部';

    $authorAvatar = $authorProfile?->avatar_path
        ? asset('storage/' . $authorProfile->avatar_path)
        : asset('images/default-avatar.png');

    $pointItems = $article->point_items;
    $tocItems = $article->toc_items;
    $likeCount = $article->likes_count ?? $article->likes()->count();
    $viewCount = $article->view_count ?? 0;
@endphp

<section class="yw-pc-article-hero">
    <div class="yw-pc-article-hero-inner">
        <div class="yw-pc-hero-visual">
            <img src="{{ $articleImage }}" alt="{{ $article->title }}">
        </div>

        <aside class="yw-pc-hero-point">
            <p class="yw-point-title">この記事のポイント</p>

            <ul>
                @foreach ($pointItems as $point)
                    <li>{{ $point }}</li>
                @endforeach
            </ul>
        </aside>
    </div>
</section>

<section class="yw-pc-article-layout">
    <article class="yw-pc-article-main">
        <div class="yw-tags">
            <span>記事</span>

            @if ($article->category)
                <a href="{{ route('articles.category', $article->category->slug) }}">
                    {{ $article->category->name }}
                </a>
            @endif

            @if ($article->prefecture)
                <span>{{ $article->prefecture->name }}</span>
            @endif
        </div>

        <h1 class="yw-pc-article-title">
            {{ $articleTitle }}
        </h1>

        <p class="yw-pc-article-lead">
            {{ $articleLead }}
        </p>

        <div class="yw-pc-author">
            <img
                src="{{ $authorAvatar }}"
                alt="{{ $authorName }}"
                class="yw-author-avatar-img"
            >

            <div>
                <p>{{ $authorName }}</p>

                <div class="yw-article-meta-lines">
                    <div class="yw-article-meta-line">
                        @if ($article->published_at)
                            <span>{{ $article->published_at->format('Y.m.d') }}</span>
                        @else
                            <span>日付未設定</span>
                        @endif

                        <span>・</span>
                        <span>{{ $article->reading_minutes ?? 3 }}分で読めます</span>
                    </div>

                    <div class="yw-article-meta-line">
                        <span>👁 {{ number_format($viewCount) }}</span>
                        <span>・</span>
                        <span class="yw-like-inline">
                            ♡ <span data-article-like-count>{{ number_format($likeCount) }}</span>
                        </span>
                    </div>
                </div>
            </div>
        </div>

        @if ($article->tags->isNotEmpty())
            <div class="yw-tag-links article-tags">
                @foreach ($article->tags as $tag)
                    <a href="{{ route('articles.tag', $tag->slug) }}">
                        #{{ $tag->name }}
                    </a>
                @endforeach
            </div>
        @endif

        <div class="yw-pc-article-body article-body">
            {!! $article->body_html !!}
        </div>

        <section class="yw-service-box">
            <p class="yw-service-title">
                作業仲間を探してみませんか？
            </p>

            <p class="yw-service-text">
                MokuMoku Matchでは、フルリモートで働く人や学習中の人が、黙々作業・勉強・情報交換できる相手を探せます。
            </p>

            <a href="{{ route('home') }}" class="yw-service-button">
                MokuMoku Matchを見る
            </a>
        </section>

        <section class="yw-pc-related">
            <div class="yw-related-head">
                <h2>関連記事</h2>
                <a href="{{ route('articles.index') }}">もっと見る →</a>
            </div>

            <div class="yw-pc-related-grid">
                @forelse ($relatedArticles->take(4) as $related)
                    @php
                        $relatedUrl = $related->short_slug
                            ? route('articles.short-show', $related->short_slug)
                            : route('articles.show', $related);

                        $relatedImage = $related->thumbnail_path
                            ? asset('storage/' . $related->thumbnail_path)
                            : asset('images/articles/pc-top.png');

                        $relatedLikeCount = $related->likes_count ?? 0;
                        $relatedViewCount = $related->view_count ?? 0;
                    @endphp

                    <article class="yw-pc-related-card">
                        <a href="{{ $relatedUrl }}">
                            <img src="{{ $relatedImage }}" alt="{{ $related->title }}">
                            <h3>{{ $related->title }}</h3>

                            @if ($related->published_at)
                                <p>
                                    {{ $related->published_at->format('Y.m.d') }}
                                    ・ 👁 {{ number_format($relatedViewCount) }}
                                    ・ ♡ {{ number_format($relatedLikeCount) }}
                                </p>
                            @endif
                        </a>
                    </article>
                @empty
                    <article class="yw-pc-related-card">
                        <a href="{{ route('articles.index') }}">
                            <img src="{{ asset('images/articles/pc-top.png') }}" alt="YomuWorks">
                            <h3>他の記事も読む</h3>
                            <p>YomuWorks</p>
                        </a>
                    </article>
                @endforelse
            </div>
        </section>
    </article>

    <aside class="yw-pc-sidebar">
        <div class="yw-share-box">
            <a href="#">X</a>
            <a href="#">f</a>
            <a href="#">B!</a>

            <button
                type="button"
                class="yw-article-like-button"
                data-article-like-button
                data-article-id="{{ $article->id }}"
                data-like-url="{{ route('articles.like', $article) }}"
            >
                <span class="yw-like-inline">
                    ♡ <span data-article-like-count>{{ number_format($likeCount) }}</span>
                </span>
            </button>
        </div>

        <div class="yw-side-counts">
            <p>記事データ</p>
            <ul>
                <li>閲覧数：{{ number_format($viewCount) }}</li>
                <li>いいね：<span data-article-like-count>{{ number_format($likeCount) }}</span></li>
            </ul>
        </div>

        <div class="yw-side-point">
            <p>この記事のポイント</p>

            <ul>
                @foreach ($pointItems as $point)
                    <li>{{ $point }}</li>
                @endforeach
            </ul>
        </div>

        <div class="yw-side-toc">
            <p>目次</p>

            <ul>
                @foreach ($tocItems as $toc)
                    <li>{{ $toc }}</li>
                @endforeach
            </ul>
        </div>
    </aside>
</section>
