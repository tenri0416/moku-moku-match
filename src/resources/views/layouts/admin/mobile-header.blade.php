<header class="sticky top-0 z-50 border-b border-slate-200 bg-white/90 backdrop-blur lg:hidden">
  <div class="flex items-center justify-between px-4 py-3">
      <a href="{{ route('admin.dashboard') }}" class="flex items-center gap-3">
        <div class="mx-auto flex h-11 w-11 shrink-0 items-center justify-center overflow-hidden rounded-2xl bg-white shadow-sm ring-1 ring-[#DDE6F5]">
            <img
                src="{{ asset('images/logo.png') }}"
                alt="MokuMoku Match"
                class="h-9 w-9 object-contain"
            >
        </div>

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
