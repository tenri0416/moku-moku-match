@extends('layouts.admin')

@section('title', '募集編集')

@section('content')
<div class="min-h-screen bg-slate-50">
    <div class="mx-auto max-w-4xl px-4 py-10 sm:px-6 lg:px-8">
        <div class="mb-8">
            <p class="text-sm font-bold text-indigo-600">ADMIN EDIT POST</p>

            <h1 class="mt-2 text-3xl font-bold text-slate-900">
                募集編集
            </h1>

            <p class="mt-2 text-slate-600">
                募集内容や公開状態を管理者として変更できます。
            </p>
        </div>

        <div class="rounded-2xl bg-white p-6 shadow-sm ring-1 ring-slate-200 sm:p-8">
            <form method="POST" action="{{ route('admin.work-posts.update', $workPost) }}">
                @method('PUT')
                @include('admin.work-posts._form', ['workPost' => $workPost])
            </form>
        </div>

        <div class="mt-6">
            <a
                href="{{ route('admin.work-posts.show', $workPost) }}"
                class="text-sm font-bold text-indigo-600 hover:text-indigo-700"
            >
                ← 募集詳細へ戻る
            </a>
        </div>
    </div>
</div>
@endsection
