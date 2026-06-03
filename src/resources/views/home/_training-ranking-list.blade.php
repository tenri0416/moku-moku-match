@php
    $rankingUsers = $rankingUsers ?? collect();
    $limit = $limit ?? 5;
    $avatarSizeClass = $avatarSizeClass ?? 'h-[42px] w-[42px]';
    $nameTextClass = $nameTextClass ?? 'text-[14px]';
    $pointTextClass = $pointTextClass ?? 'text-[15px]';
@endphp

<div class="space-y-4">
    @forelse ($rankingUsers->take($limit) as $index => $ranking)
        @php
            $rankUser = $ranking->user ?? null;
            $rank = $index + 1;
        @endphp

        @if ($rankUser)
            <a href="{{ route('users.show', $rankUser) }}"
                class="grid grid-cols-[34px_42px_1fr_76px] items-center gap-3">
                <div class="text-center">
                    @if ($rank === 1)
                        <span class="text-[26px]">🥇</span>
                    @elseif ($rank === 2)
                        <span class="text-[26px]">🥈</span>
                    @elseif ($rank === 3)
                        <span class="text-[26px]">🥉</span>
                    @else
                        <span class="text-[15px] font-black text-[#071433]">
                            {{ $rank }}
                        </span>
                    @endif
                </div>

                <img
                    src="{{ $avatarUrl($rankUser) }}"
                    alt="{{ $displayName($rankUser) }}のプロフィール画像"
                    class="{{ $avatarSizeClass }} rounded-full border border-[#DDE6F5] bg-blue-50 object-cover"
                >

                <div class="min-w-0">
                    <p class="truncate {{ $nameTextClass }} font-black text-[#071433]">
                        {{ $displayName($rankUser) }}
                    </p>

                    @if (!empty($showJobType))
                        <p class="mt-0.5 truncate text-[12px] font-bold text-[#64748B]">
                            {{ $jobType($rankUser) }}
                        </p>
                    @endif
                </div>

                <p class="text-right {{ $pointTextClass }} font-black text-[#071433]">
                    {{ number_format($ranking->total_points) }}pt
                </p>
            </a>
        @endif
    @empty
        <p class="text-center text-[13px] font-bold text-[#64748B]">
            まだランキングデータがありません。
        </p>
    @endforelse
</div>
