@php
    $featuredArticle = $articles->first();
    $otherArticles = $articles->skip(1);
@endphp

<section class="yw-pc-top">
    <div class="yw-pc-top-inner">
        <img src="{{ asset('images/articles/pc-top.png') }}" alt="YomuWorks" class="yw-pc-top-image">
    </div>
</section>

<section class="yw-pc-index">
    <div class="yw-pc-index-head">
        <p class="yw-section-kicker">ARTICLES</p>

        <h1>
            {{ $pageTitle ?? 'お役立ち記事一覧' }}
        </h1>

        <p>
            {{ $pageDescription ?? '技術、個人開発、暮らし、働き方、MokuMoku Matchの活用方法をまとめています。' }}
        </p>

        @if (isset($currentCategory))
            <div class="yw-current-filter">
                カテゴリー：{{ $currentCategory->name }}
            </div>
        @endif

        @if (isset($currentTag))
            <div class="yw-current-filter">
                タグ：#{{ $currentTag->name }}
            </div>
        @endif
    </div>

    @if ($featuredArticle)
        @php
            $featuredArticleUrl = $featuredArticle->short_slug
                ? route('articles.short-show', $featuredArticle->short_slug)
                : route('articles.show', $featuredArticle);

            $featuredImage = $featuredArticle->thumbnail_path
                ? asset('storage/' . $featuredArticle->thumbnail_path)
                : asset('images/articles/pc-top.png');

            $featuredDescription = $featuredArticle->excerpt
                ?: \Illuminate\Support\Str::limit(strip_tags($featuredArticle->body_html ?? ''), 120);

            $featuredAuthorUser = $featuredArticle->authorUser;
            $featuredAuthorProfile = $featuredAuthorUser?->profile;

            $featuredAuthorName = $featuredAuthorProfile?->display_name
                ?: $featuredAuthorUser?->name
                ?: 'YomuWorks編集部';

            $featuredAuthorAvatar = $featuredAuthorProfile?->avatar_path
                ? asset('storage/' . $featuredAuthorProfile->avatar_path)
                : asset('images/default-avatar.png');

            $featuredLikeCount = $featuredArticle->likes_count ?? 0;
            $featuredViewCount = $featuredArticle->view_count ?? 0;
        @endphp

        <article class="yw-pc-featured-card">
            <a href="{{ $featuredArticleUrl }}" class="yw-pc-featured-image-wrap">
                <img src="{{ $featuredImage }}" alt="{{ $featuredArticle->title }}" class="yw-pc-featured-image">
            </a>

            <div class="yw-pc-featured-body">
                <div class="yw-tags">
                    <span>記事</span>

                    @if ($featuredArticle->category)
                        <a href="{{ route('articles.category', $featuredArticle->category->slug) }}">
                            {{ $featuredArticle->category->name }}
                        </a>
                    @endif

                    @if ($featuredArticle->prefecture)
                        <span>{{ $featuredArticle->prefecture->name }}</span>
                    @endif
                </div>

                <h2>
                    <a href="{{ $featuredArticleUrl }}">
                        {{ $featuredArticle->title }}
                    </a>
                </h2>

                <p>
                    {{ $featuredDescription }}
                </p>

                <div class="yw-card-author">
                    <img src="{{ $featuredAuthorAvatar }}" alt="{{ $featuredAuthorName }}">
                    <span>{{ $featuredAuthorName }}</span>
                </div>

                @if ($featuredArticle->tags->isNotEmpty())
                    <div class="yw-tag-links">
                        @foreach ($featuredArticle->tags as $tag)
                            <a href="{{ route('articles.tag', $tag->slug) }}">
                                #{{ $tag->name }}
                            </a>
                        @endforeach
                    </div>
                @endif

                <div class="yw-card-meta">
                    @if ($featuredArticle->published_at)
                        <span>{{ $featuredArticle->published_at->format('Y.m.d') }}</span>
                    @endif

                    <span>{{ $featuredArticle->reading_minutes ?? 3 }}分で読めます</span>
                    <span>👁 {{ number_format($featuredViewCount) }}</span>
                    <span class="yw-like-inline">♡ {{ number_format($featuredLikeCount) }}</span>

                    <a href="{{ $featuredArticleUrl }}">記事を読む →</a>
                </div>
            </div>
        </article>
    @endif

    <div class="yw-pc-card-grid">
        @forelse ($otherArticles as $article)
            @php
                $articleUrl = $article->short_slug
                    ? route('articles.short-show', $article->short_slug)
                    : route('articles.show', $article);

                $thumbnailUrl = $article->thumbnail_path
                    ? asset('storage/' . $article->thumbnail_path)
                    : asset('images/articles/pc-top.png');

                $description = $article->excerpt
                    ?: \Illuminate\Support\Str::limit(strip_tags($article->body_html ?? ''), 100);

                $authorUser = $article->authorUser;
                $authorProfile = $authorUser?->profile;

                $authorName = $authorProfile?->display_name
                    ?: $authorUser?->name
                    ?: 'YomuWorks編集部';

                $authorAvatar = $authorProfile?->avatar_path
                    ? asset('storage/' . $authorProfile->avatar_path)
                    : asset('images/default-avatar.png');

                $likeCount = $article->likes_count ?? 0;
                $viewCount = $article->view_count ?? 0;
            @endphp

            <article class="yw-pc-article-card">
                <a href="{{ $articleUrl }}" class="yw-pc-card-image-wrap">
                    <img src="{{ $thumbnailUrl }}" alt="{{ $article->title }}" class="yw-pc-card-image">
                </a>

                <div class="yw-pc-card-body">
                    <div class="yw-tags small">
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

                    <h2>
                        <a href="{{ $articleUrl }}">
                            {{ $article->title }}
                        </a>
                    </h2>

                    <p>
                        {{ $description }}
                    </p>

                    <div class="yw-card-author">
                        <img src="{{ $authorAvatar }}" alt="{{ $authorName }}">
                        <span>{{ $authorName }}</span>
                    </div>

                    @if ($article->tags->isNotEmpty())
                        <div class="yw-tag-links">
                            @foreach ($article->tags as $tag)
                                <a href="{{ route('articles.tag', $tag->slug) }}">
                                    #{{ $tag->name }}
                                </a>
                            @endforeach
                        </div>
                    @endif

                    <div class="yw-card-meta">
                        @if ($article->published_at)
                            <span>{{ $article->published_at->format('Y.m.d') }}</span>
                        @endif

                        <span>{{ $article->reading_minutes ?? 3 }}分で読めます</span>
                        <span>👁 {{ number_format($viewCount) }}</span>
                        <span class="yw-like-inline">♡ {{ number_format($likeCount) }}</span>

                        <a href="{{ $articleUrl }}">記事を読む →</a>
                    </div>
                </div>
            </article>
        @empty
            <div class="yw-empty">
                <p>現在、公開中の記事はありません。</p>
            </div>
        @endforelse
    </div>

    @if ($articles->hasPages())
        <div class="yw-pagination">
            {{ $articles->links() }}
        </div>
    @endif
</section>
