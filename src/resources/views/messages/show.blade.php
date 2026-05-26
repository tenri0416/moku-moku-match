@extends('layouts.app')

@section('title', 'メッセージ')

@section('content')
<div class="min-h-screen bg-slate-50">
    <div class="mx-auto max-w-4xl px-4 py-10 sm:px-6 lg:px-8">
        {{-- Header --}}
        <div class="mb-6 rounded-2xl bg-white p-6 shadow-sm ring-1 ring-slate-200">
            <div class="flex flex-col gap-4 sm:flex-row sm:items-start sm:justify-between">
                <div>
                    <p class="text-sm font-bold text-indigo-600">MESSAGE</p>

                    <h1 class="mt-2 text-2xl font-bold text-slate-900">
                        {{ $workPost->title }}
                    </h1>

                    <p class="mt-2 text-sm text-slate-600">
                        相手：
                        <span class="font-semibold text-slate-800">
                            {{ $user->profile->display_name ?? $user->name }}
                        </span>
                    </p>
                </div>

                <div class="flex flex-wrap gap-2">
                    <a
                        href="{{ route('work-posts.show', $workPost) }}"
                        class="inline-flex items-center justify-center rounded-xl border border-slate-300 bg-white px-4 py-2 text-sm font-bold text-slate-700 transition hover:bg-slate-50"
                    >
                        募集詳細
                    </a>

                    <a
                        href="{{ route('messages.index') }}"
                        class="inline-flex items-center justify-center rounded-xl border border-slate-300 bg-white px-4 py-2 text-sm font-bold text-slate-700 transition hover:bg-slate-50"
                    >
                        一覧へ戻る
                    </a>
                </div>
            </div>
        </div>

        {{-- Messages --}}
        <section class="rounded-2xl bg-white p-4 shadow-sm ring-1 ring-slate-200 sm:p-6">
            <h2 class="sr-only">やり取り</h2>

            <div class="space-y-5">
                @forelse ($messages as $message)
                    @php
                        $isMine = $message->sender_id === auth()->id();
                    @endphp

                    <div class="flex {{ $isMine ? 'justify-end' : 'justify-start' }}">
                        <div class="max-w-[85%] sm:max-w-[70%]">
                            <div class="mb-1 flex items-center gap-2 {{ $isMine ? 'justify-end' : 'justify-start' }}">
                                <span class="text-xs font-semibold text-slate-500">
                                    @if ($isMine)
                                        自分
                                    @else
                                        {{ $message->sender->profile->display_name ?? $message->sender->name }}
                                    @endif
                                </span>

                                <span class="text-xs text-slate-400">
                                    {{ $message->created_at->format('Y/m/d H:i') }}
                                </span>
                            </div>

                            <div
                                class="rounded-2xl px-4 py-3 text-sm leading-7 shadow-sm
                                {{ $isMine
                                    ? 'rounded-br-md bg-indigo-600 text-white'
                                    : 'rounded-bl-md bg-slate-100 text-slate-800'
                                }}"
                            >
                                {!! nl2br(e($message->body)) !!}
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="rounded-xl bg-slate-50 p-8 text-center">
                        <div class="mx-auto flex h-14 w-14 items-center justify-center rounded-full bg-white text-2xl shadow-sm">
                            ✉️
                        </div>

                        <p class="mt-4 font-bold text-slate-900">
                            まだメッセージはありません
                        </p>

                        <p class="mt-2 text-sm text-slate-600">
                            最初のメッセージを送信して、やり取りを始めましょう。
                        </p>
                    </div>
                @endforelse
            </div>
        </section>

        {{-- Send Form --}}
        <section class="mt-6 rounded-2xl bg-white p-6 shadow-sm ring-1 ring-slate-200">
            <h2 class="text-lg font-bold text-slate-900">
                メッセージ送信
            </h2>

            <form method="POST" action="{{ route('messages.store', [$workPost, $user]) }}" class="mt-4">
                @csrf

                <div>
                    <label for="body" class="mb-2 block text-sm font-bold text-slate-700">
                        メッセージ本文
                    </label>

                    <textarea
                        id="body"
                        name="body"
                        rows="5"
                        placeholder="メッセージを入力してください"
                        class="block w-full rounded-xl border-slate-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                    >{{ old('body') }}</textarea>

                    @error('body')
                        <p class="mt-2 text-sm font-semibold text-rose-600">
                            {{ $message }}
                        </p>
                    @enderror
                </div>

                <div class="mt-4 flex flex-wrap items-center gap-3">
                    <button
                        type="submit"
                        class="inline-flex items-center justify-center rounded-xl bg-indigo-600 px-5 py-3 text-sm font-bold text-white shadow-sm transition hover:bg-indigo-700"
                    >
                        送信する
                    </button>

                    <a
                        href="{{ route('messages.index') }}"
                        class="inline-flex items-center justify-center rounded-xl border border-slate-300 bg-white px-5 py-3 text-sm font-bold text-slate-700 transition hover:bg-slate-50"
                    >
                        メッセージ一覧へ戻る
                    </a>
                </div>
            </form>
        </section>
    </div>
</div>
@endsection
