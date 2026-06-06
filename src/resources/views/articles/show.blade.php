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
