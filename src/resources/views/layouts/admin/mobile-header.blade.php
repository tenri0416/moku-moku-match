<header class="sticky top-0 z-50 border-b border-slate-200 bg-white/90 backdrop-blur lg:hidden">
  <div class="flex items-center justify-between px-4 py-3">
      <a href="{{ route('admin.dashboard') }}" class="flex items-center gap-3">
          <span class="flex h-10 w-10 items-center justify-center rounded-2xl bg-indigo-600 text-sm font-black text-white shadow-sm">
              A
          </span>

          <span>
              <span class="block text-base font-black text-slate-900">
                  Admin
              </span>
              <span class="block text-xs font-semibold text-slate-500">
                  管理画面
              </span>
          </span>
      </a>

      @include('layouts.admin.mobile-menu')
  </div>
</header>
