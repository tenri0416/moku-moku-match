<nav class="flex-1 space-y-1 px-4 py-5">
    @php
        $isArticleMenuActive = request()->routeIs(
            'admin.articles.*',
            'admin.article-categories.*',
            'admin.article-tags.*',
            'admin.article-views.*'
        );
    @endphp

    <a
        href="{{ route('admin.dashboard') }}"
        class="flex items-center justify-between rounded-xl px-4 py-3 text-sm font-bold transition
        {{ request()->routeIs('admin.dashboard') ? 'bg-indigo-50 text-indigo-700' : 'text-slate-700 hover:bg-slate-100' }}"
    >
        <span>ダッシュボード</span>
        <span class="text-slate-400">›</span>
    </a>

    <a
        href="{{ route('admin.users.index') }}"
        class="flex items-center justify-between rounded-xl px-4 py-3 text-sm font-bold transition
        {{ request()->routeIs('admin.users.*') ? 'bg-indigo-50 text-indigo-700' : 'text-slate-700 hover:bg-slate-100' }}"
    >
        <span>ユーザー管理</span>
        <span class="text-slate-400">›</span>
    </a>

    <a
        href="{{ route('admin.work-posts.index') }}"
        class="flex items-center justify-between rounded-xl px-4 py-3 text-sm font-bold transition
        {{ request()->routeIs('admin.work-posts.*') ? 'bg-indigo-50 text-indigo-700' : 'text-slate-700 hover:bg-slate-100' }}"
    >
        <span>募集管理</span>
        <span class="text-slate-400">›</span>
    </a>

    <a
        href="{{ route('admin.reports.index') }}"
        class="flex items-center justify-between rounded-xl px-4 py-3 text-sm font-bold transition
        {{ request()->routeIs('admin.reports.*') ? 'bg-rose-50 text-rose-700' : 'text-slate-700 hover:bg-slate-100' }}"
    >
        <span>通報管理</span>
        <span class="text-slate-400">›</span>
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
                class="flex items-center justify-between rounded-xl px-4 py-2.5 text-sm font-bold transition
                {{ request()->routeIs('admin.articles.*') ? 'bg-indigo-100 text-indigo-700' : 'text-slate-600 hover:bg-slate-100' }}"
            >
                <span>記事管理</span>
                <span class="text-slate-400">›</span>
            </a>

            <a
                href="{{ route('admin.article-categories.index') }}"
                class="flex items-center justify-between rounded-xl px-4 py-2.5 text-sm font-bold transition
                {{ request()->routeIs('admin.article-categories.*') ? 'bg-indigo-100 text-indigo-700' : 'text-slate-600 hover:bg-slate-100' }}"
            >
                <span>記事カテゴリー</span>
                <span class="text-slate-400">›</span>
            </a>

            <a
                href="{{ route('admin.article-tags.index') }}"
                class="flex items-center justify-between rounded-xl px-4 py-2.5 text-sm font-bold transition
                {{ request()->routeIs('admin.article-tags.*') ? 'bg-indigo-100 text-indigo-700' : 'text-slate-600 hover:bg-slate-100' }}"
            >
                <span>記事タグ</span>
                <span class="text-slate-400">›</span>
            </a>

            <a
                href="{{ route('admin.article-views.index') }}"
                class="flex items-center justify-between rounded-xl px-4 py-2.5 text-sm font-bold transition
                {{ request()->routeIs('admin.article-views.*') ? 'bg-indigo-100 text-indigo-700' : 'text-slate-600 hover:bg-slate-100' }}"
            >
                <span>記事閲覧数</span>
                <span class="text-slate-400">›</span>
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
        class="flex items-center justify-between rounded-xl px-4 py-3 text-sm font-bold transition
        {{ request()->routeIs('admin.database.*') ? 'bg-indigo-50 text-indigo-700' : 'text-slate-700 hover:bg-slate-100' }}"
    >
        <span>DBテーブルを見る</span>
        <span class="text-slate-400">›</span>
    </a>

    <a
        href="{{ route('admin.logs.index') }}"
        class="flex items-center justify-between rounded-xl px-4 py-3 text-sm font-bold transition
        {{ request()->routeIs('admin.logs.*') ? 'bg-indigo-50 text-indigo-700' : 'text-slate-700 hover:bg-slate-100' }}"
    >
        <span>Laravelログ</span>
        <span class="text-slate-400">›</span>
    </a>
</nav>
