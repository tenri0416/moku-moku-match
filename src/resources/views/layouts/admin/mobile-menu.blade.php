<details class="relative">
    @php
        $isArticleMenuActive = request()->routeIs(
            'admin.articles.*',
            'admin.article-categories.*',
            'admin.article-tags.*',
            'admin.article-views.*',
            'admin.article-inquiries.*'

        );
    @endphp

    <summary
        class="cursor-pointer list-none rounded-xl border border-slate-200 bg-white px-4 py-2 text-sm font-bold text-slate-700 shadow-sm transition hover:bg-slate-50"
    >
        メニュー
    </summary>

    <div class="absolute right-0 z-50 mt-3 max-h-[80vh] w-72 overflow-y-auto rounded-2xl border border-slate-200 bg-white shadow-xl">
        <div class="border-b border-slate-100 px-4 py-3">
            <p class="text-sm font-bold text-slate-900">
                管理メニュー
            </p>
            <p class="mt-1 text-xs text-slate-500">
                MokuMoku Match
            </p>
        </div>

        <div class="p-2">
            <a
                href="{{ route('admin.dashboard') }}"
                class="block rounded-xl px-4 py-3 text-sm font-bold
                {{ request()->routeIs('admin.dashboard') ? 'bg-indigo-50 text-indigo-700' : 'text-slate-700 hover:bg-slate-100' }}"
            >
                ダッシュボード
            </a>

            <a
                href="{{ route('admin.users.index') }}"
                class="block rounded-xl px-4 py-3 text-sm font-bold
                {{ request()->routeIs('admin.users.*') ? 'bg-indigo-50 text-indigo-700' : 'text-slate-700 hover:bg-slate-100' }}"
            >
                ユーザー管理
            </a>

            <a
                href="{{ route('admin.work-posts.index') }}"
                class="block rounded-xl px-4 py-3 text-sm font-bold
                {{ request()->routeIs('admin.work-posts.*') ? 'bg-indigo-50 text-indigo-700' : 'text-slate-700 hover:bg-slate-100' }}"
            >
                募集管理
            </a>

            <a
                href="{{ route('admin.reports.index') }}"
                class="block rounded-xl px-4 py-3 text-sm font-bold
                {{ request()->routeIs('admin.reports.*') ? 'bg-rose-50 text-rose-700' : 'text-slate-700 hover:bg-slate-100' }}"
            >
                通報管理
            </a>

            {{-- 記事関連 --}}
            <details class="group" {{ $isArticleMenuActive ? 'open' : '' }}>
                <summary
                    class="flex cursor-pointer list-none items-center justify-between rounded-xl px-4 py-3 text-sm font-bold transition
                    {{ $isArticleMenuActive ? 'bg-indigo-50 text-indigo-700' : 'text-slate-700 hover:bg-slate-100' }}"
                >
                    <span>記事関連</span>
                    <span class="text-slate-400 transition group-open:rotate-90">›</span>
                </summary>

                <div class="mt-1 space-y-1 pl-3">
                    <a
                        href="{{ route('admin.articles.index') }}"
                        class="block rounded-xl px-4 py-2.5 text-sm font-bold
                        {{ request()->routeIs('admin.articles.*') ? 'bg-indigo-100 text-indigo-700' : 'text-slate-600 hover:bg-slate-100' }}"
                    >
                        記事管理
                    </a>

                    <a
                        href="{{ route('admin.article-categories.index') }}"
                        class="block rounded-xl px-4 py-2.5 text-sm font-bold
                        {{ request()->routeIs('admin.article-categories.*') ? 'bg-indigo-100 text-indigo-700' : 'text-slate-600 hover:bg-slate-100' }}"
                    >
                        記事カテゴリー
                    </a>

                    <a
                        href="{{ route('admin.article-tags.index') }}"
                        class="block rounded-xl px-4 py-2.5 text-sm font-bold
                        {{ request()->routeIs('admin.article-tags.*') ? 'bg-indigo-100 text-indigo-700' : 'text-slate-600 hover:bg-slate-100' }}"
                    >
                        記事タグ
                    </a>

                    <a
                        href="{{ route('admin.article-views.index') }}"
                        class="block rounded-xl px-4 py-2.5 text-sm font-bold
                        {{ request()->routeIs('admin.article-views.*') ? 'bg-indigo-100 text-indigo-700' : 'text-slate-600 hover:bg-slate-100' }}"
                    >
                        記事閲覧数
                    </a>
                    <a
                        href="{{ route('admin.article-inquiries.index') }}"
                        class="block rounded-xl px-4 py-2.5 text-sm font-bold
                        {{ request()->routeIs('admin.article-inquiries.*') ? 'bg-indigo-100 text-indigo-700' : 'text-slate-600 hover:bg-slate-100' }}"
                    >
                        記事閲覧数
                    </a>
                </div>
            </details>

            <a
                href="{{ route('admin.ai-usage.index') }}"
                class="flex items-center justify-between rounded-xl px-4 py-3 text-sm font-bold transition
                {{ request()->routeIs('admin.ai-usage.*') ? 'bg-indigo-50 text-indigo-700' : 'text-slate-700 hover:bg-slate-100' }}"
            >
                <span>AI利用状況</span>
                <span class="text-slate-400">›</span>
            </a>

            <a
                href="{{ route('admin.satisfaction-surveys.index') }}"
                class="flex items-center justify-between rounded-xl px-4 py-3 text-sm font-bold transition
                {{ request()->routeIs('admin.satisfaction-surveys.*') ? 'bg-indigo-50 text-indigo-700' : 'text-slate-700 hover:bg-slate-100' }}"
            >
                <span>満足度調査</span>
                <span class="text-slate-400">›</span>
            </a>

            <a
                href="{{ route('admin.database.index') }}"
                class="block rounded-xl px-4 py-3 text-sm font-bold
                {{ request()->routeIs('admin.database.*') ? 'bg-indigo-50 text-indigo-700' : 'text-slate-700 hover:bg-slate-100' }}"
            >
                DBテーブルを見る
            </a>

            <a
                href="{{ route('admin.logs.index') }}"
                class="block rounded-xl px-4 py-3 text-sm font-bold
                {{ request()->routeIs('admin.logs.*') ? 'bg-indigo-50 text-indigo-700' : 'text-slate-700 hover:bg-slate-100' }}"
            >
                Laravelログ
            </a>

            <div class="my-2 border-t border-slate-100"></div>

            <a
                href="{{ route('home') }}"
                class="block rounded-xl px-4 py-3 text-sm font-bold text-slate-700 hover:bg-slate-100"
            >
                サイトへ戻る
            </a>

            <form method="POST" action="{{ route('admin.logout') }}">
                @csrf

                <button
                    type="submit"
                    class="block w-full rounded-xl px-4 py-3 text-left text-sm font-bold text-slate-700 hover:bg-slate-100"
                >
                    ログアウト
                </button>
            </form>
        </div>
    </div>
</details>
