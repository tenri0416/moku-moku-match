@extends('layouts.app')

@section('content')
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
                            alt="{{ optional($user->profile)->display_name ?? $user->name }}"
                            class="w-full h-full object-cover"
                        >
                    @else
                        <span class="text-5xl font-bold text-gray-500">
                            {{ mb_substr(optional($user->profile)->display_name ?? $user->name, 0, 1) }}
                        </span>
                    @endif
                </div>
            </div>

            {{-- ユーザー基本情報 --}}
            <div class="flex-1 text-center md:text-left">
                <div class="flex flex-col md:flex-row md:items-start md:justify-between gap-4">
                    <div>
                        <h1 class="text-2xl md:text-3xl font-bold text-gray-900">
                            {{ optional($user->profile)->display_name ?? $user->name }}
                        </h1>

                        <p class="text-gray-600 mt-2">
                            {{ optional($user->profile)->job_type ?? '職種未設定' }}
                        </p>

                        @if(optional($user->profile)->prefecture)
                            <p class="text-sm text-gray-500 mt-1">
                                {{ optional($user->profile->prefecture)->name }}
                            </p>
                        @endif
                    </div>

                    {{-- アクションボタン --}}
                    <div class="flex flex-col sm:flex-row md:flex-col gap-3 md:min-w-[180px]">
                        @auth
                            @if(auth()->id() !== $user->id)
                                <a
                                    href="{{ route('messages.users.show', $user) }}"
                                    class="inline-flex items-center justify-center px-5 py-3 bg-blue-600 text-white font-semibold rounded-xl hover:bg-blue-700 transition"
                                >
                                    メッセージを送る
                                </a>
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
                    {{ $totalPoints ?? 0 }} pt
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
            <h2 class="text-lg font-bold text-gray-900 mb-3">
                自己紹介
            </h2>

            <div class="bg-gray-50 rounded-xl p-4 text-gray-700 leading-relaxed whitespace-pre-line">
                {{ optional($user->profile)->bio ?? '自己紹介はまだ登録されていません。' }}
            </div>
        </section>

        <section class="bg-white rounded-2xl shadow p-6">
            <h2 class="text-lg font-bold text-gray-900 mb-3">
                スキル
            </h2>

            <div class="bg-gray-50 rounded-xl p-4 text-gray-700 leading-relaxed whitespace-pre-line">
                {{ optional($user->profile)->skills ?? 'スキルはまだ登録されていません。' }}
            </div>
        </section>

        <section class="bg-white rounded-2xl shadow p-6">
            <h2 class="text-lg font-bold text-gray-900 mb-3">
                利用目的
            </h2>

            <div class="bg-gray-50 rounded-xl p-4 text-gray-700 leading-relaxed whitespace-pre-line">
                {{ optional($user->profile)->purpose ?? '利用目的はまだ登録されていません。' }}
            </div>
        </section>

        <section class="bg-white rounded-2xl shadow p-6">
            <h2 class="text-lg font-bold text-gray-900 mb-3">
                作業スタイル
            </h2>

            <div class="bg-gray-50 rounded-xl p-4 text-gray-700 leading-relaxed whitespace-pre-line">
                {{ optional($user->profile)->work_style ?? '作業スタイルはまだ登録されていません。' }}
            </div>
        </section>
    </div>

    {{-- 下部メッセージ導線 --}}
    @auth
        @if(auth()->id() !== $user->id)
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
@endsection
