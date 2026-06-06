@php
    $articleImage = $article->thumbnail_path
        ? asset('storage/' . $article->thumbnail_path)
        : asset('images/articles/sp-top.png');

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
@endphp

<article class="yw-sp-article">
    <div class="yw-sp-article-image-wrap">
        <img src="{{ $articleImage }}" alt="{{ $article->title }}" class="yw-sp-article-image">
    </div>

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

    <h1 class="yw-sp-article-title">
        {{ $articleTitle }}
    </h1>

    <p class="yw-sp-article-lead">
        {{ $articleLead }}
    </p>

    <div class="yw-sp-author">
        <img
            src="{{ $authorAvatar }}"
            alt="{{ $authorName }}"
            class="yw-author-avatar-img"
        >

        <div>
            <p>{{ $authorName }}</p>

            <span>
                @if ($article->published_at)
                    {{ $article->published_at->format('Y.m.d') }}
                @else
                    日付未設定
                @endif
                ・ {{ $article->reading_minutes ?? 3 }}分で読めます
            </span>
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

    <section class="yw-sp-point">
        <p>この記事のポイント</p>

        <ul>
            <li>読みやすく内容を整理</li>
            <li>今日から使えるヒントを紹介</li>
            <li>働き方や学びを少し整える</li>
        </ul>
    </section>

    <div class="yw-sp-article-body article-body">
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

    <a href="{{ route('articles.index') }}" class="yw-sp-read-more">
        記事一覧へ戻る
        <span>⌄</span>
    </a>

    <section class="yw-sp-related">
        <div class="yw-related-head">
            <h2>関連記事</h2>
            <a href="{{ route('articles.index') }}">もっと見る →</a>
        </div>

        <div class="yw-sp-related-grid">
            @forelse ($relatedArticles->take(3) as $related)
                @php
                    $relatedUrl = $related->short_slug
                        ? route('articles.short-show', $related->short_slug)
                        : route('articles.show', $related);

                    $relatedImage = $related->thumbnail_path
                        ? asset('storage/' . $related->thumbnail_path)
                        : asset('images/articles/sp-top.png');
                @endphp

                <article class="yw-sp-related-card">
                    <a href="{{ $relatedUrl }}">
                        <img src="{{ $relatedImage }}" alt="{{ $related->title }}">
                        <h3>{{ $related->title }}</h3>

                        @if ($related->published_at)
                            <p>{{ $related->published_at->format('Y.m.d') }}</p>
                        @endif
                    </a>
                </article>
            @empty
                <article class="yw-sp-related-card">
                    <a href="{{ route('articles.index') }}">
                        <img src="{{ asset('images/articles/sp-top.png') }}" alt="YomuWorks">
                        <h3>他の記事も読む</h3>
                        <p>YomuWorks</p>
                    </a>
                </article>
            @endforelse
        </div>
    </section>
</article>
