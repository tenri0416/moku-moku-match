@extends('layouts.app')

@section('content')
@php
    $loginUser = auth()->user();

    $isMine = $loginUser && (int) $loginUser->id === (int) $user->id;

    $hasBlocked = false;
    $isBlockedByTarget = false;
    $hasBlockRelation = false;

    if ($loginUser && ! $isMine) {
        $hasBlocked = $loginUser->hasBlocked($user);
        $isBlockedByTarget = $loginUser->isBlockedBy($user);
        $hasBlockRelation = $hasBlocked || $isBlockedByTarget;
    }

    $displayName = optional($user->profile)->display_name ?? $user->name;
@endphp

<div class="max-w-5xl mx-auto px-4 py-8">
    {{-- プロフィールヘッダー --}}
    <div class="bg-white rounded-2xl shadow p-6 md:p-8">
        <div class="flex flex-col md:flex-row md:items-center gap-6">
            {{-- プロフィール画像 --}}
            <div class="flex justify-center md:justify-start">
                <div class="w-32 h-32 md:w-36 md:h-36 rounded-full bg-gray-200 overflow-hidden flex items-center justify-center ring-4 ring-blue-100">
                    @if(optional($user->profile)->avatar_path)
                        <img
                            src="{{ asset('storage/' . $user->profile->avatar_path) }}"
                            alt="{{ $displayName }}"
                            class="w-full h-full object-cover"
                        >
                    @else
                        <span class="text-5xl font-bold text-gray-500">
                            {{ mb_substr($displayName, 0, 1) }}
                        </span>
                    @endif
                </div>
            </div>

            {{-- ユーザー基本情報 --}}
            <div class="flex-1 text-center md:text-left">
                <div class="flex flex-col md:flex-row md:items-start md:justify-between gap-4">
                    <div>
                        <h1 class="text-2xl md:text-3xl font-bold text-gray-900">
                            {{ $displayName }}
                        </h1>

                        <p class="text-gray-600 mt-2">
                            {{ optional($user->profile)->job_type ?? '職種未設定' }}
                        </p>

                        @if(optional($user->profile)->prefecture)
                            <p class="text-sm text-gray-500 mt-1">
                                {{ optional($user->profile->prefecture)->name }}
                            </p>
                        @endif

                        @auth
                            @if(! $isMine && $hasBlocked)
                                <p class="mt-3 inline-flex rounded-full bg-rose-50 px-3 py-1 text-sm font-bold text-rose-700">
                                    このユーザーをブロック中です
                                </p>
                            @elseif(! $isMine && $isBlockedByTarget)
                                <p class="mt-3 inline-flex rounded-full bg-slate-100 px-3 py-1 text-sm font-bold text-slate-600">
                                    現在、このユーザーにはメッセージを送信できません
                                </p>
                            @endif
                        @endauth
                    </div>

                    {{-- アクションボタン --}}
                    <div class="flex flex-col sm:flex-row md:flex-col gap-3 md:min-w-[200px]">
                        @auth
                            @if(! $isMine)
                                @if($hasBlocked)
                                    <button
                                        type="button"
                                        disabled
                                        class="inline-flex items-center justify-center px-5 py-3 bg-slate-200 text-slate-500 font-semibold rounded-xl cursor-not-allowed"
                                    >
                                        ブロック中
                                    </button>

                                    <button
                                        type="button"
                                        data-open-unblock-modal
                                        class="inline-flex items-center justify-center px-5 py-3 bg-white text-slate-700 font-semibold rounded-xl border border-slate-300 hover:bg-slate-50 transition"
                                    >
                                        ブロック解除
                                    </button>
                                @elseif($isBlockedByTarget)
                                    <button
                                        type="button"
                                        disabled
                                        class="inline-flex items-center justify-center px-5 py-3 bg-slate-200 text-slate-500 font-semibold rounded-xl cursor-not-allowed"
                                    >
                                        メッセージ送信不可
                                    </button>
                                @else
                                    <a
                                        href="{{ route('messages.users.show', $user) }}"
                                        class="inline-flex items-center justify-center px-5 py-3 bg-blue-600 text-white font-semibold rounded-xl hover:bg-blue-700 transition"
                                    >
                                        メッセージを送る
                                    </a>

                                    <button
                                        type="button"
                                        data-open-block-modal
                                        class="inline-flex items-center justify-center px-5 py-3 bg-white text-rose-600 font-semibold rounded-xl border border-rose-200 hover:bg-rose-50 transition"
                                    >
                                        ブロックする
                                    </button>
                                @endif
                            @else
                                <a
                                    href="{{ route('profile.edit') }}"
                                    class="inline-flex items-center justify-center px-5 py-3 bg-gray-900 text-white font-semibold rounded-xl hover:bg-gray-800 transition"
                                >
                                    プロフィールを編集
                                </a>
                            @endif
                        @else
                            <a
                                href="{{ route('login') }}"
                                class="inline-flex items-center justify-center px-5 py-3 bg-blue-600 text-white font-semibold rounded-xl hover:bg-blue-700 transition"
                            >
                                ログインしてメッセージを送る
                            </a>
                        @endauth
                    </div>
                </div>
            </div>
        </div>

        {{-- ポイント情報 --}}
        <div class="mt-8 grid grid-cols-1 md:grid-cols-3 gap-4">
            <div class="bg-blue-50 rounded-xl p-5 text-center">
                <div class="text-sm text-blue-600 font-semibold">総ポイント</div>
                <div class="text-3xl font-bold text-gray-900 mt-2">
                    {{ $myTotalPoints ?? 0 }} pt
                </div>
            </div>

            <div class="bg-green-50 rounded-xl p-5 text-center">
                <div class="text-sm text-green-600 font-semibold">月間ポイント</div>
                <div class="text-3xl font-bold text-gray-900 mt-2">
                    {{ $monthlyPoints ?? 0 }} pt
                </div>
            </div>

            <div class="bg-yellow-50 rounded-xl p-5 text-center">
                <div class="text-sm text-yellow-600 font-semibold">トレーニング回数</div>
                <div class="text-3xl font-bold text-gray-900 mt-2">
                    {{ $trainingCount ?? 0 }} 回
                </div>
            </div>
        </div>
    </div>

    {{-- プロフィール詳細 --}}
    <div class="mt-8 space-y-6">
        <section class="bg-white rounded-2xl shadow p-6">
            <h2 class="text-lg font-bold text-gray-900 mb-3">自己紹介</h2>

            <div class="bg-gray-50 rounded-xl p-4 text-gray-700 leading-relaxed whitespace-pre-line">
                {{ optional($user->profile)->bio ?? '自己紹介はまだ登録されていません。' }}
            </div>
        </section>

        <section class="bg-white rounded-2xl shadow p-6">
            <h2 class="text-lg font-bold text-gray-900 mb-3">スキル</h2>

            <div class="bg-gray-50 rounded-xl p-4 text-gray-700 leading-relaxed whitespace-pre-line">
                {{ optional($user->profile)->skills ?? 'スキルはまだ登録されていません。' }}
            </div>
        </section>

        <section class="bg-white rounded-2xl shadow p-6">
            <h2 class="text-lg font-bold text-gray-900 mb-3">利用目的</h2>

            <div class="bg-gray-50 rounded-xl p-4 text-gray-700 leading-relaxed whitespace-pre-line">
                {{ optional($user->profile)->purpose ?? '利用目的はまだ登録されていません。' }}
            </div>
        </section>

        <section class="bg-white rounded-2xl shadow p-6">
            <h2 class="text-lg font-bold text-gray-900 mb-3">作業スタイル</h2>

            <div class="bg-gray-50 rounded-xl p-4 text-gray-700 leading-relaxed whitespace-pre-line">
                {{ optional($user->profile)->work_style ?? '作業スタイルはまだ登録されていません。' }}
            </div>
        </section>
    </div>

    {{-- 下部メッセージ導線 --}}
    @auth
        @if(! $isMine)
            @if($hasBlocked)
                <div class="mt-8 bg-rose-50 border border-rose-200 rounded-2xl shadow p-6 text-center">
                    <h2 class="text-xl font-bold text-rose-700">
                        このユーザーをブロック中です
                    </h2>

                    <p class="mt-2 text-rose-600">
                        ブロック中は、このユーザーにメッセージを送信できません。
                    </p>

                    <button
                        type="button"
                        data-open-unblock-modal
                        class="inline-flex items-center justify-center mt-5 px-6 py-3 bg-white text-rose-600 font-semibold rounded-xl border border-rose-200 hover:bg-rose-50 transition"
                    >
                        ブロックを解除する
                    </button>
                </div>
            @elseif($isBlockedByTarget)
                <div class="mt-8 bg-slate-100 border border-slate-200 rounded-2xl shadow p-6 text-center">
                    <h2 class="text-xl font-bold text-slate-700">
                        メッセージを送信できません
                    </h2>

                    <p class="mt-2 text-slate-600">
                        現在、このユーザーにはメッセージを送信できません。
                    </p>
                </div>
            @else
                <div class="mt-8 bg-blue-600 rounded-2xl shadow p-6 text-center text-white">
                    <h2 class="text-xl font-bold">
                        このユーザーにメッセージを送りますか？
                    </h2>

                    <p class="mt-2 text-blue-100">
                        募集に関係なく、直接メッセージを送ることができます。
                    </p>

                    <a
                        href="{{ route('messages.users.show', $user) }}"
                        class="inline-flex items-center justify-center mt-5 px-6 py-3 bg-white text-blue-600 font-semibold rounded-xl hover:bg-blue-50 transition"
                    >
                        メッセージを送る
                    </a>
                </div>
            @endif
        @endif
    @else
        <div class="mt-8 bg-blue-600 rounded-2xl shadow p-6 text-center text-white">
            <h2 class="text-xl font-bold">
                メッセージを送るにはログインが必要です
            </h2>

            <p class="mt-2 text-blue-100">
                ログインすると、このユーザーに直接メッセージを送れます。
            </p>

            <a
                href="{{ route('login') }}"
                class="inline-flex items-center justify-center mt-5 px-6 py-3 bg-white text-blue-600 font-semibold rounded-xl hover:bg-blue-50 transition"
            >
                ログインする
            </a>
        </div>
    @endauth
