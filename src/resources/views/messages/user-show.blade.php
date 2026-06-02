@extends('layouts.app')

@section('content')
@php
    $profile = $user->profile;
    $avatarPath = $profile?->avatar_path;
    $avatarUrl = $avatarPath
        ? asset('storage/' . $avatarPath)
        : asset('images/default-avatar.png');

    $displayName = $profile?->display_name ?? $user->name;
@endphp

<div class="min-h-screen bg-slate-50">
    <div class="mx-auto max-w-4xl px-4 py-10 sm:px-6 lg:px-8">

        {{-- Header --}}
        <div class="mb-6 flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
            <div class="flex items-center gap-4">
                <img
                    src="{{ $avatarUrl }}"
                    alt="{{ $displayName }}のプロフィール画像"
                    class="h-14 w-14 rounded-full border border-slate-200 bg-white object-cover"
                >

                <div>
                    <p class="text-sm font-bold text-indigo-600">
                        DIRECT MESSAGE
                    </p>
                    <h1 class="text-2xl font-bold text-slate-900">
                        {{ $displayName }} さんとのメッセージ
                    </h1>
                </div>
            </div>

            <a
                href="{{ route('users.show', $user) }}"
                class="inline-flex items-center justify-center rounded-xl border border-slate-300 bg-white px-4 py-2 text-sm font-bold text-slate-700 transition hover:bg-slate-50"
            >
                プロフィールへ戻る
            </a>
        </div>

        {{-- Flash Message --}}
        @if (session('success'))
            <div class="mb-6 rounded-xl bg-emerald-50 px-4 py-3 text-sm font-bold text-emerald-700">
                {{ session('success') }}
            </div>
        @endif

        @if ($errors->any())
            <div class="mb-6 rounded-xl bg-rose-50 px-4 py-3 text-sm font-bold text-rose-700">
                {{ $errors->first() }}
            </div>
        @endif

        {{-- Message Area --}}
        <section class="rounded-2xl bg-white shadow-sm ring-1 ring-slate-200">
            <div
                id="message-list"
                data-message-polling
                data-latest-url="{{ route('messages.users.latest', $user) }}"
                data-latest-message-id="{{ $latestMessageId ?? 0 }}"
                class="h-[520px] space-y-4 overflow-y-auto p-5"
            >
                @forelse ($messages as $message)
                    @php
                        $isMine = $message->sender_id === auth()->id();
                        $senderProfile = $message->sender?->profile;
                        $senderName = $senderProfile?->display_name ?? $message->sender?->name ?? 'ユーザー';
                        $senderAvatarPath = $senderProfile?->avatar_path;
                        $senderAvatarUrl = $senderAvatarPath
                            ? asset('storage/' . $senderAvatarPath)
                            : asset('images/default-avatar.png');
                    @endphp

                    <div class="flex {{ $isMine ? 'justify-end' : 'justify-start' }}">
                        <div class="flex max-w-[80%] gap-3 {{ $isMine ? 'flex-row-reverse' : '' }}">
                            <img
                                src="{{ $senderAvatarUrl }}"
                                alt="{{ $senderName }}のプロフィール画像"
                                class="h-9 w-9 flex-shrink-0 rounded-full border border-slate-200 bg-white object-cover"
                            >

                            <div>
                                <div class="mb-1 text-xs font-bold {{ $isMine ? 'text-right text-slate-400' : 'text-slate-500' }}">
                                    {{ $senderName }}
                                </div>

                                <div class="rounded-2xl px-4 py-3 text-sm leading-6 shadow-sm {{ $isMine ? 'bg-indigo-600 text-white' : 'bg-slate-100 text-slate-800' }}">
                                    {!! nl2br(e($message->body)) !!}
                                </div>

                                <div class="mt-1 text-xs {{ $isMine ? 'text-right text-slate-400' : 'text-slate-400' }}">
                                    {{ $message->created_at->format('Y/m/d H:i') }}
                                </div>
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="flex h-full items-center justify-center text-center">
                        <div>
                            <div class="mx-auto flex h-14 w-14 items-center justify-center rounded-full bg-slate-100 text-2xl">
                                💬
                            </div>
                            <h2 class="mt-4 text-lg font-bold text-slate-900">
                                まだメッセージはありません
                            </h2>
                            <p class="mt-2 text-sm text-slate-500">
                                最初のメッセージを送ってみましょう。
                            </p>
                        </div>
                    </div>
                @endforelse
            </div>

            {{-- Send Form --}}
            <div class="border-t border-slate-200 p-4">
                <form
                    method="POST"
                    action="{{ route('messages.users.store', $user) }}"
                    class="space-y-3"
                    id="message-form"
                >
                    @csrf

                    <textarea
                        name="body"
                        rows="3"
                        required
                        maxlength="2000"
                        placeholder="メッセージを入力してください"
                        class="block w-full resize-none rounded-xl border-slate-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                    >{{ old('body') }}</textarea>

                    <div class="flex justify-end">
                        <button
                            type="submit"
                            id="message-submit-button"
                            class="inline-flex items-center justify-center rounded-xl bg-indigo-600 px-6 py-3 text-sm font-bold text-white shadow-sm transition hover:bg-indigo-700 disabled:cursor-not-allowed disabled:opacity-60"
                        >
                            送信する
                        </button>
                    </div>
                </form>
            </div>
        </section>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const messageList = document.getElementById('message-list');
    const form = document.getElementById('message-form');
    const button = document.getElementById('message-submit-button');

    if (messageList) {
        messageList.scrollTop = messageList.scrollHeight;
    }

    if (form && button) {
        form.addEventListener('submit', function () {
            button.disabled = true;
            button.textContent = '送信中...';
        });
    }
});
</script>
@endsection
