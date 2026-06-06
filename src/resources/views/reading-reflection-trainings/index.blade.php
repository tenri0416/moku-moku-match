@extends('layouts.app')

@section('content')
    <div class="min-h-screen bg-slate-50 px-4 py-6 pb-24 sm:px-6 lg:px-8">
        <div class="mx-auto max-w-3xl">
            @if (session('success'))
                <div class="mb-4 rounded-2xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm font-bold text-emerald-700">
                    {{ session('success') }}
                </div>
            @endif

            <div class="rounded-3xl bg-white p-5 shadow-sm sm:p-8">
                <div class="flex items-start justify-between gap-4">
                    <div>
                        <p class="text-sm font-bold text-amber-600">Reading Reflection</p>
                        <h1 class="mt-1 text-2xl font-bold text-slate-900">
                            読書振り返り
                        </h1>
                        <p class="mt-3 text-sm leading-7 text-slate-600">
                            寝る前の10分読書で感じたこと、学んだこと、自分なりの解釈を残すための暫定トレーニングです。
                        </p>
                    </div>

                    <div class="flex h-12 w-12 shrink-0 items-center justify-center rounded-2xl bg-amber-100 text-2xl">
                        📚
                    </div>
                </div>

                <div class="mt-6">
                    @include('reading-reflection-trainings._modal')
                </div>
            </div>

            <div class="mt-6 space-y-4">
                @forelse ($reflections as $reflection)
                    <article class="rounded-3xl bg-white p-5 shadow-sm">
                        <div class="flex items-start justify-between gap-3">
                            <div>
                                <p class="text-sm font-bold text-slate-900">
                                    {{ $reflection->read_on->format('Y年m月d日') }}
                                </p>

                                @if ($reflection->book_title)
                                    <h2 class="mt-1 text-lg font-bold text-slate-800">
                                        {{ $reflection->book_title }}
                                    </h2>
                                @else
                                    <h2 class="mt-1 text-lg font-bold text-slate-800">
                                        タイトル未入力
                                    </h2>
                                @endif
                            </div>

                            <div class="shrink-0 rounded-full bg-amber-50 px-3 py-1 text-xs font-bold text-amber-700">
                                {{ $reflection->read_minutes }}分
                            </div>
                        </div>

                        <div class="mt-3 flex flex-wrap gap-2">
                            <span class="rounded-full bg-slate-100 px-3 py-1 text-xs font-bold text-slate-600">
                                {{ $reflection->mood_label }}
                            </span>
                        </div>

                        <p class="mt-4 whitespace-pre-line text-sm leading-7 text-slate-700">
                            {{ $reflection->reflection_text }}
                        </p>
                    </article>
                @empty
                    <div class="rounded-3xl border border-dashed border-slate-200 bg-white p-8 text-center">
                        <p class="text-sm font-bold text-slate-700">
                            まだ読書振り返りはありません。
                        </p>
                        <p class="mt-2 text-sm text-slate-500">
                            まずは今日の10分読書を記録してみましょう。
                        </p>
                    </div>
                @endforelse
            </div>

            <div class="mt-6">
                {{ $reflections->links() }}
            </div>
        </div>
    </div>
@endsection
