{{-- PCランキングパネル --}}
<section class="overflow-hidden rounded-[18px] border border-[#DDE6F5] bg-white shadow-[0_8px_22px_rgba(15,43,95,0.06)]">
  {{-- TOP3 --}}
  @if ($topThree->isNotEmpty())
      <div class="relative min-h-[260px] border-b border-[#E8EDF6] bg-white px-8 pt-8">
          <div class="grid h-full grid-cols-3 items-end gap-6">
              @foreach ([1, 0, 2] as $displayIndex)
                  @php
                      $ranking = $topThree->get($displayIndex);
                      $rank = $displayIndex + 1;
                  @endphp

                  @if ($ranking)
                      <a href="{{ route('users.show', $ranking->user) }}"
                          class="flex flex-col items-center justify-end text-center">
                          <div class="relative">
                              <div class="absolute -top-12 left-1/2 -translate-x-1/2 text-[54px] leading-none">
                                  {{ $rankCrown($rank) ?: $rank }}
                              </div>

                              <div class="{{ $rank === 1 ? 'h-[92px] w-[92px]' : 'h-[78px] w-[78px]' }} overflow-hidden rounded-full bg-white ring-4 {{ $avatarRingClass($rank) }}">
                                  <img
                                      src="{{ $avatarUrl($ranking) }}"
                                      alt="{{ $displayName($ranking) }}"
                                      class="h-full w-full object-cover"
                                  >
                              </div>
                          </div>

                          <p class="mt-4 text-[18px] font-black text-[#071433]">
                              {{ $displayName($ranking) }}
                          </p>

                          <p class="mt-1 text-[24px] font-black text-[#0D4FE8]">
                              {{ number_format($ranking->total_points) }}pt
                          </p>

                          <div class="{{ $rank === 1 ? 'h-[72px] bg-yellow-100' : ($rank === 2 ? 'h-[48px] bg-slate-100' : 'h-[42px] bg-orange-100') }} mt-4 w-full rounded-t-[8px]"></div>
                      </a>
                  @else
                      <div></div>
                  @endif
              @endforeach
          </div>
      </div>
  @endif

  {{-- 4位以降 --}}
  <div>
      <div class="grid grid-cols-[80px_1fr_130px_90px_160px] border-b border-[#E8EDF6] bg-[#FBFCFF] px-5 py-3 text-[14px] font-black text-[#46516B]">
          <div>順位</div>
          <div>ユーザー</div>
          <div>ポイント</div>
          <div>回数</div>
          <div>プロフィール</div>
      </div>

      @forelse ($rankings->slice(3, 7) as $index => $ranking)
          @php
              $rank = $index + 4;
              $me = $isMe($ranking);
          @endphp

          <a href="{{ route('users.show', $ranking->user) }}"
              class="grid grid-cols-[80px_1fr_130px_90px_160px] items-center border-b border-[#E8EDF6] px-5 py-3 last:border-b-0 {{ $me ? 'border border-[#0D4FE8] bg-blue-50' : 'bg-white' }}">
              <div class="text-[16px] font-black text-[#071433]">
                  {{ $rank }}位
              </div>

              <div class="flex min-w-0 items-center gap-3">
                  <div class="h-9 w-9 shrink-0 overflow-hidden rounded-full bg-blue-50">
                      <img
                          src="{{ $avatarUrl($ranking) }}"
                          alt="{{ $displayName($ranking) }}"
                          class="h-full w-full object-cover"
                      >
                  </div>

                  <span class="truncate text-[16px] font-black text-[#071433]">
                      {{ $displayName($ranking) }}
                  </span>
              </div>

              <div class="text-[16px] font-black text-[#071433]">
                  {{ number_format($ranking->total_points) }}pt
              </div>

              <div class="text-[15px] font-bold text-[#46516B]">
                  {{ $ranking->training_count }}回
              </div>

              <div class="text-[15px] font-black text-[#0D4FE8]">
                  プロフィールを見る
                  <span class="text-[20px]">›</span>
              </div>
          </a>
      @empty
          <div class="px-5 py-8 text-center text-[15px] font-bold text-[#64748B]">
              まだランキングデータがありません。
          </div>
      @endforelse

      @if ($myMonthlyRanking)
          <a href="{{ route('users.show', $myMonthlyRanking->user) }}"
              class="grid grid-cols-[80px_1fr_130px_90px_160px] items-center border-2 border-[#0D4FE8] bg-blue-50 px-5 py-3">
              <div class="text-[17px] font-black text-[#0D4FE8]">
                  あなた {{ $myMonthlyRank }}位
              </div>

              <div class="flex min-w-0 items-center gap-3">
                  <div class="h-10 w-10 shrink-0 overflow-hidden rounded-full bg-blue-50">
                      <img
                          src="{{ $avatarUrl($myMonthlyRanking) }}"
                          alt="あなた"
                          class="h-full w-full object-cover"
                      >
                  </div>

                  <span class="truncate text-[17px] font-black text-[#0D4FE8]">
                      {{ $displayName($myMonthlyRanking) }}（あなた）
                  </span>
              </div>

              <div class="text-[17px] font-black text-[#0D4FE8]">
                  {{ number_format($myMonthlyRanking->total_points) }}pt
              </div>

              <div class="text-[15px] font-bold text-[#0D4FE8]">
                  {{ $myMonthlyRanking->training_count }}回
              </div>

              <div class="text-[15px] font-black text-[#0D4FE8]">
                  プロフィールを見る
                  <span class="text-[20px]">›</span>
              </div>
          </a>
      @endif
  </div>
</section>
