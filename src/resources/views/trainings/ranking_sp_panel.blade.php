{{-- SPランキングパネル：resources/views/trainings/ranking_sp_panel.blade.php --}}
@if ($topThree->isNotEmpty())
    <div class="mb-4 overflow-hidden rounded-[18px] border border-[#DDE6F5] bg-white px-3 pb-0 pt-6 shadow-[0_8px_22px_rgba(15,43,95,0.06)]">
        <div class="grid grid-cols-3 items-end gap-2">
            @foreach ([1, 0, 2] as $displayIndex)
                @php
                    $ranking = $topThree->get($displayIndex);
                    $rank = $displayIndex + 1;
                @endphp

                @if ($ranking)
                    <a href="{{ route('users.show', $ranking->user) }}"
                        class="{{ $rank === 1 ? 'min-h-[220px] border-yellow-200 bg-yellow-50/70 pb-6 pt-4' : 'min-h-[190px] bg-white pb-5 pt-8' }} flex flex-col items-center justify-end rounded-t-[18px] border px-2 text-center">
                        <div class="relative">
                            <div class="absolute -top-10 left-1/2 -translate-x-1/2 text-[42px] leading-none">
                                {{ $rankCrown($rank) ?: $rank }}
                            </div>

                            <div class="{{ $rank === 1 ? 'h-[92px] w-[92px]' : 'h-[76px] w-[76px]' }} overflow-hidden rounded-full bg-white ring-4 {{ $avatarRingClass($rank) }}">
                                <img
                                    src="{{ $avatarUrl($ranking) }}"
                                    alt="{{ $displayName($ranking) }}"
                                    class="h-full w-full object-cover"
                                >
                            </div>
                        </div>

                        <p class="mt-4 max-w-full truncate text-[22px] font-black text-[#071433]">
                            {{ $displayName($ranking) }}
                        </p>

                        <p class="mt-2 text-[26px] font-black leading-none text-[#0D4FE8]">
                            {{ number_format($ranking->total_points) }}<span class="text-[16px]">pt</span>
                        </p>
                    </a>
                @else
                    <div></div>
                @endif
            @endforeach
        </div>
    </div>
@endif

<div class="overflow-hidden rounded-[18px] border border-[#DDE6F5] bg-white shadow-[0_8px_22px_rgba(15,43,95,0.06)]">
    @forelse ($rows as $row)
        @php
            $rank = $row['rank'];
            $ranking = $row['ranking'];
            $me = $isMe($ranking);
        @endphp

        <a href="{{ route('users.show', $ranking->user) }}"
            class="grid grid-cols-[46px_48px_minmax(0,1fr)_82px_22px] items-center gap-2 border-b border-[#E8EDF6] px-3 py-3 last:border-b-0 {{ $me ? 'border border-[#0D4FE8] bg-blue-50' : 'bg-white' }}">
            <div class="text-center text-[24px] font-black text-[#071433]">
                {{ $rank }}
            </div>

            <div class="h-11 w-11 overflow-hidden rounded-full bg-blue-50">
                <img
                    src="{{ $avatarUrl($ranking) }}"
                    alt="{{ $displayName($ranking) }}"
                    class="h-full w-full object-cover"
                >
            </div>

            <div class="min-w-0">
                <p class="truncate text-[21px] font-black text-[#071433]">
                    {{ $me ? 'あなた' : $displayName($ranking) }}
                </p>

                <p class="mt-1 truncate text-[14px] font-bold text-[#46516B]">
                    {{ $profileLabel($ranking) }}
                </p>
            </div>

            <p class="text-right text-[23px] font-black text-[#071433]">
                {{ number_format($ranking->total_points) }}<span class="text-[14px]">pt</span>
            </p>

            <span class="text-[26px] text-[#8793A8]">›</span>
        </a>
    @empty
        <div class="px-5 py-8 text-center">
            <p class="text-[15px] font-bold text-[#64748B]">
                まだランキングデータがありません。
            </p>
        </div>
    @endforelse
</div>
