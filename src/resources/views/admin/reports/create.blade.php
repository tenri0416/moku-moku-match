@extends('layouts.app')

@section('title', '通報')

@section('content')
<div class="container">
    <h1>通報</h1>

    <p>
        不適切な募集やユーザーを運営に通報できます。
    </p>

    <form method="POST" action="{{ route('reports.store') }}">
        @csrf

        <input
            type="hidden"
            name="reported_user_id"
            value="{{ old('reported_user_id', request('reported_user_id')) }}"
        >

        <input
            type="hidden"
            name="work_post_id"
            value="{{ old('work_post_id', request('work_post_id')) }}"
        >

        @error('target')
            <p>{{ $message }}</p>
        @enderror

        @error('reported_user_id')
            <p>{{ $message }}</p>
        @enderror

        @error('work_post_id')
            <p>{{ $message }}</p>
        @enderror

        <div>
            <label for="reason">通報理由 <span>必須</span></label>
            <input
                type="text"
                id="reason"
                name="reason"
                value="{{ old('reason') }}"
                placeholder="例：不適切な内容が含まれている"
            >

            @error('reason')
                <p>{{ $message }}</p>
            @enderror
        </div>

        <div>
            <label for="body">詳細内容</label>
            <textarea
                id="body"
                name="body"
                rows="6"
                placeholder="具体的な内容を入力してください"
            >{{ old('body') }}</textarea>

            @error('body')
                <p>{{ $message }}</p>
            @enderror
        </div>

        <div>
            <button type="submit">通報する</button>
            <a href="{{ route('work-posts.index') }}">募集一覧へ戻る</a>
        </div>
    </form>
</div>
@endsection
@extends('layouts.admin')

@section('title', '通報作成')

