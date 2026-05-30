<div class="flex items-center gap-3">
  <a
      href="{{ route('articles.index') }}"
      class="hidden rounded-full border border-[#D8CCB8] px-5 py-3 text-sm font-black text-[#0B1548] transition hover:border-[#C9825D] hover:text-[#C9825D] sm:inline-flex"
  >
      記事一覧
  </a>

  <a
      href="{{ route('home') }}"
      class="rounded-full bg-[#0B1548] px-5 py-3 text-sm font-black text-white shadow-[0_12px_30px_rgba(11,21,72,0.18)] transition hover:-translate-y-0.5 hover:bg-[#17215A]"
  >
      サービスへ
  </a>

  <div class="hidden h-12 w-12 items-center justify-center rounded-full bg-[#2E343B] text-white md:flex">
      <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.2">
          <path stroke-linecap="round" stroke-linejoin="round" d="m21 21-4.35-4.35m1.1-5.4a6.5 6.5 0 1 1-13 0 6.5 6.5 0 0 1 13 0Z" />
      </svg>
  </div>
</div>
