{!! '<?xml version="1.0" encoding="UTF-8"?>' !!}
<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">
    <url>
        <loc>{{ url('/') }}</loc>
        <priority>1.0</priority>
    </url>

    <url>
        <loc>{{ route('work-posts.index') }}</loc>
        <priority>0.8</priority>
    </url>

    @foreach ($workPosts as $workPost)
        <url>
            <loc>{{ route('work-posts.show', $workPost) }}</loc>
            <lastmod>{{ $workPost->updated_at->toAtomString() }}</lastmod>
            <priority>0.6</priority>
        </url>
    @endforeach
</urlset>