@section('content')
<div class="min-h-screen bg-slate-50">
    <div class="mx-auto max-w-4xl px-4 py-10 sm:px-6 lg:px-8">
        {{-- Header --}}
        <div class="mb-8">
            <p class="text-sm font-bold text-rose-600">ADMIN REPORT</p>

            <h1 class="mt-2 text-3xl font-bold text-slate-900">
                通報作成
            </h1>

            <p class="mt-2 text-slate-600">
                管理者が手動で通報情報を登録する画面です。
            </p>
        </div>

        {{-- Notice --}}
        <div class="mb-6 rounded-2xl border border-rose-100 bg-rose-50 p-5">
            <div class="flex gap-3">
                <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-full bg-white text-xl shadow-sm">
                    ⚠️
                </div>

                <div>
                    <h2 class="font-bold text-rose-900">
                        管理者用の登録画面です
                    </h2>

                    <p class="mt-2 text-sm leading-7 text-rose-800">
                        通報者、通報対象ユーザー、対象募集、理由を確認したうえで登録してください。
                        通報対象ユーザーまたは対象募集のどちらかは指定してください。
                    </p>
                </div>
            </div>
        </div>

        {{-- Form --}}
        <div class="rounded-2xl bg-white p-6 shadow-sm ring-1 ring-slate-200 sm:p-8">
            <form method="POST" action="{{ route('admin.reports.store') }}" class="space-y-6">
                @csrf

                @error('target')
                    <div class="rounded-xl border border-rose-200 bg-rose-50 p-4">
                        <p class="text-sm font-semibold text-rose-700">
                            {{ $message }}
                        </p>
                    </div>
                @enderror

                <div class="grid gap-6 md:grid-cols-2">
                    {{-- 通報者ID --}}
                    <div>
                        <label for="reporter_id" class="mb-2 block text-sm font-bold text-slate-700">
                            通報者ID <span class="text-rose-500">必須</span>
                        </label>

                        <input
                            type="number"
                            id="reporter_id"
                            name="reporter_id"
                            value="{{ old('reporter_id') }}"
                            placeholder="例：1"
                            class="block w-full rounded-xl border-slate-300 shadow-sm focus:border-rose-500 focus:ring-rose-500"
                        >

                        @error('reporter_id')
                            <p class="mt-2 text-sm font-semibold text-rose-600">
                                {{ $message }}
                            </p>
                        @enderror
                    </div>

                    {{-- 通報対象ユーザーID --}}
                    <div>
                        <label for="reported_user_id" class="mb-2 block text-sm font-bold text-slate-700">
                            通報対象ユーザーID
                        </label>

                        <input
                            type="number"
                            id="reported_user_id"
                            name="reported_user_id"
                            value="{{ old('reported_user_id') }}"
                            placeholder="例：2"
                            class="block w-full rounded-xl border-slate-300 shadow-sm focus:border-rose-500 focus:ring-rose-500"
                        >

                        @error('reported_user_id')
                            <p class="mt-2 text-sm font-semibold text-rose-600">
                                {{ $message }}
                            </p>
                        @enderror
                    </div>

                    {{-- 対象募集ID --}}
                    <div>
                        <label for="work_post_id" class="mb-2 block text-sm font-bold text-slate-700">
                            対象募集ID
                        </label>

                        <input
                            type="number"
                            id="work_post_id"
                            name="work_post_id"
                            value="{{ old('work_post_id') }}"
                            placeholder="例：10"
                            class="block w-full rounded-xl border-slate-300 shadow-sm focus:border-rose-500 focus:ring-rose-500"
                        >

                        @error('work_post_id')
                            <p class="mt-2 text-sm font-semibold text-rose-600">
                                {{ $message }}
                            </p>
                        @enderror
                    </div>

                    {{-- ステータス --}}
                    <div>
                        <label for="status" class="mb-2 block text-sm font-bold text-slate-700">
                            ステータス
                        </label>

                        <select
                            id="status"
                            name="status"
                            class="block w-full rounded-xl border-slate-300 shadow-sm focus:border-rose-500 focus:ring-rose-500"
                        >
                            <option value="1" @selected(old('status') == 1)>未対応</option>
                            <option value="2" @selected(old('status') == 2)>対応中</option>
                            <option value="3" @selected(old('status') == 3)>対応済み</option>
                        </select>

                        @error('status')
                            <p class="mt-2 text-sm font-semibold text-rose-600">
                                {{ $message }}
                            </p>
                        @enderror
                    </div>
                </div>

                {{-- 通報理由 --}}
                <div>
                    <label for="reason" class="mb-2 block text-sm font-bold text-slate-700">
                        通報理由 <span class="text-rose-500">必須</span>
                    </label>

                    <input
                        type="text"
                        id="reason"
                        name="reason"
                        value="{{ old('reason') }}"
                        placeholder="例：不適切な募集内容が含まれている"
                        class="block w-full rounded-xl border-slate-300 shadow-sm focus:border-rose-500 focus:ring-rose-500"
                    >

                    @error('reason')
                        <p class="mt-2 text-sm font-semibold text-rose-600">
                            {{ $message }}
                        </p>
                    @enderror
                </div>

                {{-- 詳細内容 --}}
                <div>
                    <label for="body" class="mb-2 block text-sm font-bold text-slate-700">
                        詳細内容
                    </label>

                    <textarea
                        id="body"
                        name="body"
                        rows="7"
                        placeholder="具体的な通報内容や管理メモを入力してください。"
                        class="block w-full rounded-xl border-slate-300 shadow-sm focus:border-rose-500 focus:ring-rose-500"
                    >{{ old('body') }}</textarea>

                    @error('body')
                        <p class="mt-2 text-sm font-semibold text-rose-600">
                            {{ $message }}
                        </p>
                    @enderror
                </div>

                {{-- Buttons --}}
                <div class="flex flex-wrap items-center gap-3 border-t border-slate-200 pt-6">
                    <button
                        type="submit"
                        class="inline-flex items-center justify-center rounded-xl bg-rose-600 px-6 py-3 text-sm font-bold text-white shadow-sm transition hover:bg-rose-700"
                    >
                        登録する
                    </button>

                    <a
                        href="{{ route('admin.reports.index') }}"
                        class="inline-flex items-center justify-center rounded-xl border border-slate-300 bg-white px-6 py-3 text-sm font-bold text-slate-700 transition hover:bg-slate-50"
                    >
                        通報一覧へ戻る
                    </a>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
