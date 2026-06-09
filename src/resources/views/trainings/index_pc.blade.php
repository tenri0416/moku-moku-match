{{-- PC版：resources/views/trainings/index_pc.blade.php --}}
<div class="hidden md:block min-h-screen bg-[#F8FAFF] text-[#071433]">
    <div class="mx-auto w-full max-w-[1440px] px-8 py-8">

        @if (session('success'))
            <div class="mb-5 rounded-2xl border border-emerald-200 bg-emerald-50 px-5 py-4 text-[15px] font-bold text-emerald-700">
                {{ session('success') }}
            </div>
        @endif

        @if (session('error'))
            <div class="mb-5 rounded-2xl border border-red-200 bg-red-50 px-5 py-4 text-[15px] font-bold text-red-700">
                {{ session('error') }}
            </div>
        @endif

        <div class="grid grid-cols-[1fr_380px] gap-8">

            {{-- 左側 --}}
            <main>
                {{-- ヒーロー --}}
                <section class="mb-6">
                    <div class="grid grid-cols-[1fr_430px] items-center gap-6">
                        <div>
                            <div class="mb-4 flex items-center gap-4">
                                <span class="text-[64px] leading-none">🏆</span>

                                <h1 class="text-[50px] font-black leading-tight tracking-[0.01em] text-[#071433]">
                                    自己成長トレーニング
                                </h1>
                            </div>

                            <p class="text-[18px] font-bold leading-relaxed text-[#334155]">
                                今日も少しずつ、前に進もう
                            </p>

                            <p class="mt-4 text-[16px] font-bold leading-relaxed text-[#334155]">
                                6つのトレーニングで、考える力と表現する力をバランスよく育てていきましょう。
                            </p>
                        </div>

                        <div class="flex justify-center">
                            <img
                                src="{{ asset('images/training-top.png') }}"
                                alt="自己成長トレーニング"
                                class="h-[210px] w-full max-w-[430px] object-contain"
                                loading="eager"
                            >
                        </div>
                    </div>
                </section>

                {{-- 青いステータスカード --}}
                <section class="mb-7 overflow-hidden rounded-[18px] bg-gradient-to-br from-[#1D66F3] to-[#0648D8] px-6 py-6 text-white shadow-[0_14px_28px_rgba(13,79,232,0.28)]">
                    <div class="grid grid-cols-3 divide-x divide-white/25">
                        <div class="flex items-center gap-5 px-4">
                            <div class="text-[66px]">🏅</div>
                            <div class="min-w-0 flex-1">
                                <p class="text-[16px] font-bold text-blue-50">総ポイント</p>
                                <p class="mt-1 text-[38px] font-black leading-none">
                                    {{ $myTotalPoints }} <span class="text-[20px]">pt</span>
                                </p>

                                <div class="mt-4 h-3 w-full overflow-hidden rounded-full bg-white/25">
                                    <div
                                        class="h-full rounded-full bg-orange-300"
                                        style="width: {{ $trainingProgressPercent }}%;"
                                    ></div>
                                </div>

                                <p class="mt-3 text-[15px] font-bold text-blue-50">
                                    @if ($nextGoalRemainingPoints > 0)
                                        次の難易度まであと{{ $nextGoalRemainingPoints }}pt！
                                    @else
                                        難易度Maxに到達しています！
                                    @endif
                                </p>
                            </div>
                        </div>

                        <div class="flex items-center justify-center gap-5 px-4">
                            <div class="text-[58px]">📋</div>
                            <div>
                                <p class="text-[16px] font-bold text-blue-50">本日完了</p>
                                <p class="mt-1 text-[40px] font-black leading-none">
                                    {{ $completedTodayCount }} <span class="text-[24px]">/ 6</span>
                                </p>
                                <p class="mt-4 text-[15px] font-bold text-blue-50">
                                    @if ($completedTodayCount >= 6)
                                        本日のトレーニングはすべて完了です！
                                    @else
                                        あと{{ max(0, 6 - $completedTodayCount) }}つ実施できます
                                    @endif
                                </p>
                            </div>
                        </div>

                        <div class="flex items-center justify-center gap-5 px-4">
                            <div class="text-[58px]">🔥</div>
                            <div>
                                <p class="text-[16px] font-bold text-blue-50">履歴</p>
                                <p class="mt-1 text-[40px] font-black leading-none">
                                    {{ $historyCount }} <span class="text-[24px]">件</span>
                                </p>
                                <p class="mt-4 text-[15px] font-bold text-blue-50">
                                    @if ($historyCount > 0)
                                        これまでの実施履歴です
                                    @else
                                        まだ履歴はありません
                                    @endif
                                </p>
                            </div>
                        </div>
                    </div>
                </section>

                {{-- 今日できるトレーニング --}}
                <section id="pcTrainingCards" class="mb-6">
                    <div class="mb-4 flex items-center justify-between">
                        <h2 class="flex items-center gap-3 text-[26px] font-black text-[#071433]">
                            <span class="text-[36px] text-amber-500">☀</span>
                            今日できるトレーニング
                        </h2>

                    </div>

                    <div class="grid grid-cols-3 gap-4">
                        @foreach ($trainingCards as $card)
                            @php
                                $isDone = $todayStatuses[$card['key']] ?? false;
                            @endphp

                            <a href="{{ $isDone ? 'javascript:void(0)' : $card['route'] }}"
                                @if (!$isDone && $card['loading'])
                                    data-ai-loading-link="true"
                                    data-ai-loading-type="question"
                                @endif
                                class="rounded-[16px] border border-[#DDE6F5] bg-white px-5 py-5 shadow-[0_8px_22px_rgba(15,43,95,0.06)] transition hover:-translate-y-0.5 hover:shadow-[0_12px_28px_rgba(15,43,95,0.10)] {{ $isDone ? 'opacity-75' : '' }}">
                                <div class="flex items-start gap-4">
                                    <div class="flex h-[74px] w-[74px] shrink-0 items-center justify-center rounded-full {{ $card['bg'] }} text-[40px]">
                                        {{ $card['emoji'] }}
                                    </div>

                                    <div class="min-w-0 flex-1">
                                        <h3 class="text-[20px] font-black text-[#071433]">
                                            {{ $card['label'] }}
                                        </h3>

                                        <p class="mt-2 text-[15px] font-bold leading-relaxed text-[#46516B]">
                                            {!! $card['pc_description'] !!}
                                        </p>
                                    </div>
                                </div>

                                <div class="mt-5 flex items-center justify-between">
                                    <span class="rounded-lg border border-orange-200 bg-orange-50 px-3 py-1.5 text-[15px] font-bold text-orange-600">
                                        {{ $card['points'] }}
                                    </span>

                                    @if ($isDone)
                                        <span class="rounded-lg border border-emerald-200 bg-emerald-50 px-4 py-2 text-[15px] font-black text-emerald-700">
                                            完了
                                        </span>
                                    @else
                                        <span class="rounded-lg border border-[#DDE6F5] bg-[#F4F7FC] px-4 py-2 text-[15px] font-black text-[#46516B]">
                                            未実施
                                        </span>
                                    @endif
                                </div>
                            </a>
                        @endforeach
                    </div>
                </section>

                {{-- 下部：ランキング・履歴 --}}
                <section class="grid grid-cols-[320px_1fr] gap-5">
                    <div class="rounded-[16px] border border-[#DDE6F5] bg-white px-5 py-5 shadow-[0_8px_22px_rgba(15,43,95,0.06)]">
                        <h2 class="mb-4 flex items-center gap-2 text-[22px] font-black text-[#071433]">
                            🏆
                            ランキング
                        </h2>

                        <p class="mb-5 text-[15px] font-bold leading-relaxed text-[#46516B]">
                            他のユーザーとポイントを<br>
                            競い合って、モチベーションUP！
                        </p>

                        <a href="{{ route('trainings.ranking') }}"
                            class="flex h-[54px] items-center justify-center gap-3 rounded-[12px] bg-[#0D4FE8] text-[17px] font-black text-white shadow-[0_10px_18px_rgba(13,79,232,0.24)]">
                            ランキングを見る
                            <span class="text-[24px]">›</span>
                        </a>
                    </div>

                    <div class="rounded-[16px] border border-[#DDE6F5] bg-white px-5 py-5 shadow-[0_8px_22px_rgba(15,43,95,0.06)]">
                        <div class="mb-4 flex items-center justify-between">
                            <h2 class="text-[22px] font-black text-[#071433]">
                                最近の履歴
                            </h2>
                        </div>

                        <div id="pcTrainingHistory" class="overflow-hidden rounded-[12px] border border-[#DDE6F5]">
                            @forelse ($trainings->take($historyLimit) as $training)
                                <a href="{{ $training['show_route'] ?? route('trainings.show', ['type' => $training['type'], 'id' => $training['id']]) }}"
                                    class="grid grid-cols-[34px_120px_1fr_70px_58px_24px] items-center gap-3 border-b border-[#E8EDF6] bg-white px-4 py-3 last:border-b-0">
                                    <span class="text-[24px]">🗓️</span>

                                    <span class="text-[15px] font-bold text-[#46516B]">
                                        {{ $training['training_date']->format('Y-m-d') }}
                                    </span>

                                    <span class="truncate text-[15px] font-black text-[#071433]">
                                        {{ $training['type_label'] }}
                                    </span>

                                    <span class="text-right text-[17px] font-black text-[#0D4FE8]">
                                        {{ $training['total_score'] ?? '-' }}点
                                    </span>

                                    <span class="rounded-full border border-orange-200 bg-orange-50 px-2 py-1 text-center text-[14px] font-bold text-orange-600">
                                        +{{ $training['earned_points'] }}pt
                                    </span>

                                    <span class="text-[24px] text-[#8793A8]">›</span>
                                </a>
                            @empty
                                <div class="px-5 py-8 text-center">
                                    <p class="text-[15px] font-bold text-[#64748B]">
                                        まだトレーニング履歴がありません。
                                    </p>
                                </div>
                            @endforelse
                        </div>
                    </div>
                </section>
            </main>

            {{-- 右側 --}}
            <aside class="space-y-5">
                {{-- 続けるコツ --}}
                <section class="rounded-[18px] border border-[#DDE6F5] bg-white px-6 py-6 shadow-[0_8px_22px_rgba(15,43,95,0.06)]">
                    <h2 class="mb-6 flex items-center gap-3 text-[22px] font-black text-[#071433]">
                        💡
                        トレーニングを続けるコツ
                    </h2>

                    <div class="space-y-6">
                        <div class="flex gap-4">
                            <span class="mt-1 flex h-6 w-6 shrink-0 items-center justify-center rounded-full bg-emerald-500 text-[14px] text-white">✓</span>
                            <div>
                                <h3 class="text-[17px] font-black text-[#071433]">毎日コツコツ続ける</h3>
                                <p class="mt-2 text-[14px] font-bold leading-relaxed text-[#46516B]">
                                    短時間でも毎日の積み重ねが大切です
                                </p>
                            </div>
                        </div>

                        <div class="flex gap-4">
                            <span class="mt-1 flex h-6 w-6 shrink-0 items-center justify-center rounded-full bg-emerald-500 text-[14px] text-white">✓</span>
                            <div>
                                <h3 class="text-[17px] font-black text-[#071433]">小さな達成を喜ぶ</h3>
                                <p class="mt-2 text-[14px] font-bold leading-relaxed text-[#46516B]">
                                    完了ごとに自分をほめることが継続の秘訣
                                </p>
                            </div>
                        </div>

                        <div class="flex gap-4">
                            <span class="mt-1 flex h-6 w-6 shrink-0 items-center justify-center rounded-full bg-emerald-500 text-[14px] text-white">✓</span>
                            <div>
                                <h3 class="text-[17px] font-black text-[#071433]">記録を振り返る</h3>
                                <p class="mt-2 text-[14px] font-bold leading-relaxed text-[#46516B]">
                                    過去の自分と比べて成長を実感しましょう
                                </p>
                            </div>
                        </div>

                        <div class="flex gap-4">
                            <span class="mt-1 flex h-6 w-6 shrink-0 items-center justify-center rounded-full bg-emerald-500 text-[14px] text-white">✓</span>
                            <div>
                                <h3 class="text-[17px] font-black text-[#071433]">さまざまなトレーニングに挑戦</h3>
                                <p class="mt-2 text-[14px] font-bold leading-relaxed text-[#46516B]">
                                    バランスよく取り組むと力が伸びます
                                </p>
                            </div>
                        </div>
                    </div>
                </section>

                {{-- 今日の状況 --}}
                <section class="rounded-[18px] border border-[#DDE6F5] bg-white px-6 py-6 shadow-[0_8px_22px_rgba(15,43,95,0.06)]">
                    <h2 class="mb-5 flex items-center gap-3 text-[22px] font-black text-[#071433]">
                        📊
                        今日の状況
                    </h2>

                    <div class="grid grid-cols-3 gap-3">
                        <div class="rounded-[12px] border border-[#DDE6F5] bg-white px-3 py-4 text-center">
                            <div class="text-[32px]">🔥</div>
                            <p class="mt-2 text-[14px] font-black text-[#334155]">連続</p>
                            <p class="mt-1 text-[28px] font-black leading-none text-[#071433]">
                                {{ $continuousDays }}<span class="text-[14px]">日</span>
                            </p>
                        </div>

                        <div class="rounded-[12px] border border-[#DDE6F5] bg-white px-3 py-4 text-center">
                            <div class="text-[32px]">🏅</div>
                            <p class="mt-2 text-[14px] font-black text-[#334155]">総pt</p>
                            <p class="mt-1 text-[25px] font-black leading-none text-[#071433]">
                                {{ $myTotalPoints }}<span class="text-[13px]">pt</span>
                            </p>
                        </div>

                        <div class="rounded-[12px] border border-[#DDE6F5] bg-white px-3 py-4 text-center">
                            <div class="text-[32px]">🏅</div>
                            <p class="mt-2 text-[14px] font-black text-[#334155]">月間</p>
                            <p class="mt-1 text-[28px] font-black leading-none text-[#071433]">
                                {{ $monthlyRank }}<span class="text-[14px]">位</span>
                            </p>
                        </div>
                    </div>
                </section>

                {{-- 応援カード --}}
                <section class="relative overflow-hidden rounded-[18px] border border-[#BFD6FF] bg-[#F0F7FF] px-6 py-6 shadow-[0_8px_22px_rgba(15,43,95,0.06)]">
                    <div class="pr-20">
                        <p class="text-[18px] font-black leading-relaxed text-[#0D4FE8]">
                            あなたの成長を応援しています！
                        </p>

                        <p class="mt-4 text-[15px] font-bold leading-relaxed text-[#334155]">
                            できたことを積み重ねると、<br>
                            未来の自分がきっと変わります。<br>
                            今日も一歩ずつ、<br>
                            前に進んでいきましょう！
                        </p>
                    </div>

                    <div class="absolute bottom-5 right-5 text-[64px]">
                        🌱
                    </div>
                </section>

                {{-- 目標設定 --}}
                <section class="relative overflow-hidden rounded-[18px] border border-[#DDE6F5] bg-white px-6 py-6 shadow-[0_8px_22px_rgba(15,43,95,0.06)]">
                    <div class="pr-20">
                        <p class="text-[18px] font-black leading-relaxed text-[#0D4FE8]">
                            目標設定で、もっと伸びる！
                        </p>

                        <p class="mt-4 text-[15px] font-bold leading-relaxed text-[#334155]">
                            目標を設定すると、達成への<br>
                            モチベーションが高まります。
                        </p>

                        <a href="{{ route('trainings.index') }}"
                            class="mt-5 flex h-[44px] w-[170px] items-center justify-center gap-2 rounded-[10px] border border-[#DDE6F5] bg-white text-[15px] font-black text-[#0D4FE8]">
                            目標を設定する
                            <span class="text-[20px]">›</span>
                        </a>
                    </div>

                    <div class="absolute bottom-6 right-4 text-[70px]">
                        🎯
                    </div>
                </section>
            </aside>
        </div>
    </div>
</div>
