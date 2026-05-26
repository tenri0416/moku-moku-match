@extends('layouts.app')

@section('title', '参加申請')

@section('content')
<div class="min-h-screen bg-slate-50">
    <div class="mx-auto max-w-4xl px-4 py-10 sm:px-6 lg:px-8">
        {{-- Header --}}
        <div class="mb-8">
            <p class="text-sm font-bold text-indigo-600">APPLICATION</p>

            <h1 class="mt-2 text-3xl font-bold text-slate-900">
                参加申請
            </h1>

            <p class="mt-2 text-slate-600">
                募集内容を確認し、投稿者に参加申請メッセージを送りましょう。
            </p>
        </div>

        {{-- Work Post Summary --}}
        <section class="mb-6 rounded-2xl bg-white p-6 shadow-sm ring-1 ring-slate-200 sm:p-8">
            <div class="mb-4 flex flex-wrap gap-2">
                <span class="rounded-full bg-indigo-50 px-3 py-1 text-xs font-bold text-indigo-700">
                    {{ $workPost->purpose }}
                </span>

                @if ($workPost->location_type === 'online')
                    <span class="rounded-full bg-sky-50 px-3 py-1 text-xs font-bold text-sky-700">
                        オンライン
                    </span>
                @elseif ($workPost->location_type === 'offline')
                    <span class="rounded-full bg-amber-50 px-3 py-1 text-xs font-bold text-amber-700">
                        オフライン
                    </span>
                @elseif ($workPost->location_type === 'both')
                    <span class="rounded-full bg-purple-50 px-3 py-1 text-xs font-bold text-purple-700">
                        どちらでも可
                    </span>
                @else
                    <span class="rounded-full bg-slate-100 px-3 py-1 text-xs font-bold text-slate-600">
                        未設定
                    </span>
                @endif
            </div>

            <h2 class="text-2xl font-bold leading-tight text-slate-900">
                {{ $workPost->title }}
            </h2>

            <dl class="mt-5 grid gap-4 text-sm md:grid-cols-2">
                <div class="rounded-xl bg-slate-50 p-4">
                    <dt class="font-bold text-slate-500">投稿者</dt>
                    <dd class="mt-1 font-semibold text-slate-900">
                        {{ $workPost->user->profile->display_name ?? $workPost->user->name }}
                    </dd>
                </div>

                <div class="rounded-xl bg-slate-50 p-4">
                    <dt class="font-bold text-slate-500">目的</dt>
                    <dd class="mt-1 font-semibold text-slate-900">
                        {{ $workPost->purpose }}
                    </dd>
                </div>

                <div class="rounded-xl bg-slate-50 p-4">
                    <dt class="font-bold text-slate-500">使用ツール</dt>
                    <dd class="mt-1 font-semibold text-slate-900">
                        {{ $workPost->meeting_tool ?? '未定' }}
                    </dd>
                </div>

                <div class="rounded-xl bg-slate-50 p-4">
                    <dt class="font-bold text-slate-500">開始日時</dt>
                    <dd class="mt-1 font-semibold text-slate-900">
                        {{ $workPost->start_at ? $workPost->start_at->format('Y/m/d H:i') : '未定' }}
                    </dd>
                </div>
            </dl>

            <div class="mt-5">
                <a
                    href="{{ route('work-posts.show', $workPost) }}"
                    class="text-sm font-bold text-indigo-600 hover:text-indigo-700"
                >
                    募集詳細を見る →
                </a>
            </div>
        </section>

        {{-- Form --}}
        <section class="rounded-2xl bg-white p-6 shadow-sm ring-1 ring-slate-200 sm:p-8">
            <h2 class="text-xl font-bold text-slate-900">
                申請メッセージ
            </h2>

            <p class="mt-2 text-sm leading-7 text-slate-600">
                参加したい理由や、簡単な自己紹介を書いておくと、投稿者が承認しやすくなります。
            </p>

            <form method="POST" action="{{ route('applications.store', $workPost) }}" class="mt-6">
                @csrf

                <div>
                    <label for="message" class="mb-2 block text-sm font-bold text-slate-700">
                        申請メッセージ
                    </label>

                    <textarea
                        id="message"
                        name="message"
                        rows="7"
                        placeholder="例：Laravelを勉強しています。平日の午前中に一緒に黙々作業できる方を探していたので、ぜひ参加したいです。"
                        class="block w-full rounded-xl border-slate-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                    >{{ old('message') }}</textarea>

                    @error('message')
                        <p class="mt-2 text-sm font-semibold text-rose-600">
                            {{ $message }}
                        </p>
                    @enderror
                </div>

                <div class="mt-6 flex flex-wrap items-center gap-3">
                    <button
                        type="submit"
                        class="inline-flex items-center justify-center rounded-xl bg-indigo-600 px-6 py-3 text-sm font-bold text-white shadow-sm transition hover:bg-indigo-700"
                    >
                        参加申請する
                    </button>

                    <a
                        href="{{ route('work-posts.show', $workPost) }}"
                        class="inline-flex items-center justify-center rounded-xl border border-slate-300 bg-white px-6 py-3 text-sm font-bold text-slate-700 transition hover:bg-slate-50"
                    >
                        募集詳細へ戻る
                    </a>
                </div>
            </form>
        </section>
    </div>
</div>
@endsection
