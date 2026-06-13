<div class="hidden md:block min-h-screen bg-[#F8FAFF] px-8 py-8 text-[#071433]">
    <div class="mx-auto max-w-[1200px]">

        @if (session('success'))
            <div
                class="mb-5 rounded-2xl border border-emerald-200 bg-emerald-50 px-5 py-4 text-sm font-bold text-emerald-700">
                {{ session('success') }}
            </div>
        @endif

        @if (session('error'))
            <div class="mb-5 rounded-2xl border border-red-200 bg-red-50 px-5 py-4 text-sm font-bold text-red-700">
                {{ session('error') }}
            </div>
        @endif

        <section class="mb-6 rounded-3xl bg-gradient-to-br from-[#1D66F3] to-[#0648D8] p-8 text-white shadow-lg">
            <div class="flex items-center justify-between gap-6">
                <div>
                    <p class="text-sm font-black text-blue-50">語彙を忘れないための復習</p>
                    <h1 class="mt-2 text-4xl font-black">ボキャブラリートレーニング</h1>
                    <p class="mt-4 text-base font-bold text-blue-50">
                        読書で調べた言葉を登録し、あとからAI採点で復習できます。
                    </p>
                </div>

                <div class="grid w-[420px] grid-cols-4 gap-3 text-center">
                    <div class="rounded-2xl bg-white/15 p-4">
                        <p class="text-xs font-bold">登録</p>
                        <p class="mt-1 text-3xl font-black">{{ $totalWords }}</p>
                    </div>
                    <div class="rounded-2xl bg-white/15 p-4">
                        <p class="text-xs font-bold">復習対象</p>
                        <p class="mt-1 text-3xl font-black">{{ $reviewTargetCount }}</p>
                    </div>
                    <div class="rounded-2xl bg-white/15 p-4">
                        <p class="text-xs font-bold">苦手</p>
                        <p class="mt-1 text-3xl font-black">{{ $weakCount }}</p>
                    </div>
                    <div class="rounded-2xl bg-white/15 p-4">
                        <p class="text-xs font-bold">定着</p>
                        <p class="mt-1 text-3xl font-black">{{ $masteredCount }}</p>
                    </div>
                </div>
            </div>
        </section>

        <div class="mb-6 flex items-center justify-between gap-4">
            <form method="GET" action="{{ route('trainings.vocabulary.index') }}" class="flex flex-1 gap-3">
                <input type="text" name="keyword" value="{{ request('keyword') }}"
                    class="h-[52px] flex-1 rounded-2xl border border-[#CBD7EA] px-4 text-base font-bold outline-none focus:border-[#0D4FE8]"
                    placeholder="言葉・意味・例文を検索">

                <button class="h-[52px] rounded-2xl bg-[#0D4FE8] px-8 text-base font-black text-white">
                    検索
                </button>
            </form>

            <a href="{{ route('trainings.vocabulary.create') }}"
                class="flex h-[52px] items-center justify-center rounded-2xl bg-[#0D4FE8] px-8 text-base font-black text-white">
                ＋ 言葉を登録
            </a>

            <a href="{{ route('trainings.vocabulary.review') }}"
                class="flex h-[52px] items-center justify-center rounded-2xl border-2 border-[#0D4FE8] bg-white px-8 text-base font-black text-[#0D4FE8]">
                復習する
            </a>
            <a href="{{ route('trainings.vocabulary.print.index') }}"
                class="flex h-[52px] items-center justify-center rounded-2xl border-2 border-emerald-500 bg-emerald-50 px-8 text-base font-black text-emerald-700">
                印刷テストを作る
            </a>
        </div>

        <section class="overflow-hidden rounded-3xl border border-[#DDE6F5] bg-white shadow-sm">
            <table class="w-full">
                <thead class="bg-[#F8FAFF]">
                    <tr>
                        <th class="px-5 py-4 text-left text-sm font-black text-[#46516B]">言葉</th>
                        <th class="px-5 py-4 text-left text-sm font-black text-[#46516B]">意味</th>
                        <th class="px-5 py-4 text-left text-sm font-black text-[#46516B]">状態</th>
                        <th class="px-5 py-4 text-right text-sm font-black text-[#46516B]">操作</th>
                    </tr>
                </thead>

                <tbody>
                    @forelse ($words as $word)
                        <tr class="border-t border-[#E8EDF6]">
                            <td class="px-5 py-4 align-top">
                                <p class="text-lg font-black text-[#071433]">{{ $word->word }}</p>
                                <p class="mt-1 text-xs font-bold text-[#64748B]">
                                    重要度{{ $word->importance }} ・ 復習{{ $word->review_count }}回 ・
                                    正答率{{ $word->correctRate() }}%
                                </p>
                            </td>

                            <td class="px-5 py-4 align-top">
                                <p class="line-clamp-2 text-sm font-bold leading-7 text-[#334155]">
                                    {{ $word->meaning }}
                                </p>
                                <p
                                    class="mt-2 line-clamp-1 rounded-xl bg-[#F8FAFF] px-3 py-2 text-xs font-bold text-[#64748B]">
                                    {{ $word->example_sentence }}
                                </p>
                            </td>

                            <td class="px-5 py-4 align-top">
                                <span class="rounded-full bg-[#F0F7FF] px-3 py-1 text-xs font-black text-[#0D4FE8]">
                                    {{ $word->statusLabel() }}
                                </span>
                            </td>

                            <td class="px-5 py-4 align-top">
                                <div class="flex justify-end gap-2">
                                    <a href="{{ route('trainings.vocabulary.review', ['word_id' => $word->id]) }}"
                                        class="rounded-xl bg-[#0D4FE8] px-4 py-2 text-sm font-black text-white">
                                        復習
                                    </a>

                                    <a href="{{ route('trainings.vocabulary.edit', $word) }}"
                                        class="rounded-xl border border-[#CBD7EA] bg-white px-4 py-2 text-sm font-black text-[#0D4FE8]">
                                        編集
                                    </a>

                                    <form method="POST" action="{{ route('trainings.vocabulary.destroy', $word) }}">
                                        @csrf
                                        @method('DELETE')
                                        <button
                                            class="rounded-xl border border-red-200 bg-red-50 px-4 py-2 text-sm font-black text-red-600">
                                            削除
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="px-5 py-10 text-center text-sm font-bold text-[#64748B]">
                                まだ登録された言葉がありません。
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </section>

        <div class="mt-5">
            {{ $words->links() }}
        </div>
    </div>
</div>
