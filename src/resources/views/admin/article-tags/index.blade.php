@extends('layouts.admin')

@section('title', '記事タグ管理')

@section('content')
<div class="py-8">
    <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
        <div class="mb-6 flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <p class="text-sm font-bold text-indigo-600">
                    ARTICLE TAGS
                </p>

                <h1 class="mt-2 text-3xl font-black text-slate-900">
                    記事タグ管理
                </h1>

                <p class="mt-2 text-sm text-slate-600">
                    記事に複数設定できるタグを管理します。
                </p>
            </div>

            <a
                href="{{ route('admin.article-tags.create') }}"
                class="inline-flex items-center justify-center rounded-xl bg-indigo-600 px-5 py-3 text-sm font-bold text-white transition hover:bg-indigo-700"
            >
                タグ作成
            </a>
        </div>

        <div class="overflow-hidden rounded-2xl bg-white shadow-sm ring-1 ring-slate-200">
            <table class="min-w-full divide-y divide-slate-200">
                <thead class="bg-slate-50">
                    <tr>
                        <th class="px-5 py-3 text-left text-xs font-black uppercase tracking-wider text-slate-500">ID</th>
                        <th class="px-5 py-3 text-left text-xs font-black uppercase tracking-wider text-slate-500">タグ</th>
                        <th class="px-5 py-3 text-left text-xs font-black uppercase tracking-wider text-slate-500">スラッグ</th>
                        <th class="px-5 py-3 text-left text-xs font-black uppercase tracking-wider text-slate-500">状態</th>
                        <th class="px-5 py-3 text-left text-xs font-black uppercase tracking-wider text-slate-500">並び順</th>
                        <th class="px-5 py-3 text-right text-xs font-black uppercase tracking-wider text-slate-500">操作</th>
                    </tr>
                </thead>

                <tbody class="divide-y divide-slate-100 bg-white">
                    @forelse ($tags as $tag)
                        <tr>
                            <td class="px-5 py-4 text-sm font-bold text-slate-500">
                                {{ $tag->id }}
                            </td>

                            <td class="px-5 py-4">
                                <div class="font-bold text-slate-900">
                                    #{{ $tag->name }}
                                </div>

                                @if ($tag->description)
                                    <div class="mt-1 text-xs text-slate-500">
                                        {{ \Illuminate\Support\Str::limit($tag->description, 80) }}
                                    </div>
                                @endif
                            </td>

                            <td class="px-5 py-4 text-sm text-slate-600">
                                {{ $tag->slug }}
                            </td>

                            <td class="px-5 py-4">
                                @if ($tag->is_active)
                                    <span class="rounded-full bg-emerald-50 px-3 py-1 text-xs font-bold text-emerald-700">
                                        有効
                                    </span>
                                @else
                                    <span class="rounded-full bg-slate-100 px-3 py-1 text-xs font-bold text-slate-600">
                                        無効
                                    </span>
                                @endif
                            </td>

                            <td class="px-5 py-4 text-sm text-slate-600">
                                {{ $tag->sort_order }}
                            </td>

                            <td class="px-5 py-4">
                                <div class="flex justify-end gap-2">
                                    <a
                                        href="{{ route('admin.article-tags.edit', $tag) }}"
                                        class="rounded-lg border border-slate-300 px-3 py-2 text-xs font-bold text-slate-700 hover:bg-slate-50"
                                    >
                                        編集
                                    </a>

                                    <form method="POST" action="{{ route('admin.article-tags.destroy', $tag) }}" onsubmit="return confirm('削除してもよろしいですか？');">
                                        @csrf
                                        @method('DELETE')

                                        <button
                                            type="submit"
                                            class="rounded-lg bg-rose-600 px-3 py-2 text-xs font-bold text-white hover:bg-rose-700"
                                        >
                                            削除
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-5 py-10 text-center text-sm text-slate-500">
                                タグはありません。
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
