<section class="yw-sp-top">
    <img src="{{ asset('images/articles/sp-top.png') }}" alt="YomuWorks" class="yw-sp-top-image">
</section>

<section class="yw-sp-index">
    <div class="yw-sp-index-head">
        <p class="yw-section-kicker">ARTICLES</p>

        <h1>
            {{ $pageTitle ?? 'お役立ち記事一覧' }}
        </h1>

        <p>
            {{ $pageDescription ?? '技術、個人開発、暮らし、働き方をやさしくまとめています。' }}
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

    <div class="yw-sp-card-list">
        @forelse ($articles as $article)
            @php
                $articleUrl = $article->short_slug
                    ? route('articles.short-show', $article->short_slug)
                    : route('articles.show', $article);

                $thumbnailUrl = $article->thumbnail_path
                    ? asset('storage/' . $article->thumbnail_path)
                    : asset('images/articles/sp-top.png');

                $description = $article->excerpt
                    ?: \Illuminate\Support\Str::limit(strip_tags($article->body_html ?? ''), 70);

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

            <article class="yw-sp-article-card">
                <a href="{{ $articleUrl }}" class="yw-sp-card-image-wrap">
                    <img
                        src="{{ $thumbnailUrl }}"
                        alt="{{ $article->title }}"
                        class="yw-sp-card-image"
                    >
                </a>

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

                    <a href="{{ $articleUrl }}">読む →</a>
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
