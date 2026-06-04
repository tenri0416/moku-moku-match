@extends('layouts.admin')

@section('title', $typeLabel . '詳細')

@section('content')
<div class="py-8">
    <div class="mx-auto max-w-7xl px-4">
        <div class="mb-6 flex flex-col gap-4 sm:flex-row sm:items-start sm:justify-between">
            <div>
                <h1 class="text-2xl font-bold text-slate-900">
                    {{ $typeLabel }}詳細
                </h1>

                <div class="mt-2 flex flex-wrap items-center gap-2 text-sm text-slate-600">
                    @if ($type === 'error')
                        <span class="inline-flex rounded-full bg-rose-100 px-3 py-1 text-xs font-bold text-rose-700">
                            エラーログ
                        </span>
                    @else
                        <span class="inline-flex rounded-full bg-indigo-100 px-3 py-1 text-xs font-bold text-indigo-700">
                            通常ログ
                        </span>
                    @endif

                    <span>
                        {{ $file }}
                    </span>

                    <span>
                        {{ $size }}
                    </span>
                </div>
            </div>

            <a
                href="{{ route('admin.logs.index', ['type' => $type, 'date' => $date]) }}"
                class="inline-flex items-center justify-center rounded-lg border border-slate-300 bg-white px-5 py-2 text-sm font-bold text-slate-700 hover:bg-slate-50"
            >
                一覧へ戻る
            </a>
        </div>

        <div class="overflow-hidden rounded-xl bg-slate-950 shadow-sm ring-1 ring-slate-800">
            <div class="flex items-center justify-between border-b border-slate-800 bg-slate-900 px-4 py-3">
                <div>
                    <p class="text-sm font-bold text-white">
                        {{ $file }}
                    </p>
                    <p class="mt-1 text-xs text-slate-400">
                        {{ $typeLabel }} / {{ $date ?? '日付なし' }}
                    </p>
                </div>
            </div>

            <pre class="max-h-[75vh] overflow-auto whitespace-pre-wrap break-words p-4 text-xs leading-6 text-slate-100">{{ $content }}</pre>
        </div>
    </div>
</div>
@endsection
