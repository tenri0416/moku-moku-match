@php
    $description = $article->seo_description_text
        ?: $article->excerpt
        ?: \Illuminate\Support\Str::limit(strip_tags($article->body_html ?? ''), 120);

    $ogImage = $article->thumbnail_path
        ? asset('storage/' . $article->thumbnail_path)
        : asset('images/articles/pc-top.png');
@endphp

@extends('layouts.article')

@section('title', ($article->seo_title ?: $article->title) . ' | YomuWorks')

@section('description', $description)

@section('og_type', 'article')

@section('og_image', $ogImage)

@push('styles')
    @if ($article->body_css)
        <style>
            {!! $article->body_css !!}
        </style>
    @endif
@endpush

@section('pc_content')
    @include('articles.partials.show_pc')
@endsection

@section('sp_content')
    @include('articles.partials.show_sp')
@endsection
@once
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const likeButtons = document.querySelectorAll('[data-article-like-button]');

            if (!likeButtons.length) {
                return;
            }

            const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');

            const getBrowserKey = () => {
                const storageKey = 'yomuworks_article_browser_key';
                let browserKey = localStorage.getItem(storageKey);

                if (!browserKey) {
                    browserKey = 'guest_' + crypto.randomUUID();
                    localStorage.setItem(storageKey, browserKey);
                }

                return browserKey;
            };

            const updateLikeCounts = (count) => {
                document.querySelectorAll('[data-article-like-count]').forEach((element) => {
                    element.textContent = Number(count).toLocaleString();
                });
            };

            likeButtons.forEach((button) => {
                const articleId = button.dataset.articleId;
                const likeUrl = button.dataset.likeUrl;
                const likedStorageKey = `yomuworks_article_liked_${articleId}`;

                if (localStorage.getItem(likedStorageKey) === '1') {
                    button.classList.add('is-liked');
                    button.disabled = true;
                }

                button.addEventListener('click', async () => {
                    if (button.disabled) {
                        return;
                    }

                    button.disabled = true;

                    try {
                        const response = await fetch(likeUrl, {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'Accept': 'application/json',
                                'X-CSRF-TOKEN': csrfToken,
                            },
                            body: JSON.stringify({
                                browser_key: getBrowserKey(),
                            }),
                        });

                        const data = await response.json();

                        if (!response.ok) {
                            throw new Error(data.message || 'いいねに失敗しました。');
                        }

                        localStorage.setItem(likedStorageKey, '1');
                        button.classList.add('is-liked');
                        updateLikeCounts(data.like_count);
                    } catch (error) {
                        button.disabled = false;
                        alert(error.message || 'いいねに失敗しました。');
                    }
                });
            });
        });
    </script>
@endonce
