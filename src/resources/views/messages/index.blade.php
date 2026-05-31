@extends('layouts.app')

@section('title', 'メッセージ一覧')

@section('content')
<div class="min-h-screen bg-slate-50">
    <div class="mx-auto max-w-5xl px-4 py-10 sm:px-6 lg:px-8">
        <div class="mb-8">
            <p class="text-sm font-bold text-indigo-600">MESSAGES</p>

            <h1 class="mt-2 text-3xl font-bold text-slate-900">
                メッセージ一覧
            </h1>

            <p class="mt-2 text-slate-600">
                募集に関するメッセージを確認できます。
            </p>
        </div>

        <div class="space-y-4">
            @forelse ($messages as $messageItem)
                @php
                    /*
                    |--------------------------------------------------------------------------
                    | メッセージ一覧用の表示データを整える
                    |--------------------------------------------------------------------------
                    |
                    | Controller側で groupBy() している場合、$messageItem は Collection になります。
                    | groupBy() していない場合、$messageItem は Message モデル1件になります。
                    | どちらでも動くように、ここで最新メッセージ1件に揃えます。
                    |
                    */

                    $message = $messageItem instanceof \Illuminate\Support\Collection
                        ? $messageItem->first()
                        : $messageItem;

                    $partner = null;
                    $unreadCount = 0;

                    if ($message) {
                        $partner = $message->sender_id === auth()->id()
                            ? $message->receiver
                            : $message->sender;

                        if ($partner) {
                            $unreadCount = \App\Models\Message::query()
                                ->where('work_post_id', $message->work_post_id)
                                ->where('sender_id', $partner->id)
                                ->where('receiver_id', auth()->id())
                                ->whereNull('read_at')
                                ->count();
                        }
                    }
                @endphp

                @if ($message && $partner && $message->workPost)
                    <article class="rounded-2xl bg-white p-5 shadow-sm ring-1 ring-slate-200 transition hover:-translate-y-0.5 hover:shadow-md">
                        <div class="flex flex-col gap-4 sm:flex-row sm:items-start sm:justify-between">
                            <div class="min-w-0">
                                <div class="flex flex-wrap items-center gap-2">
                                    <span class="rounded-full bg-indigo-50 px-3 py-1 text-xs font-bold text-indigo-700">
                                        募集
                                    </span>

                                    @if ($unreadCount > 0)
                                        <span class="rounded-full bg-rose-600 px-3 py-1 text-xs font-bold text-white">
                                            未読 {{ $unreadCount }}
                                        </span>
                                    @endif

                                    <span class="rounded-full bg-slate-100 px-3 py-1 text-xs font-bold text-slate-600">
                                        {{ $message->created_at?->format('Y/m/d H:i') }}
                                    </span>
                                </div>

                                <h2 class="mt-3 text-lg font-bold text-slate-900">
                                    <a
                                        href="{{ route('messages.show', [$message->workPost, $partner]) }}"
                                        class="hover:text-indigo-600"
                                    >
                                        {{ $message->workPost->title }}
                                    </a>
                                </h2>

                                <p class="mt-2 text-sm text-slate-600">
                                    相手：
                                    <span class="font-semibold text-slate-800">
                                        {{ $partner->profile?->display_name ?? $partner->name }}
                                    </span>
                                </p>

                                <p class="mt-3 line-clamp-2 text-sm leading-6 text-slate-600">
                                    {{ \Illuminate\Support\Str::limit($message->body, 80) }}
                                </p>
                            </div>

                            <div class="shrink-0">
                                <a
                                    href="{{ route('messages.show', [$message->workPost, $partner]) }}"
                                    class="inline-flex items-center justify-center rounded-xl bg-indigo-600 px-4 py-2 text-sm font-bold text-white shadow-sm transition hover:bg-indigo-700"
                                >
                                    メッセージを見る
                                </a>
                            </div>
                        </div>
                    </article>
                @endif
            @empty
                <div class="rounded-2xl bg-white p-10 text-center shadow-sm ring-1 ring-slate-200">
                    <div class="mx-auto flex h-14 w-14 items-center justify-center rounded-full bg-slate-100 text-2xl">
                        💬
                    </div>

                    <h2 class="mt-4 text-lg font-bold text-slate-900">
                        メッセージはありません
                    </h2>

                    <p class="mt-2 text-sm leading-6 text-slate-600">
                        募集への参加や相談が始まると、ここにメッセージが表示されます。
                    </p>

                    <div class="mt-6">
                        <a
                            href="{{ route('work-posts.index') }}"
                            class="inline-flex items-center justify-center rounded-xl bg-indigo-600 px-5 py-3 text-sm font-bold text-white transition hover:bg-indigo-700"
                        >
                            募集を探す
                        </a>
                    </div>
                </div>
            @endforelse
        </div>
    </div>
</div>
@endsection
