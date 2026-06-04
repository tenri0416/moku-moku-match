{{-- SP版：resources/views/messages/user-show_sp.blade.php --}}
@php
    $hasBlocked = $hasBlocked ?? false;
    $isBlockedByTarget = $isBlockedByTarget ?? false;
    $hasBlockRelation = $hasBlockRelation ?? ($hasBlocked || $isBlockedByTarget);

    $profileUrl = Route::has('users.show')
        ? route('users.show', $user)
        : '#';
@endphp

<div class="block md:hidden min-h-screen w-full overflow-x-hidden bg-[#F8FAFF] text-[#071433]">
    <div class="mx-auto flex min-h-screen w-full max-w-[430px] flex-col overflow-x-hidden bg-[#F8FAFF] px-3 pb-5 pt-4">

        {{-- 上部 --}}
        <header class="mb-4">
            <div class="mb-4 flex items-center justify-between">
                <a
                    href="{{ route('messages.index') }}"
                    class="inline-flex items-center gap-1 text-[16px] font-bold text-[#0D4FE8]"
                >
                    <span class="text-[28px] leading-none">‹</span>
                    一覧へ戻る
                </a>

                <a
                    href="{{ $profileUrl }}"
                    class="rounded-full border border-[#DDE6F5] bg-white px-4 py-2 text-[14px] font-black text-[#0D4FE8] shadow-[0_8px_18px_rgba(15,43,95,0.06)]"
                >
                    プロフィール
                </a>
            </div>

            <div class="rounded-[18px] border border-[#DDE6F5] bg-white px-4 py-4 shadow-[0_8px_22px_rgba(15,43,95,0.06)]">
                <div class="flex items-center gap-4">
                    <a
                        href="{{ $profileUrl }}"
                        class="h-[66px] w-[66px] shrink-0 overflow-hidden rounded-full bg-blue-50"
                    >
                        <img
                            src="{{ $partnerAvatarUrl }}"
                            alt="{{ $partnerDisplayName }}のプロフィール画像"
                            class="h-full w-full object-cover"
                        >
                    </a>

                    <div class="min-w-0 flex-1">
                        <p class="text-[13px] font-black tracking-[0.16em] text-[#0D4FE8]">
                            DIRECT MESSAGE
                        </p>

                        <h1 class="mt-1 truncate text-[24px] font-black text-[#071433]">
                            {{ $partnerDisplayName }}
                        </h1>

                        <p class="mt-1 truncate text-[15px] font-bold text-[#0D4FE8]">
                            {{ $partnerJobType }}
                        </p>
                    </div>
                </div>

                @if ($hasBlocked)
                    <div class="mt-3 rounded-[14px] border border-rose-200 bg-rose-50 px-4 py-3">
                        <p class="text-[14px] font-black text-rose-700">
                            このユーザーをブロック中です
                        </p>
                    </div>
                @elseif ($isBlockedByTarget)
                    <div class="mt-3 rounded-[14px] border border-slate-200 bg-slate-100 px-4 py-3">
                        <p class="text-[14px] font-black text-slate-600">
                            現在、このユーザーにはメッセージを送信できません
                        </p>
                    </div>
                @endif
            </div>
        </header>

        {{-- メッセージ --}}
        <section class="min-h-0 flex-1 overflow-hidden rounded-[18px] border border-[#DDE6F5] bg-white shadow-[0_8px_22px_rgba(15,43,95,0.06)]">
            <div
                id="message-list-sp"
                class="h-[calc(100vh-330px)] min-h-[380px] space-y-5 overflow-y-auto px-4 py-5"
                data-message-list
            >
                @forelse ($messages as $message)
                    @php
                        $isMine = (int) $message->sender_id === (int) auth()->id();

                        $senderProfile = $message->sender?->profile;
                        $senderName = $senderProfile?->display_name ?? $message->sender?->name ?? 'ユーザー';

                        $senderAvatarPath = $senderProfile?->avatar_path;
                        $senderAvatarUrl = $senderAvatarPath
                            ? asset('storage/' . ltrim($senderAvatarPath, '/'))
                            : asset('images/default-avatar.png');
                    @endphp

                    <div class="flex {{ $isMine ? 'justify-end' : 'justify-start' }}" data-message-id="{{ $message->id }}">
                        <div class="flex max-w-[86%] gap-2 {{ $isMine ? 'flex-row-reverse' : '' }}">
                            @unless ($isMine)
                                <div class="h-9 w-9 shrink-0 overflow-hidden rounded-full bg-blue-50">
                                    <img
                                        src="{{ $senderAvatarUrl }}"
                                        alt="{{ $senderName }}のプロフィール画像"
                                        class="h-full w-full object-cover"
                                    >
                                </div>
                            @endunless

                            <div>
                                <div class="mb-1 flex items-center gap-2 {{ $isMine ? 'justify-end' : 'justify-start' }}">
                                    <span class="text-[11px] font-bold text-[#64748B]">
                                        {{ $isMine ? 'あなた' : $senderName }}
                                    </span>

                                    <span class="text-[11px] font-bold text-[#94A3B8]">
                                        {{ optional($message->created_at)->format('H:i') }}
                                    </span>
                                </div>

                                <div class="{{ $isMine ? 'rounded-br-[6px] bg-[#0D4FE8] text-white' : 'rounded-bl-[6px] bg-[#F1F5F9] text-[#071433]' }} rounded-[18px] px-4 py-3 text-[15px] font-bold leading-7 shadow-sm">
                                    {!! nl2br(e($message->body)) !!}
                                </div>

                                @if ($isMine)
                                    <div class="mt-1 text-right text-[11px] font-bold text-[#94A3B8]">
                                        {{ $message->read_at ? '既読' : '未読' }}
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="flex h-full items-center justify-center text-center" data-empty-message>
                        <div>
                            <div class="mx-auto flex h-16 w-16 items-center justify-center rounded-full bg-blue-50 text-[34px]">
                                💬
                            </div>

                            <h2 class="mt-4 text-[20px] font-black text-[#071433]">
                                まだメッセージはありません
                            </h2>

                            <p class="mt-2 text-[14px] font-bold leading-relaxed text-[#64748B]">
                                最初のメッセージを送ってみましょう。
                            </p>
                        </div>
                    </div>
                @endforelse
            </div>
        </section>

        {{-- 送信フォーム --}}
        <section class="mt-4 rounded-[18px] border border-[#DDE6F5] bg-white px-4 py-4 shadow-[0_8px_22px_rgba(15,43,95,0.06)]">
            @if ($hasBlocked)
                <div class="rounded-[14px] border border-rose-200 bg-rose-50 px-4 py-4">
                    <p class="text-[15px] font-black text-rose-700">
                        このユーザーをブロック中のため、メッセージを送信できません。
                    </p>

                    <p class="mt-2 text-[14px] font-bold leading-6 text-rose-600">
                        プロフィール画面からブロックを解除してください。
                    </p>

                    <div class="mt-4 flex items-center gap-3">
                        <a
                            href="{{ route('messages.index') }}"
                            class="flex h-[52px] w-[92px] shrink-0 items-center justify-center rounded-[14px] border border-[#CBD7EA] bg-white text-[15px] font-black text-[#071433]"
                        >
                            戻る
                        </a>

                        <a
                            href="{{ $profileUrl }}"
                            class="flex h-[52px] flex-1 items-center justify-center rounded-[14px] bg-white text-[15px] font-black text-rose-600 ring-1 ring-rose-200"
                        >
                            解除する
                        </a>

                        <button
                            type="button"
                            disabled
                            class="flex h-[52px] flex-1 items-center justify-center rounded-[14px] bg-slate-300 text-[16px] font-black text-slate-600 cursor-not-allowed"
                        >
                            ブロック中
                        </button>
                    </div>
                </div>
            @elseif ($isBlockedByTarget)
                <div class="rounded-[14px] border border-slate-200 bg-slate-100 px-4 py-4">
                    <p class="text-[15px] font-black text-slate-700">
                        現在、このユーザーにはメッセージを送信できません。
                    </p>

                    <p class="mt-2 text-[14px] font-bold leading-6 text-slate-600">
                        過去のメッセージは確認できますが、新しいメッセージは送信できません。
                    </p>

                    <div class="mt-4 flex items-center gap-3">
                        <a
                            href="{{ route('messages.index') }}"
                            class="flex h-[52px] w-[116px] shrink-0 items-center justify-center rounded-[14px] border border-[#CBD7EA] bg-white text-[16px] font-black text-[#071433]"
                        >
                            戻る
                        </a>

                        <button
                            type="button"
                            disabled
                            class="flex h-[52px] flex-1 items-center justify-center rounded-[14px] bg-slate-300 text-[16px] font-black text-slate-600 cursor-not-allowed"
                        >
                            送信できません
                        </button>
                    </div>
                </div>
            @else
                <form
                    method="POST"
                    action="{{ route('messages.users.store', $user) }}"
                    data-message-form
                >
                    @csrf

                    <textarea
                        name="body"
                        rows="3"
                        required
                        maxlength="2000"
                        placeholder="メッセージを入力してください"
                        class="block min-h-[96px] w-full resize-none rounded-[14px] border border-[#CBD7EA] bg-white px-4 py-3 text-[16px] font-bold leading-7 text-[#071433] outline-none placeholder:text-[#94A3B8] focus:border-[#0D4FE8] focus:ring-4 focus:ring-blue-100"
                        data-message-body
                    >{{ old('body') }}</textarea>

                    @if ($errors->any())
                        <p class="mt-2 rounded-xl bg-red-50 px-3 py-2 text-[13px] font-bold text-red-600">
                            {{ $errors->first() }}
                        </p>
                    @endif

                    <div class="mt-3 flex items-center gap-3">
                        <a
                            href="{{ route('messages.index') }}"
                            class="flex h-[52px] w-[116px] shrink-0 items-center justify-center rounded-[14px] border border-[#CBD7EA] bg-white text-[16px] font-black text-[#071433]"
                        >
                            戻る
                        </a>

                        <button
                            type="submit"
                            class="flex h-[52px] flex-1 items-center justify-center rounded-[14px] bg-[#0D4FE8] text-[17px] font-black text-white shadow-[0_10px_18px_rgba(13,79,232,0.24)] disabled:cursor-not-allowed disabled:opacity-60"
                            data-message-submit
                        >
                            送信する
                        </button>
                    </div>
                </form>
            @endif
        </section>
    </div>
</div>
