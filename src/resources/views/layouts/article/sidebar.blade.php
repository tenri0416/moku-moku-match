<aside class="hidden lg:block">
  <div class="sticky top-6 space-y-6">
      <section class="border-t-4 border-[#C9825D] bg-white p-5 shadow-sm">
          <h2 class="text-lg font-black text-[#111827]">
              MokuMoku Match
          </h2>

          <p class="mt-3 text-sm font-bold leading-7 text-[#5B6472]">
              一人で働く毎日に、ちょうどいいつながりを。
              作業仲間を探せるフルリモート向けマッチングサービスです。
          </p>

          <a
              href="{{ route('home') }}"
              class="mt-5 inline-flex w-full items-center justify-center bg-[#0B1548] px-4 py-3 text-sm font-black text-white transition hover:bg-[#17215A]"
          >
              サービスを見る
          </a>
      </section>

      <section class="bg-white p-5 shadow-sm">
          <div class="border-b-2 border-[#E8E0D2] pb-3">
              <h2 class="text-lg font-black text-[#111827]">
                  注目テーマ
              </h2>
          </div>

          <div class="mt-4 space-y-3">
              <a href="{{ route('articles.index') }}" class="flex items-center gap-3 border-b border-[#EFE7DA] pb-3 transition hover:text-[#C9825D]">
                  <span class="flex h-8 w-8 shrink-0 items-center justify-center bg-[#C9BA84] text-sm font-black text-white">
                      1
                  </span>
                  <span class="text-sm font-black leading-6">
                      フルリモートで集中する方法
                  </span>
              </a>

              <a href="{{ route('articles.index') }}" class="flex items-center gap-3 border-b border-[#EFE7DA] pb-3 transition hover:text-[#C9825D]">
                  <span class="flex h-8 w-8 shrink-0 items-center justify-center bg-[#C9BA84] text-sm font-black text-white">
                      2
                  </span>
                  <span class="text-sm font-black leading-6">
                      作業仲間の見つけ方
                  </span>
              </a>

              <a href="{{ route('articles.index') }}" class="flex items-center gap-3 transition hover:text-[#C9825D]">
                  <span class="flex h-8 w-8 shrink-0 items-center justify-center bg-[#C9BA84] text-sm font-black text-white">
                      3
                  </span>
                  <span class="text-sm font-black leading-6">
                      一人作業を続けるコツ
                  </span>
              </a>
          </div>
      </section>
  </div>
</aside>
