@extends('layouts.app')

@section('content')
<div class="max-w-4xl mx-auto px-4 py-8">
    <div class="bg-white rounded-2xl shadow p-6">
        <div class="flex items-center gap-6">
            <div class="w-24 h-24 rounded-full bg-gray-200 overflow-hidden flex items-center justify-center">
                @if(optional($user->profile)->avatar_path)
                    <img
                        src="{{ asset('storage/' . $user->profile->avatar_path) }}"
                        alt="{{ optional($user->profile)->display_name ?? $user->name }}"
                        class="w-full h-full object-cover"
                    >
                @else
                    <span class="text-3xl font-bold text-gray-500">
                        {{ mb_substr(optional($user->profile)->display_name ?? $user->name, 0, 1) }}
                    </span>
                @endif
            </div>

            <div class="flex-1">
                <h1 class="text-2xl font-bold text-gray-900">
                    {{ optional($user->profile)->display_name ?? $user->name }}
                </h1>

                <p class="text-gray-600 mt-1">
                    {{ optional($user->profile)->job_type ?? '職種未設定' }}
                </p>

                @auth
                    @if(auth()->id() !== $user->id)
                        <a
                            href="{{ route('messages.users.show', $user) }}"
                            class="inline-block mt-4 px-5 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700"
                        >
                            メッセージを送る
                        </a>
                    @endif
                @else
                    <a
                        href="{{ route('login') }}"
                        class="inline-block mt-4 px-5 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700"
                    >
                        ログインしてメッセージを送る
                    </a>
                @endauth
            </div>
        </div>

        <div class="mt-8 grid grid-cols-1 md:grid-cols-3 gap-4">
            <div class="bg-gray-50 rounded-xl p-4">
                <div class="text-sm text-gray-500">総ポイント</div>
                <div class="text-2xl font-bold text-gray-900">
                    {{ $myTotalPoints ?? 0 }} pt
                </div>
            </div>

            <div class="bg-gray-50 rounded-xl p-4">
                <div class="text-sm text-gray-500">月間ポイント</div>
                <div class="text-2xl font-bold text-gray-900">
                    {{ $monthlyPoints ?? 0 }} pt
                </div>
            </div>

            <div class="bg-gray-50 rounded-xl p-4">
                <div class="text-sm text-gray-500">トレーニング回数</div>
                <div class="text-2xl font-bold text-gray-900">
                    {{ $trainingCount ?? 0 }} 回
                </div>
            </div>
        </div>

        <div class="mt-8 space-y-6">
            <section>
                <h2 class="text-lg font-bold text-gray-900 mb-2">自己紹介</h2>
                <div class="bg-gray-50 rounded-xl p-4 text-gray-700 whitespace-pre-line">
                    {{ optional($user->profile)->bio ?? '自己紹介はまだ登録されていません。' }}
                </div>
            </section>

            <section>
                <h2 class="text-lg font-bold text-gray-900 mb-2">スキル</h2>
                <div class="bg-gray-50 rounded-xl p-4 text-gray-700 whitespace-pre-line">
                    {{ optional($user->profile)->skills ?? 'スキルはまだ登録されていません。' }}
                </div>
            </section>

            <section>
                <h2 class="text-lg font-bold text-gray-900 mb-2">利用目的</h2>
                <div class="bg-gray-50 rounded-xl p-4 text-gray-700 whitespace-pre-line">
                    {{ optional($user->profile)->purpose ?? '利用目的はまだ登録されていません。' }}
                </div>
            </section>

            <section>
                <h2 class="text-lg font-bold text-gray-900 mb-2">作業スタイル</h2>
                <div class="bg-gray-50 rounded-xl p-4 text-gray-700 whitespace-pre-line">
                    {{ optional($user->profile)->work_style ?? '作業スタイルはまだ登録されていません。' }}
                </div>
            </section>
        </div>
    </div>
</div>
@endsection
