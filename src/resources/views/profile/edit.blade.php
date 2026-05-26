@extends('layouts.app')

@section('title', 'プロフィール編集')

@section('content')
<div class="min-h-screen bg-slate-50">
    <div class="mx-auto max-w-4xl px-4 py-10 sm:px-6 lg:px-8">
        {{-- Header --}}
        <div class="mb-8">
            <p class="text-sm font-bold text-indigo-600">PROFILE</p>

            <h1 class="mt-2 text-3xl font-bold text-slate-900">
                プロフィール編集
            </h1>

            <p class="mt-2 text-slate-600">
                募集の作成や参加申請を行うには、プロフィール登録が必要です。
            </p>
        </div>

        {{-- Notice --}}
        <div class="mb-6 rounded-2xl border border-indigo-100 bg-indigo-50 p-5">
            <p class="text-sm leading-7 text-indigo-900">
                表示名は他のユーザーに公開されます。
                本名ではなく、ニックネームや活動名で登録することをおすすめします。
            </p>
        </div>

        {{-- Form --}}
        <div class="rounded-2xl bg-white p-6 shadow-sm ring-1 ring-slate-200 sm:p-8">
            <form method="POST" action="{{ route('profile.update') }}" class="space-y-6">
                @csrf
                @method('PATCH')

                {{-- 表示名 --}}
                <div>
                    <label for="display_name" class="mb-2 block text-sm font-bold text-slate-700">
                        表示名 <span class="text-rose-500">必須</span>
                    </label>

                    <input
                        type="text"
                        id="display_name"
                        name="display_name"
                        value="{{ old('display_name', $profile->display_name ?? '') }}"
                        placeholder="例：Laravelエンジニア"
                        class="block w-full rounded-xl border-slate-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                    >

                    @error('display_name')
                        <p class="mt-2 text-sm font-semibold text-rose-600">
                            {{ $message }}
                        </p>
                    @enderror
                </div>

                <div class="grid gap-6 md:grid-cols-2">
                    {{-- 職種 --}}
                    <div>
                        <label for="job_type" class="mb-2 block text-sm font-bold text-slate-700">
                            職種
                        </label>

                        <input
                            type="text"
                            id="job_type"
                            name="job_type"
                            value="{{ old('job_type', $profile->job_type ?? '') }}"
                            placeholder="例：バックエンドエンジニア"
                            class="block w-full rounded-xl border-slate-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                        >

                        @error('job_type')
                            <p class="mt-2 text-sm font-semibold text-rose-600">
                                {{ $message }}
                            </p>
                        @enderror
                    </div>

                    {{-- 都道府県 --}}
                    <div>
                        <label for="prefecture" class="mb-2 block text-sm font-bold text-slate-700">
                            都道府県
                        </label>

                        <input
                            type="text"
                            id="prefecture"
                            name="prefecture"
                            value="{{ old('prefecture', $profile->prefecture ?? '') }}"
                            placeholder="例：奈良県"
                            class="block w-full rounded-xl border-slate-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                        >

                        @error('prefecture')
                            <p class="mt-2 text-sm font-semibold text-rose-600">
                                {{ $message }}
                            </p>
                        @enderror
                    </div>
                </div>

                {{-- スキル --}}
                <div>
                    <label for="skills" class="mb-2 block text-sm font-bold text-slate-700">
                        スキル
                    </label>

                    <textarea
                        id="skills"
                        name="skills"
                        rows="4"
                        placeholder="例：Laravel, React, AWS, MySQL"
                        class="block w-full rounded-xl border-slate-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                    >{{ old('skills', $profile->skills ?? '') }}</textarea>

                    <p class="mt-2 text-xs text-slate-500">
                        自分ができること、勉強中の技術、話せる分野などを入力してください。
                    </p>

                    @error('skills')
                        <p class="mt-2 text-sm font-semibold text-rose-600">
                            {{ $message }}
                        </p>
                    @enderror
                </div>

                {{-- 自己紹介 --}}
                <div>
                    <label for="bio" class="mb-2 block text-sm font-bold text-slate-700">
                        自己紹介
                    </label>

                    <textarea
                        id="bio"
                        name="bio"
                        rows="6"
                        placeholder="例：フルリモートでLaravelの開発をしています。平日の午前中に一緒に黙々作業できる方を探しています。"
                        class="block w-full rounded-xl border-slate-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                    >{{ old('bio', $profile->bio ?? '') }}</textarea>

                    @error('bio')
                        <p class="mt-2 text-sm font-semibold text-rose-600">
                            {{ $message }}
                        </p>
                    @enderror
                </div>

                <div class="grid gap-6 md:grid-cols-2">
 {{-- 利用目的 --}}
<div>
    <label for="purpose" class="mb-2 block text-sm font-bold text-slate-700">
        利用目的
    </label>

    <textarea
        id="purpose"
        name="purpose"
        rows="4"
        placeholder="例：黙々作業、勉強、情報交換、フリーランス仲間探しなど"
        class="block w-full rounded-xl border-slate-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
    >{{ old('purpose', $profile->purpose ?? '') }}</textarea>

    @error('purpose')
        <p class="mt-2 text-sm font-semibold text-rose-600">
            {{ $message }}
        </p>
    @enderror
</div>

{{-- 希望作業スタイル --}}
<div>
    <label for="work_style" class="mb-2 block text-sm font-bold text-slate-700">
        希望作業スタイル
    </label>

    <textarea
        id="work_style"
        name="work_style"
        rows="4"
        placeholder="例：最初と最後だけ会話して、作業中は集中したい。1〜2時間くらい一緒に作業したい。"
        class="block w-full rounded-xl border-slate-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
    >{{ old('work_style', $profile->work_style ?? '') }}</textarea>

    @error('work_style')
        <p class="mt-2 text-sm font-semibold text-rose-600">
            {{ $message }}
        </p>
    @enderror
</div>
                </div>

                {{-- Buttons --}}
                <div class="flex flex-wrap items-center gap-3 border-t border-slate-200 pt-6">
                    <button
                        type="submit"
                        class="inline-flex items-center justify-center rounded-xl bg-indigo-600 px-6 py-3 text-sm font-bold text-white shadow-sm transition hover:bg-indigo-700"
                    >
                        保存する
                    </button>

                    <a
                        href="{{ route('mypage') }}"
                        class="inline-flex items-center justify-center rounded-xl border border-slate-300 bg-white px-6 py-3 text-sm font-bold text-slate-700 transition hover:bg-slate-50"
                    >
                        マイページへ戻る
                    </a>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
