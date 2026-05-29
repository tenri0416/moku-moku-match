@extends('layouts.admin')

@section('title', '記事編集')

@section('content')
<div class="min-h-screen bg-slate-50">
    <div class="mx-auto max-w-5xl px-4 py-10 sm:px-6 lg:px-8">
        <div class="mb-8">
            <p class="text-sm font-bold text-indigo-600">ARTICLE EDIT</p>
            <h1 class="mt-2 text-3xl font-bold text-slate-900">
                記事編集
            </h1>
        </div>

        <div class="rounded-2xl bg-white p-6 shadow-sm ring-1 ring-slate-200 sm:p-8">
            <form method="POST" action="{{ route('admin.articles.update', $article) }}" enctype="multipart/form-data">
                @method('PATCH')
                @include('admin.articles._form')
            </form>
        </div>
    </div>
</div>
@endsection
