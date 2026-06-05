<footer class="mt-8 border-t border-[#D8CCB8] bg-[#0B1548] text-white">
  <div class="mx-auto grid max-w-7xl gap-8 px-4 py-10 sm:px-6 lg:grid-cols-[1.4fr_1fr] lg:px-8">
      <div>
          <p class="text-2xl font-black tracking-[0.08em]">
              MokuMoku Match
          </p>

          <p class="mt-2 text-xs font-bold tracking-[0.24em] text-[#BFA46A]">
              REMOTE WORK MAGAZINE
          </p>

          <p class="mt-5 max-w-2xl text-sm font-bold leading-7 text-white/70">
              MokuMoku Matchは、フリーランスやリモートワーカーが一緒に黙々作業できる仲間を探せるマッチングサービスです。
              このメディアでは、働く場所・集中環境・作業仲間づくりに役立つ情報を発信しています。
          </p>
      </div>

      <div class="flex flex-col gap-3 text-sm font-black lg:items-end">
          <a href="{{ route('articles.index') }}" class="text-white/75 transition hover:text-white">
              記事一覧
          </a>

          <a href="{{ route('home') }}" class="text-white/75 transition hover:text-white">
              MokuMoku Matchへ
          </a>

          <a
              href="{{ route('home') }}"
              class="mt-3 inline-flex w-fit items-center justify-center bg-white px-5 py-3 text-[#0B1548] transition hover:bg-[#F7F3EA]"
          >
              サービスを見る
          </a>
      </div>
  </div>

  <div class="border-t border-white/10">
      <div class="mx-auto max-w-7xl px-4 py-4 text-xs text-white/50 sm:px-6 lg:px-8">
          <p>© {{ date('Y') }} MokuMoku Match</p>
      </div>
  </div>
</footer>
