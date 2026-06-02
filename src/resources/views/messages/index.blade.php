@extends('layouts.app')

@section('title', 'メッセージ一覧')

@section('content')
<div class="min-h-screen bg-slate-50">
    <div class="mx-auto max-w-5xl px-4 py-10 sm:px-6 lg:px-8">
        {{-- Header --}}
        <div class="mb-8">
            <p class="text-sm font-bold text-indigo-600">
                MESSAGES
            </p>

            <h1 class="mt-2 text-3xl font-bold text-slate-900">
                メッセージ一覧
            </h1>

            <p class="mt-2 text-slate-600">
                ユーザー同士のメッセージ履歴を確認できます。
            </p>
        </div>

        {{-- Flash --}}
        @if (session('success'))
            <div class="mb-6 rounded-xl bg-emerald-50 px-4 py-3 text-sm font-bold text-emerald-700">
                {{ session('success') }}
            </div>
        @endif

        {{-- List --}}
        <section class="rounded-2xl bg-white p-6 shadow-sm ring-1 ring-slate-200">
            <div class="mb-5 flex items-center justify-between">
                <h2 class="text-xl font-bold text-slate-900">
                    会話一覧
                </h2>

                <p class="text-sm font-semibold text-slate-500">
                    {{ $messages->count() }}件
                </p>
            </div>

            <div class="space-y-4">
                @forelse ($messages as $message)
                    @php
                        $loginUserId = auth()->id();

                        // 自分ではない相手ユーザーを取得
                        $partner = $message->sender_id === $loginUserId
                            ? $message->receiver
                            : $message->sender;

                        $partnerProfile = $partner?->profile;
                        $displayName = $partnerProfile?->display_name ?? $partner?->name ?? 'ユーザー';
                        $jobType = $partnerProfile?->job_type ?? '職種未設定';

                        $avatarPath = $partnerProfile?->avatar_path;
                        $avatarUrl = $avatarPath
                            ? asset('storage/' . $avatarPath)
                            : asset('images/default-avatar.png');

                        // work_post_id は使わず、sender_id / receiver_id のみで未読数を集計
                        $unreadCount = 0;

                        if ($partner) {
                            $unreadCount = \App\Models\Message::query()
                                ->where('sender_id', $partner->id)
                                ->where('receiver_id', $loginUserId)
                                ->whereNull('read_at')
                                ->count();
                        }

                        $lastMessageBody = \Illuminate\Support\Str::limit($message->body, 80);
                    @endphp

                    @if ($partner)
                        <article class="rounded-2xl border border-slate-200 bg-slate-50/60 p-5 transition hover:border-indigo-300 hover:bg-white hover:shadow-sm">
                            <a href="{{ route('messages.users.show', $partner) }}" class="block">
                                <div class="flex items-start gap-4">
                                    {{-- Avatar --}}
                                    <img
                                        src="{{ $avatarUrl }}"
                                        alt="{{ $displayName }}のプロフィール画像"
                                        class="h-14 w-14 flex-shrink-0 rounded-full border border-slate-200 bg-white object-cover"
                                    >

                                    {{-- Content --}}
                                    <div class="min-w-0 flex-1">
                                        <div class="flex flex-col gap-2 sm:flex-row sm:items-start sm:justify-between">
                                            <div class="min-w-0">
                                                <div class="flex flex-wrap items-center gap-2">
                                                    <h3 class="truncate text-lg font-bold text-slate-900">
                                                        {{ $displayName }}
                                                    </h3>

                                                    @if ($unreadCount > 0)
                                                        <span class="rounded-full bg-rose-500 px-2.5 py-1 text-xs font-bold text-white">
                                                            未読 {{ $unreadCount > 99 ? '99+' : $unreadCount }}
                                                        </span>
                                                    @endif
                                                </div>

                                                <p class="mt-1 truncate text-sm font-semibold text-slate-500">
                                                    {{ $jobType }}
                                                </p>
                                            </div>

                                            <div class="text-xs font-semibold text-slate-400">
                                                {{ optional($message->created_at)->format('Y/m/d H:i') }}
                                            </div>
                                        </div>

                                        <p class="mt-3 text-sm leading-6 text-slate-600">
                                            @if ($message->sender_id === $loginUserId)
                                                <span class="font-bold text-slate-500">あなた：</span>
                                            @else
                                                <span class="font-bold text-indigo-600">{{ $displayName }}：</span>
                                            @endif

                                            {{ $lastMessageBody }}
                                        </p>
                                    </div>

                                    {{-- Arrow --}}
                                    <div class="hidden text-slate-400 sm:block">
                                        →
                                    </div>
                                </div>
                            </a>
                        </article>
                    @endif
                @empty
                    <div class="rounded-2xl bg-slate-50 p-10 text-center ring-1 ring-slate-200">
                        <div class="mx-auto flex h-14 w-14 items-center justify-center rounded-full bg-white text-2xl">
                            💬
                        </div>

                        <h3 class="mt-4 text-lg font-bold text-slate-900">
                            まだメッセージはありません
                        </h3>

                        <p class="mt-2 text-sm leading-6 text-slate-600">
                            ランキングやユーザープロフィール画面から、気になるユーザーにメッセージを送ってみましょう。
                        </p>

                        <div class="mt-6">
                            <a
                                href="{{ route('trainings.ranking') }}"
                                class="inline-flex items-center justify-center rounded-xl bg-indigo-600 px-5 py-3 text-sm font-bold text-white transition hover:bg-indigo-700"
                            >
                                ランキングを見る
                            </a>
                        </div>
                    </div>
                @endforelse
            </div>
        </section>
    </div>
</div>
@endsection
