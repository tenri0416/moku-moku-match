@extends('layouts.admin')

@section('title', '募集管理')

@section('content')
<div class="min-h-screen bg-slate-50">
    <div class="mx-auto max-w-7xl px-4 py-10 sm:px-6 lg:px-8">
        <div class="mb-8 flex flex-col gap-4 sm:flex-row sm:items-end sm:justify-between">
            <div>
                <p class="text-sm font-bold text-indigo-600">ADMIN WORK POSTS</p>

                <h1 class="mt-2 text-3xl font-bold text-slate-900">
                    募集管理
                </h1>

                <p class="mt-2 text-slate-600">
                    投稿された募集の内容、投稿者、公開状態を確認できます。
                </p>
            </div>

            @if (Route::has('admin.work-posts.create'))
                <a
                    href="{{ route('admin.work-posts.create') }}"
                    class="inline-flex items-center justify-center rounded-xl bg-indigo-600 px-5 py-3 text-sm font-bold text-white shadow-sm transition hover:bg-indigo-700"
                >
                    募集作成
                </a>
            @endif
        </div>

        <div class="mb-8 grid gap-4 md:grid-cols-4">
            <div class="rounded-2xl bg-white p-5 shadow-sm ring-1 ring-slate-200">
                <p class="text-sm font-bold text-slate-500">総募集数</p>
                <p class="mt-2 text-3xl font-black text-slate-900">
                    {{ method_exists($workPosts, 'total') ? $workPosts->total() : $workPosts->count() }}
                </p>
            </div>

            <div class="rounded-2xl bg-white p-5 shadow-sm ring-1 ring-slate-200">
                <p class="text-sm font-bold text-slate-500">募集中</p>
                <p class="mt-2 text-3xl font-black text-emerald-600">
                    {{ $workPosts->where('status', 1)->count() }}
                </p>
            </div>

            <div class="rounded-2xl bg-white p-5 shadow-sm ring-1 ring-slate-200">
                <p class="text-sm font-bold text-slate-500">終了</p>
                <p class="mt-2 text-3xl font-black text-slate-600">
                    {{ $workPosts->where('status', 2)->count() }}
                </p>
            </div>

            <div class="rounded-2xl bg-white p-5 shadow-sm ring-1 ring-slate-200">
                <p class="text-sm font-bold text-slate-500">非公開</p>
                <p class="mt-2 text-3xl font-black text-rose-600">
                    {{ $workPosts->where('status', 3)->count() }}
                </p>
            </div>
        </div>

        <div class="overflow-hidden rounded-2xl bg-white shadow-sm ring-1 ring-slate-200">
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-slate-200">
                    <thead class="bg-slate-50">
                        <tr>
                            <th class="px-5 py-4 text-left text-xs font-bold uppercase tracking-wide text-slate-500">ID</th>
                            <th class="px-5 py-4 text-left text-xs font-bold uppercase tracking-wide text-slate-500">タイトル</th>
                            <th class="px-5 py-4 text-left text-xs font-bold uppercase tracking-wide text-slate-500">投稿者</th>
                            <th class="px-5 py-4 text-left text-xs font-bold uppercase tracking-wide text-slate-500">目的</th>
                            <th class="px-5 py-4 text-left text-xs font-bold uppercase tracking-wide text-slate-500">開催形式</th>
                            <th class="px-5 py-4 text-left text-xs font-bold uppercase tracking-wide text-slate-500">状態</th>
                            <th class="px-5 py-4 text-left text-xs font-bold uppercase tracking-wide text-slate-500">投稿日時</th>
                            <th class="px-5 py-4 text-right text-xs font-bold uppercase tracking-wide text-slate-500">操作</th>
                        </tr>
                    </thead>

                    <tbody class="divide-y divide-slate-200 bg-white">
                        @forelse ($workPosts as $workPost)
                            <tr class="hover:bg-slate-50">
                                <td class="whitespace-nowrap px-5 py-4 text-sm font-semibold text-slate-900">
                                    #{{ $workPost->id }}
                                </td>

                                <td class="px-5 py-4">
                                    <p class="font-bold text-slate-900">
                                        {{ $workPost->title }}
                                    </p>

                                    <p class="mt-1 text-xs text-slate-500">
                                        {{ $workPost->start_at ? $workPost->start_at->format('Y/m/d H:i') : '開始日時未定' }}
                                    </p>
                                </td>

                                <td class="whitespace-nowrap px-5 py-4 text-sm text-slate-700">
                                    {{ $workPost->user->profile->display_name ?? $workPost->user->name }}
                                </td>

                                <td class="whitespace-nowrap px-5 py-4 text-sm text-slate-700">
                                    {{ $workPost->purpose }}
                                </td>

                                <td class="whitespace-nowrap px-5 py-4">
                                    @if ($workPost->location_type === 'online')
                                        <span class="rounded-full bg-sky-50 px-3 py-1 text-xs font-bold text-sky-700">オンライン</span>
                                    @elseif ($workPost->location_type === 'offline')
                                        <span class="rounded-full bg-amber-50 px-3 py-1 text-xs font-bold text-amber-700">オフライン</span>
                                    @elseif ($workPost->location_type === 'both')
                                        <span class="rounded-full bg-purple-50 px-3 py-1 text-xs font-bold text-purple-700">どちらでも可</span>
                                    @else
                                        <span class="rounded-full bg-slate-100 px-3 py-1 text-xs font-bold text-slate-600">未設定</span>
                                    @endif
                                </td>

                                <td class="whitespace-nowrap px-5 py-4">
                                    @if ($workPost->status === 1)
                                        <span class="rounded-full bg-emerald-50 px-3 py-1 text-xs font-bold text-emerald-700">募集中</span>
                                    @elseif ($workPost->status === 2)
                                        <span class="rounded-full bg-slate-100 px-3 py-1 text-xs font-bold text-slate-600">終了</span>
                                    @else
                                        <span class="rounded-full bg-rose-50 px-3 py-1 text-xs font-bold text-rose-700">非公開</span>
                                    @endif
                                </td>

                                <td class="whitespace-nowrap px-5 py-4 text-sm text-slate-500">
                                    {{ $workPost->created_at->format('Y/m/d H:i') }}
                                </td>

                                <td class="whitespace-nowrap px-5 py-4 text-right">
                                    <a
                                        href="{{ route('admin.work-posts.show', $workPost) }}"
                                        class="inline-flex items-center justify-center rounded-xl bg-indigo-600 px-4 py-2 text-sm font-bold text-white transition hover:bg-indigo-700"
                                    >
                                        詳細
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="px-5 py-12 text-center">
                                    <div class="mx-auto flex h-14 w-14 items-center justify-center rounded-full bg-slate-100 text-2xl">
                                        📝
                                    </div>

                                    <h2 class="mt-4 text-lg font-bold text-slate-900">
                                        募集はありません
                                    </h2>

                                    <p class="mt-2 text-sm text-slate-600">
                                        現在、登録されている募集はありません。
                                    </p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        @if (method_exists($workPosts, 'links'))
            <div class="mt-8">
                {{ $workPosts->links() }}
            </div>
        @endif
    </div>
</div>
@endsection
