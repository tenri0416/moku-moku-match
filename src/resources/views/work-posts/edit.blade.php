@extends('layouts.app')

@section('title', '募集編集')

@section('content')
<div class="min-h-screen bg-slate-50">
    <div class="mx-auto max-w-4xl px-4 py-10 sm:px-6 lg:px-8">
        <div class="mb-8">
            <p class="text-sm font-bold text-indigo-600">EDIT POST</p>
            <h1 class="mt-2 text-3xl font-bold text-slate-900">
                募集編集
            </h1>
            <p class="mt-2 text-slate-600">
                募集内容を編集できます。変更内容を確認して更新してください。
            </p>
        </div>

        <div class="rounded-2xl bg-white p-6 shadow-sm ring-1 ring-slate-200 sm:p-8">
            <form method="POST" action="{{ route('work-posts.update', $workPost) }}">
                @method('PUT')

                @include('work-posts._form')
            </form>
        </div>
    </div>
</div>
@endsection
