<div class="border-t border-slate-200 p-4">
  <a
      href="{{ route('home') }}"
      class="flex w-full items-center justify-center rounded-xl border border-slate-300 bg-white px-4 py-3 text-sm font-bold text-slate-700 transition hover:bg-slate-50"
  >
      サイトへ戻る
  </a>

  <form method="POST" action="{{ route('admin.logout') }}" class="mt-3">
      @csrf

      <button
          type="submit"
          class="flex w-full items-center justify-center rounded-xl bg-slate-900 px-4 py-3 text-sm font-bold text-white transition hover:bg-slate-800"
      >
          ログアウト
      </button>
  </form>
</div>
