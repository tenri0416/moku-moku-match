<header class="hidden border-b border-slate-200 bg-white lg:block">
  <div class="mx-auto flex max-w-7xl items-center justify-between px-8 py-4">
      <div>
          <p class="text-sm font-bold text-slate-500">
              管理画面
          </p>
          <p class="mt-1 text-lg font-black text-slate-900">
              @yield('title', '管理画面')
          </p>
      </div>

      <div class="flex items-center gap-3">
          <a
              href="{{ route('home') }}"
              class="rounded-xl border border-slate-300 bg-white px-4 py-2 text-sm font-bold text-slate-700 transition hover:bg-slate-50"
          >
              サイトへ戻る
          </a>

          <form method="POST" action="{{ route('admin.logout') }}">
              @csrf

              <button
                  type="submit"
                  class="rounded-xl bg-slate-900 px-4 py-2 text-sm font-bold text-white transition hover:bg-slate-800"
              >
                  ログアウト
              </button>
          </form>
      </div>
  </div>
</header>
