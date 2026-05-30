@extends('layouts.admin')

@section('title', '記事カテゴリー管理')

@section('content')
<div class="py-8">
    <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
        <div class="mb-6 flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <p class="text-sm font-bold text-indigo-600">
                    ARTICLE CATEGORIES
                </p>

                <h1 class="mt-2 text-3xl font-black text-slate-900">
                    記事カテゴリー管理
                </h1>

                <p class="mt-2 text-sm text-slate-600">
                    記事の大分類を管理します。カテゴリーは最大3階層まで作成できます。
                </p>
            </div>

            <a
                href="{{ route('admin.article-categories.create') }}"
                class="inline-flex items-center justify-center rounded-xl bg-indigo-600 px-5 py-3 text-sm font-bold text-white transition hover:bg-indigo-700"
            >
                カテゴリー作成
            </a>
        </div>

        <div class="overflow-hidden rounded-2xl bg-white shadow-sm ring-1 ring-slate-200">
            <table class="min-w-full divide-y divide-slate-200">
                <thead class="bg-slate-50">
                    <tr>
                        <th class="px-5 py-3 text-left text-xs font-black uppercase tracking-wider text-slate-500">ID</th>
                        <th class="px-5 py-3 text-left text-xs font-black uppercase tracking-wider text-slate-500">カテゴリー</th>
                        <th class="px-5 py-3 text-left text-xs font-black uppercase tracking-wider text-slate-500">スラッグ</th>
                        <th class="px-5 py-3 text-left text-xs font-black uppercase tracking-wider text-slate-500">状態</th>
                        <th class="px-5 py-3 text-left text-xs font-black uppercase tracking-wider text-slate-500">並び順</th>
                        <th class="px-5 py-3 text-right text-xs font-black uppercase tracking-wider text-slate-500">操作</th>
                    </tr>
                </thead>

                <tbody class="divide-y divide-slate-100 bg-white">
                    @forelse ($categories as $category)
                        <tr>
                            <td class="px-5 py-4 text-sm font-bold text-slate-500">
                                {{ $category->id }}
                            </td>

                            <td class="px-5 py-4">
                                <div class="font-bold text-slate-900">
                                    {{ $category->displayName() }}
                                </div>

                                @if ($category->description)
                                    <div class="mt-1 text-xs text-slate-500">
                                        {{ \Illuminate\Support\Str::limit($category->description, 80) }}
                                    </div>
                                @endif
                            </td>

                            <td class="px-5 py-4 text-sm text-slate-600">
                                {{ $category->slug }}
                            </td>

                            <td class="px-5 py-4">
                                @if ($category->is_active)
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
                                {{ $category->sort_order }}
                            </td>

                            <td class="px-5 py-4">
                                <div class="flex justify-end gap-2">
                                    <a
                                        href="{{ route('admin.article-categories.edit', $category) }}"
                                        class="rounded-lg border border-slate-300 px-3 py-2 text-xs font-bold text-slate-700 hover:bg-slate-50"
                                    >
                                        編集
                                    </a>

                                    <form method="POST" action="{{ route('admin.article-categories.destroy', $category) }}" onsubmit="return confirm('削除してもよろしいですか？');">
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
                                カテゴリーはありません。
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
