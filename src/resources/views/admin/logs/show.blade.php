@extends('layouts.admin')

@section('title', $file)

@section('content')
<div class="py-8">
    <div class="mx-auto max-w-7xl px-4">
        <div class="mb-6 flex flex-col gap-3 sm:flex-row sm:items-end sm:justify-between">
            <div>
                <h1 class="text-2xl font-bold text-slate-900">
                    {{ $file }}
                </h1>
                <p class="mt-2 text-sm text-slate-600">
                    ファイルサイズ：{{ $size }} / 全件表示
                </p>
            </div>

            <a
                href="{{ route('admin.logs.index') }}"
                class="inline-flex items-center justify-center rounded-lg border border-slate-300 bg-white px-4 py-2 text-sm font-bold text-slate-700 hover:bg-slate-100"
            >
                ログ一覧へ戻る
            </a>
        </div>

        <div class="rounded-xl bg-slate-950 p-4 shadow-sm ring-1 ring-slate-800">
            <pre class="max-h-[80vh] overflow-auto whitespace-pre-wrap break-words text-xs leading-6 text-slate-100">{{ $content }}</pre>
        </div>
    </div>
</div>
@endsection
