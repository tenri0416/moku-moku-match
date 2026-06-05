{!! '<?xml version="1.0" encoding="UTF-8"?>' !!}
<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">
    <url>
        <loc>{{ route('home') }}</loc>
        <lastmod>{{ now()->toAtomString() }}</lastmod>
        <changefreq>daily</changefreq>
        <priority>1.0</priority>
    </url>

    @if (Route::has('work-posts.index'))
        <url>
            <loc>{{ route('work-posts.index') }}</loc>
            <lastmod>{{ now()->toAtomString() }}</lastmod>
            <changefreq>daily</changefreq>
            <priority>0.8</priority>
        </url>
    @endif

    @if (Route::has('articles.index'))
        <url>
            <loc>{{ route('articles.index') }}</loc>
            <lastmod>{{ now()->toAtomString() }}</lastmod>
            <changefreq>weekly</changefreq>
            <priority>0.8</priority>
        </url>
    @endif

    @if (Route::has('privacy-policy'))
        <url>
            <loc>{{ route('privacy-policy') }}</loc>
            <lastmod>{{ now()->toAtomString() }}</lastmod>
            <changefreq>monthly</changefreq>
            <priority>0.4</priority>
        </url>
    @endif

    @if (Route::has('terms'))
        <url>
            <loc>{{ route('terms') }}</loc>
            <lastmod>{{ now()->toAtomString() }}</lastmod>
            <changefreq>monthly</changefreq>
            <priority>0.4</priority>
        </url>
    @endif

    @foreach (($workPosts ?? collect()) as $workPost)
        @if (Route::has('work-posts.show'))
            <url>
                <loc>{{ route('work-posts.show', $workPost) }}</loc>
                <lastmod>{{ optional($workPost->updated_at)->toAtomString() ?? now()->toAtomString() }}</lastmod>
                <changefreq>weekly</changefreq>
                <priority>0.6</priority>
            </url>
        @endif
    @endforeach

    @foreach (($articles ?? collect()) as $article)
        @php
            $articleUrl = null;

            if (! empty($article->short_slug) && Route::has('articles.short-show')) {
                $articleUrl = route('articles.short-show', $article->short_slug);
            } elseif (Route::has('articles.show')) {
                $articleUrl = route('articles.show', $article);
            }
        @endphp

        @if ($articleUrl)
            <url>
                <loc>{{ $articleUrl }}</loc>
                <lastmod>{{ optional($article->updated_at)->toAtomString() ?? now()->toAtomString() }}</lastmod>
                <changefreq>weekly</changefreq>
                <priority>0.7</priority>
            </url>
        @endif
    @endforeach
</urlset>
