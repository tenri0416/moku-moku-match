@extends('layouts.article')

@section('title', isset($pageTitle) ? $pageTitle . ' | YomuWorks' : '記事一覧 | YomuWorks')

@section('description', $pageDescription ?? '技術、個人開発、暮らし、働き方、MokuMoku Matchの活用方法を届ける記事一覧です。')

@section('pc_content')
    @include('articles.partials.index_pc')
@endsection

@section('sp_content')
    @include('articles.partials.index_sp')
@endsection