</div>

@auth
    @if(! $isMine)
        {{-- ブロック確認モーダル --}}
        <div
            id="blockConfirmModal"
            class="fixed inset-0 z-[100] hidden items-center justify-center bg-slate-900/40 px-4"
            data-block-modal
        >
            <div class="w-full max-w-md rounded-2xl bg-white p-6 shadow-2xl">
                <h2 class="text-xl font-black text-slate-900">
                    ユーザーをブロックしますか？
                </h2>

                <p class="mt-3 text-sm leading-6 text-slate-600">
                    {{ $displayName }}さんをブロックすると、このユーザーにメッセージを送信できなくなります。
                    ブロックはプロフィール画面からいつでも解除できます。
                </p>

                <div class="mt-6 flex gap-3">
                    <button
                        type="button"
                        data-close-block-modal
                        class="flex-1 rounded-xl border border-slate-300 bg-white px-4 py-3 text-sm font-bold text-slate-700 hover:bg-slate-50"
                    >
                        キャンセル
                    </button>

                    <form method="POST" action="{{ route('users.block', $user) }}" class="flex-1">
                        @csrf

                        <button
                            type="submit"
                            class="w-full rounded-xl bg-rose-600 px-4 py-3 text-sm font-bold text-white hover:bg-rose-700"
                        >
                            ブロックする
                        </button>
                    </form>
                </div>
            </div>
        </div>

        {{-- ブロック解除確認モーダル --}}
        <div
            id="unblockConfirmModal"
            class="fixed inset-0 z-[100] hidden items-center justify-center bg-slate-900/40 px-4"
            data-unblock-modal
        >
            <div class="w-full max-w-md rounded-2xl bg-white p-6 shadow-2xl">
                <h2 class="text-xl font-black text-slate-900">
                    ブロックを解除しますか？
                </h2>

                <p class="mt-3 text-sm leading-6 text-slate-600">
                    {{ $displayName }}さんのブロックを解除すると、再びメッセージを送信できるようになります。
                </p>

                <div class="mt-6 flex gap-3">
                    <button
                        type="button"
                        data-close-unblock-modal
                        class="flex-1 rounded-xl border border-slate-300 bg-white px-4 py-3 text-sm font-bold text-slate-700 hover:bg-slate-50"
                    >
                        キャンセル
                    </button>

                    <form method="POST" action="{{ route('users.unblock', $user) }}" class="flex-1">
                        @csrf
                        @method('DELETE')

                        <button
                            type="submit"
                            class="w-full rounded-xl bg-slate-900 px-4 py-3 text-sm font-bold text-white hover:bg-slate-800"
                        >
                            解除する
                        </button>
                    </form>
                </div>
            </div>
        </div>

        <script>
            document.addEventListener('DOMContentLoaded', function () {
                const blockModal = document.querySelector('[data-block-modal]');
                const unblockModal = document.querySelector('[data-unblock-modal]');

                const openBlockButtons = document.querySelectorAll('[data-open-block-modal]');
                const closeBlockButtons = document.querySelectorAll('[data-close-block-modal]');

                const openUnblockButtons = document.querySelectorAll('[data-open-unblock-modal]');
                const closeUnblockButtons = document.querySelectorAll('[data-close-unblock-modal]');

                function openModal(modal) {
                    if (!modal) return;
                    modal.classList.remove('hidden');
                    modal.classList.add('flex');
                }

                function closeModal(modal) {
                    if (!modal) return;
                    modal.classList.add('hidden');
                    modal.classList.remove('flex');
                }

                openBlockButtons.forEach(function (button) {
                    button.addEventListener('click', function () {
                        openModal(blockModal);
                    });
                });

                closeBlockButtons.forEach(function (button) {
                    button.addEventListener('click', function () {
                        closeModal(blockModal);
                    });
                });

                openUnblockButtons.forEach(function (button) {
                    button.addEventListener('click', function () {
                        openModal(unblockModal);
                    });
                });

                closeUnblockButtons.forEach(function (button) {
                    button.addEventListener('click', function () {
                        closeModal(unblockModal);
                    });
                });

                [blockModal, unblockModal].forEach(function (modal) {
                    if (!modal) return;

                    modal.addEventListener('click', function (event) {
                        if (event.target === modal) {
                            closeModal(modal);
                        }
                    });
                });
            });
        </script>
    @endif
@endauth
@endsection
