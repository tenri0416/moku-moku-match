<nav class="flex-1 space-y-1 px-4 py-5">
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

  <a
      href="{{ route('admin.articles.index') }}"
      class="flex items-center justify-between rounded-xl px-4 py-3 text-sm font-bold transition
      {{ request()->routeIs('admin.articles.*') ? 'bg-indigo-50 text-indigo-700' : 'text-slate-700 hover:bg-slate-100' }}"
  >
      <span>記事管理</span>
      <span class="text-slate-400">›</span>
  </a>
  <a
    href="{{ route('admin.article-categories.index') }}"
    class="flex items-center justify-between rounded-xl px-4 py-3 text-sm font-bold transition
    {{ request()->routeIs('admin.article-categories.*') ? 'bg-indigo-50 text-indigo-700' : 'text-slate-700 hover:bg-slate-100' }}"
>
    <span>記事カテゴリー</span>
    <span class="text-slate-400">›</span>
</a>

<a
    href="{{ route('admin.article-tags.index') }}"
    class="flex items-center justify-between rounded-xl px-4 py-3 text-sm font-bold transition
    {{ request()->routeIs('admin.article-tags.*') ? 'bg-indigo-50 text-indigo-700' : 'text-slate-700 hover:bg-slate-100' }}"
>
    <span>記事タグ</span>
    <span class="text-slate-400">›</span>
</a>
<a
    href="{{ route('admin.article-views.index') }}"
    class="flex items-center justify-between rounded-xl px-4 py-3 text-sm font-bold transition
    {{ request()->routeIs('admin.article-views.*') ? 'bg-indigo-50 text-indigo-700' : 'text-slate-700 hover:bg-slate-100' }}"
>
    <span>記事閲覧数</span>
    <span class="text-slate-400">›</span>
</a>
</nav>
