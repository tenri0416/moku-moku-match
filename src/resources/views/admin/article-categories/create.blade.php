@extends('layouts.admin')

@section('title', '記事カテゴリー作成')

@section('content')
<div class="py-8">
    <div class="mx-auto max-w-4xl px-4 sm:px-6 lg:px-8">
        <div class="mb-6">
            <p class="text-sm font-bold text-indigo-600">
                CREATE CATEGORY
            </p>

            <h1 class="mt-2 text-3xl font-black text-slate-900">
                記事カテゴリー作成
            </h1>
        </div>

        @include('admin.article-categories._form', [
            'action' => route('admin.article-categories.store'),
            'method' => 'POST',
            'buttonText' => 'カテゴリーを作成する',
        ])
    </div>
</div>
@endsection
