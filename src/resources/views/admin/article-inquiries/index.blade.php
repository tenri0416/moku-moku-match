@extends('layouts.admin')

@section('title', 'YomuWorksお問い合わせ')

@section('content')
<div class="min-h-screen bg-slate-50">
    <div class="mx-auto max-w-7xl px-4 py-10 sm:px-6 lg:px-8">
        <div class="mb-8 flex flex-col gap-4 sm:flex-row sm:items-end sm:justify-between">
            <div>
                <p class="text-sm font-bold text-indigo-600">
                    ARTICLE INQUIRIES
                </p>

                <h1 class="mt-2 text-3xl font-bold text-slate-900">
                    YomuWorksお問い合わせ
                </h1>

                <p class="mt-3 text-slate-600">
                    記事サイトから送信されたお問い合わせを確認できます。
                </p>
            </div>

            <form method="GET" action="{{ route('admin.article-inquiries.index') }}">
                <select name="status" onchange="this.form.submit()" class="rounded-xl border-slate-300">
                    <option value="">すべて</option>
                    <option value="1" @selected((string) $status === '1')>未対応</option>
                    <option value="2" @selected((string) $status === '2')>返信済み</option>
                </select>
            </form>
        </div>

        @if (session('success'))
            <div class="mb-5 rounded-xl bg-emerald-50 px-4 py-3 text-sm font-bold text-emerald-700">
                {{ session('success') }}
            </div>
        @endif

        <div class="overflow-hidden rounded-2xl bg-white shadow-sm ring-1 ring-slate-200">
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-slate-200">
                    <thead class="bg-slate-50">
                        <tr>
                            <th class="px-5 py-3 text-left text-xs font-bold text-slate-500">ID</th>
                            <th class="px-5 py-3 text-left text-xs font-bold text-slate-500">メール</th>
                            <th class="px-5 py-3 text-left text-xs font-bold text-slate-500">内容</th>
                            <th class="px-5 py-3 text-left text-xs font-bold text-slate-500">状態</th>
                            <th class="px-5 py-3 text-left text-xs font-bold text-slate-500">送信日</th>
                            <th class="px-5 py-3 text-right text-xs font-bold text-slate-500">操作</th>
                        </tr>
                    </thead>

                    <tbody class="divide-y divide-slate-100">
                        @forelse ($inquiries as $inquiry)
                            <tr>
                                <td class="px-5 py-4 text-sm text-slate-700">
                                    {{ $inquiry->id }}
                                </td>

                                <td class="px-5 py-4 text-sm text-slate-700">
                                    {{ $inquiry->email }}
                                </td>

                                <td class="px-5 py-4 text-sm text-slate-700">
                                    {{ \Illuminate\Support\Str::limit($inquiry->body, 70) }}
                                </td>

                                <td class="px-5 py-4 text-sm">
                                    @if ($inquiry->isReplied())
                                        <span class="rounded-full bg-emerald-50 px-3 py-1 text-xs font-bold text-emerald-700">
                                            返信済み
                                        </span>
                                    @else
                                        <span class="rounded-full bg-amber-50 px-3 py-1 text-xs font-bold text-amber-700">
                                            未対応
                                        </span>
                                    @endif
                                </td>

                                <td class="px-5 py-4 text-sm text-slate-700">
                                    {{ $inquiry->created_at->format('Y/m/d H:i') }}
                                </td>

                                <td class="px-5 py-4 text-right">
                                    <a
                                        href="{{ route('admin.article-inquiries.show', $inquiry) }}"
                                        class="rounded-lg bg-indigo-600 px-3 py-2 text-xs font-bold text-white hover:bg-indigo-700"
                                    >
                                        詳細
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="px-5 py-10 text-center text-sm text-slate-500">
                                    お問い合わせはありません。
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        @if ($inquiries->hasPages())
            <div class="mt-8">
                {{ $inquiries->links() }}
            </div>
        @endif
    </div>
</div>
@endsection
