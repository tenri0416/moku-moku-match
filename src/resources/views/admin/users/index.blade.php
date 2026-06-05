@extends('layouts.admin')

@section('title', 'ユーザー一覧')

@section('content')
<div class="min-h-screen bg-slate-50">
    <div class="mx-auto w-full max-w-7xl px-3 py-6 sm:px-6 sm:py-10 lg:px-8">
        {{-- Header --}}
        <div class="mb-6 sm:mb-8">
            <p class="text-xs font-bold tracking-wide text-indigo-600 sm:text-sm">ADMIN USERS</p>

            <h1 class="mt-2 break-words text-2xl font-bold leading-tight text-slate-900 sm:text-3xl">
                ユーザー一覧
            </h1>

            <p class="mt-2 text-sm leading-6 text-slate-600 sm:text-base">
                登録ユーザーのプロフィール、権限、利用状態を確認できます。
            </p>
        </div>

        {{-- Summary --}}
        <div class="mb-6 grid grid-cols-1 gap-3 sm:mb-8 sm:grid-cols-3 sm:gap-4">
            <div class="rounded-2xl bg-white p-4 shadow-sm ring-1 ring-slate-200 sm:p-5">
                <p class="text-xs font-bold text-slate-500 sm:text-sm">総ユーザー数</p>
                <p class="mt-2 text-2xl font-black text-slate-900 sm:text-3xl">
                    {{ method_exists($users, 'total') ? $users->total() : $users->count() }}
                </p>
            </div>

            <div class="rounded-2xl bg-white p-4 shadow-sm ring-1 ring-slate-200 sm:p-5">
                <p class="text-xs font-bold text-slate-500 sm:text-sm">有効ユーザー</p>
                <p class="mt-2 text-2xl font-black text-emerald-600 sm:text-3xl">
                    {{ $users->where('status', 1)->count() }}
                </p>
            </div>

            <div class="rounded-2xl bg-white p-4 shadow-sm ring-1 ring-slate-200 sm:p-5">
                <p class="text-xs font-bold text-slate-500 sm:text-sm">停止ユーザー</p>
                <p class="mt-2 text-2xl font-black text-rose-600 sm:text-3xl">
                    {{ $users->where('status', 2)->count() }}
                </p>
            </div>
        </div>

        {{-- Mobile Cards --}}
        <div class="space-y-3 md:hidden">
            @forelse ($users as $user)
                <article class="rounded-2xl bg-white p-4 shadow-sm ring-1 ring-slate-200">
                    <div class="flex items-start justify-between gap-3">
                        <div class="min-w-0">
                            <p class="text-xs font-bold text-slate-500">#{{ $user->id }}</p>
                            <h2 class="mt-1 break-words text-base font-bold leading-6 text-slate-900">
                                {{ $user->profile->display_name ?? $user->name }}
                            </h2>
                            <p class="mt-1 break-all text-xs leading-5 text-slate-500">
                                {{ $user->email }}
                            </p>
                        </div>

                        <div class="flex shrink-0 flex-col items-end gap-2">
                            @if ($user->role === 2)
                                <span class="rounded-full bg-indigo-50 px-2.5 py-1 text-[11px] font-bold text-indigo-700">
                                    管理者
                                </span>
                            @else
                                <span class="rounded-full bg-slate-100 px-2.5 py-1 text-[11px] font-bold text-slate-600">
                                    一般
                                </span>
                            @endif

                            @if ($user->status === 1)
                                <span class="rounded-full bg-emerald-50 px-2.5 py-1 text-[11px] font-bold text-emerald-700">
                                    有効
                                </span>
                            @elseif ($user->status === 2)
                                <span class="rounded-full bg-rose-50 px-2.5 py-1 text-[11px] font-bold text-rose-700">
                                    停止中
                                </span>
                            @else
                                <span class="rounded-full bg-slate-100 px-2.5 py-1 text-[11px] font-bold text-slate-600">
                                    不明
                                </span>
                            @endif
                        </div>
                    </div>

                    <div class="mt-4 grid grid-cols-1 gap-3 rounded-xl bg-slate-50 p-3 text-xs leading-5 text-slate-600">
                        <div>
                            <span class="font-bold text-slate-500">職種：</span>
                            <span class="break-words">{{ $user->profile->job_type ?? '職種未設定' }}</span>
                        </div>
                        <div>
                            <span class="font-bold text-slate-500">登録日時：</span>
                            <span>{{ $user->created_at->format('Y/m/d H:i') }}</span>
                        </div>
                    </div>

                    <a
                        href="{{ route('admin.users.show', $user) }}"
                        class="mt-4 flex w-full items-center justify-center rounded-xl bg-indigo-600 px-4 py-3 text-sm font-bold text-white transition hover:bg-indigo-700"
                    >
                        詳細を見る
                    </a>
                </article>
            @empty
                <div class="rounded-2xl bg-white px-4 py-10 text-center shadow-sm ring-1 ring-slate-200">
                    <div class="mx-auto flex h-14 w-14 items-center justify-center rounded-full bg-slate-100 text-2xl">
                        👤
                    </div>

                    <h2 class="mt-4 text-lg font-bold text-slate-900">
                        ユーザーはいません
                    </h2>

                    <p class="mt-2 text-sm text-slate-600">
                        現在、登録ユーザーは存在しません。
                    </p>
                </div>
            @endforelse
        </div>

        {{-- Desktop Table --}}
        <div class="hidden overflow-hidden rounded-2xl bg-white shadow-sm ring-1 ring-slate-200 md:block">
            <div class="overflow-x-auto">
                <table class="min-w-[980px] divide-y divide-slate-200">
                    <thead class="bg-slate-50">
                        <tr>
                            <th class="px-5 py-4 text-left text-xs font-bold uppercase tracking-wide text-slate-500">
                                ID
                            </th>
                            <th class="px-5 py-4 text-left text-xs font-bold uppercase tracking-wide text-slate-500">
                                ユーザー
                            </th>
                            <th class="px-5 py-4 text-left text-xs font-bold uppercase tracking-wide text-slate-500">
                                メールアドレス
                            </th>
                            <th class="px-5 py-4 text-left text-xs font-bold uppercase tracking-wide text-slate-500">
                                権限
                            </th>
                            <th class="px-5 py-4 text-left text-xs font-bold uppercase tracking-wide text-slate-500">
                                ステータス
                            </th>
                            <th class="px-5 py-4 text-left text-xs font-bold uppercase tracking-wide text-slate-500">
                                登録日時
                            </th>
                            <th class="px-5 py-4 text-right text-xs font-bold uppercase tracking-wide text-slate-500">
                                操作
                            </th>
                        </tr>
                    </thead>

                    <tbody class="divide-y divide-slate-200 bg-white">
                        @forelse ($users as $user)
                            <tr class="hover:bg-slate-50">
                                <td class="whitespace-nowrap px-5 py-4 text-sm font-semibold text-slate-900">
                                    #{{ $user->id }}
                                </td>

                                <td class="px-5 py-4">
                                    <div class="min-w-0">
                                        <p class="break-words text-sm font-bold text-slate-900">
                                            {{ $user->profile->display_name ?? $user->name }}
                                        </p>

                                        <p class="mt-1 break-words text-xs text-slate-500">
                                            {{ $user->profile->job_type ?? '職種未設定' }}
                                        </p>
                                    </div>
                                </td>

                                <td class="px-5 py-4 text-sm text-slate-700">
                                    <span class="break-all">{{ $user->email }}</span>
                                </td>

                                <td class="whitespace-nowrap px-5 py-4">
                                    @if ($user->role === 2)
                                        <span class="rounded-full bg-indigo-50 px-3 py-1 text-xs font-bold text-indigo-700">
                                            管理者
                                        </span>
                                    @else
                                        <span class="rounded-full bg-slate-100 px-3 py-1 text-xs font-bold text-slate-600">
                                            一般
                                        </span>
                                    @endif
                                </td>

                                <td class="whitespace-nowrap px-5 py-4">
                                    @if ($user->status === 1)
                                        <span class="rounded-full bg-emerald-50 px-3 py-1 text-xs font-bold text-emerald-700">
                                            有効
                                        </span>
                                    @elseif ($user->status === 2)
                                        <span class="rounded-full bg-rose-50 px-3 py-1 text-xs font-bold text-rose-700">
                                            停止中
                                        </span>
                                    @else
                                        <span class="rounded-full bg-slate-100 px-3 py-1 text-xs font-bold text-slate-600">
                                            不明
                                        </span>
                                    @endif
                                </td>

                                <td class="whitespace-nowrap px-5 py-4 text-sm text-slate-500">
                                    {{ $user->created_at->format('Y/m/d H:i') }}
                                </td>

                                <td class="whitespace-nowrap px-5 py-4 text-right">
                                    <a
                                        href="{{ route('admin.users.show', $user) }}"
                                        class="inline-flex items-center justify-center rounded-xl bg-indigo-600 px-4 py-2 text-sm font-bold text-white transition hover:bg-indigo-700"
                                    >
                                        詳細
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="px-5 py-12 text-center">
                                    <div class="mx-auto flex h-14 w-14 items-center justify-center rounded-full bg-slate-100 text-2xl">
                                        👤
                                    </div>

                                    <h2 class="mt-4 text-lg font-bold text-slate-900">
                                        ユーザーはいません
                                    </h2>

                                    <p class="mt-2 text-sm text-slate-600">
                                        現在、登録ユーザーは存在しません。
                                    </p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        @if (method_exists($users, 'links'))
            <div class="mt-6 overflow-x-auto sm:mt-8">
                {{ $users->links() }}
            </div>
        @endif
    </div>
</div>
@endsection
