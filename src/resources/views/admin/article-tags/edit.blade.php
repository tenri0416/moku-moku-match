@extends('layouts.admin')

@section('title', '記事タグ編集')

@section('content')
<div class="py-8">
    <div class="mx-auto max-w-4xl px-4 sm:px-6 lg:px-8">
        <div class="mb-6">
            <p class="text-sm font-bold text-indigo-600">
                EDIT TAG
            </p>

            <h1 class="mt-2 text-3xl font-black text-slate-900">
                記事タグ編集
            </h1>
        </div>

        @include('admin.article-tags._form', [
            'action' => route('admin.article-tags.update', $tag),
            'method' => 'PUT',
            'buttonText' => 'タグを更新する',
        ])
    </div>
</div>
@endsection
