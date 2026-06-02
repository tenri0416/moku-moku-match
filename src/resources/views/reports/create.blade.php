@extends('layouts.app')

@section('title', '通報する')

@section('content')
<div class="min-h-screen bg-slate-50">
    <div class="mx-auto max-w-3xl px-4 py-10 sm:px-6 lg:px-8">
        {{-- Header --}}
        <div class="mb-8">
            <p class="text-sm font-bold text-rose-600">
                REPORT
            </p>

            <h1 class="mt-2 text-3xl font-bold text-slate-900">
                通報する
            </h1>

            <p class="mt-2 text-slate-600">
                不適切な募集やユーザーを管理者へ通報できます。
            </p>
        </div>

        {{-- Error --}}
        @if ($errors->any())
            <div class="mb-6 rounded-xl border border-rose-200 bg-rose-50 p-4">
                <p class="font-bold text-rose-700">
                    入力内容を確認してください。
                </p>

                <ul class="mt-2 list-disc space-y-1 pl-5 text-sm text-rose-700">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        {{-- Report Target --}}
        <section class="mb-6 rounded-2xl bg-white p-6 shadow-sm ring-1 ring-slate-200">
            <h2 class="text-lg font-bold text-slate-900">
                通報対象
            </h2>

            <div class="mt-4 space-y-3 text-sm text-slate-700">
                <p>
                    <span class="font-bold text-slate-500">対象ユーザー：</span>
                    {{ $reportedUser->profile->display_name ?? $reportedUser->name }}
                </p>

                <p>
                    <span class="font-bold text-slate-500">対象募集：</span>
                    {{ $workPost->title }}
                </p>
            </div>
        </section>

        {{-- Form --}}
        <section class="rounded-2xl bg-white p-6 shadow-sm ring-1 ring-slate-200">
            <form method="POST" action="{{ route('reports.store') }}" class="space-y-6">
                @csrf

                {{-- GETで受け取った値をPOSTでも送る --}}
                <input type="hidden" name="reported_user_id" value="{{ old('reported_user_id', $reportedUser->id) }}">
                <input type="hidden" name="work_post_id" value="{{ old('work_post_id', $workPost->id) }}">

                <div>
                    <label for="reason" class="mb-2 block text-sm font-bold text-slate-700">
                        通報理由 <span class="text-rose-500">*</span>
                    </label>

                    <select
                        id="reason"
                        name="reason"
                        required
                        class="block w-full rounded-xl border-slate-300 shadow-sm focus:border-rose-500 focus:ring-rose-500"
                    >
                        <option value="">選択してください</option>
                        <option value="spam" @selected(old('reason') === 'spam')>
                            スパム・宣伝目的
                        </option>
                        <option value="harassment" @selected(old('reason') === 'harassment')>
                            迷惑行為・嫌がらせ
                        </option>
                        <option value="inappropriate" @selected(old('reason') === 'inappropriate')>
                            不適切な内容
                        </option>
                        <option value="false_information" @selected(old('reason') === 'false_information')>
                            虚偽・誤解を招く内容
                        </option>
                        <option value="other" @selected(old('reason') === 'other')>
                            その他
                        </option>
                    </select>
                </div>

                <div>
                    <label for="body" class="mb-2 block text-sm font-bold text-slate-700">
                        詳細 <span class="text-rose-500">*</span>
                    </label>

                    <textarea
                        id="body"
                        name="body"
                        rows="7"
                        required
                        maxlength="2000"
                        placeholder="通報理由の詳細を入力してください"
                        class="block w-full rounded-xl border-slate-300 shadow-sm focus:border-rose-500 focus:ring-rose-500"
                    >{{ old('body') }}</textarea>

                    <p class="mt-2 text-xs text-slate-500">
                        できるだけ具体的に記載してください。最大2000文字まで入力できます。
                    </p>
                </div>

                <div class="flex flex-col-reverse gap-3 sm:flex-row sm:justify-end">
                    <a
                        href="{{ route('work-posts.show', $workPost) }}"
                        class="inline-flex items-center justify-center rounded-xl border border-slate-300 bg-white px-5 py-3 text-sm font-bold text-slate-700 transition hover:bg-slate-50"
                    >
                        戻る
                    </a>

                    <button
                        type="submit"
                        class="inline-flex items-center justify-center rounded-xl bg-rose-600 px-5 py-3 text-sm font-bold text-white shadow-sm transition hover:bg-rose-700"
                    >
                        通報を送信する
                    </button>
                </div>
            </form>
        </section>
    </div>
</div>
@endsection
