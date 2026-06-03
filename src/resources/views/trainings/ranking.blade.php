@extends('layouts.app')

@section('content')
@php
    $authUserId = auth()->id();

    $displayName = function ($ranking) {
        return $ranking->user?->profile?->display_name
            ?? $ranking->user?->name
            ?? 'ユーザー';
    };

    $profileLabel = function ($ranking) {
        return $ranking->user?->profile?->job_type
            ?? $ranking->user?->profile?->purpose
            ?? 'トレーニング中';
    };

    $avatarUrl = function ($ranking) {
        $user = $ranking->user;
        $profile = $user?->profile;
        $avatarPath = $profile?->avatar_path;

        if ($avatarPath) {
            return asset('storage/' . ltrim($avatarPath, '/'));
        }

        return asset('images/default-avatar.png');
    };

    $isMe = function ($ranking) use ($authUserId) {
        return (int) $ranking->user_id === (int) $authUserId;
    };

    $monthlyTopThree = $monthlyRankings->take(3)->values();
    $totalTopThree = $totalRankings->take(3)->values();

    $myMonthlyIndex = $monthlyRankings->search(fn ($ranking) => (int) $ranking->user_id === (int) $authUserId);
    $myMonthlyRanking = $myMonthlyIndex !== false ? $monthlyRankings[$myMonthlyIndex] : null;
    $myMonthlyRank = $myMonthlyIndex !== false ? $myMonthlyIndex + 1 : null;

    $topTenBorder = $monthlyRankings->get(9);
    $pointsToTopTen = null;

    if ($myMonthlyRanking && $topTenBorder && $myMonthlyRank > 10) {
        $pointsToTopTen = max(0, (int) $topTenBorder->total_points - (int) $myMonthlyRanking->total_points + 1);
    }

    $myMonthlyPoints = $myMonthlyRanking?->total_points ?? 0;
    $myMonthlyTrainingCount = $myMonthlyRanking?->training_count ?? 0;

    $continuousDays = $continuousDays ?? 7;
    $monthlyRank = $myMonthlyRank ?? 12;

    $buildMobileRows = function ($rankings) use ($authUserId) {
        $rows = collect();

        foreach ($rankings as $index => $ranking) {
            $rank = $index + 1;

            if ($rank >= 4 && $rank <= 10) {
                $rows->push([
                    'rank' => $rank,
                    'ranking' => $ranking,
                ]);
            }
        }

        $myIndex = $rankings->search(fn ($ranking) => (int) $ranking->user_id === (int) $authUserId);

        if ($myIndex !== false) {
            $myRank = $myIndex + 1;
            $myRanking = $rankings[$myIndex];

            $alreadyExists = $rows->contains(fn ($row) => (int) $row['ranking']->user_id === (int) $authUserId);

            if (! $alreadyExists) {
                $rows->push([
                    'rank' => $myRank,
                    'ranking' => $myRanking,
                ]);
            }
        }

        return $rows
            ->unique(fn ($row) => $row['rank'] . '-' . $row['ranking']->user_id)
            ->sortBy('rank')
            ->values();
    };

    $mobileMonthlyRows = $buildMobileRows($monthlyRankings);
    $mobileTotalRows = $buildMobileRows($totalRankings);

    $rankTextColor = function ($rank) {
        return match ($rank) {
            1 => 'text-yellow-600',
            2 => 'text-slate-500',
            3 => 'text-orange-600',
            default => 'text-[#071433]',
        };
    };

    $rankCrown = function ($rank) {
        return match ($rank) {
            1 => '👑',
            2 => '♛',
            3 => '♕',
            default => '',
        };
    };

    $podiumCardClass = function ($rank) {
        return match ($rank) {
            1 => 'bg-gradient-to-b from-yellow-50 to-white border-yellow-200 scale-105',
            2 => 'bg-white border-slate-200',
            3 => 'bg-white border-orange-200',
            default => 'bg-white border-slate-200',
        };
    };

    $avatarRingClass = function ($rank) {
        return match ($rank) {
            1 => 'ring-yellow-300',
            2 => 'ring-slate-300',
            3 => 'ring-orange-300',
            default => 'ring-blue-100',
        };
    };
@endphp

@include('trainings.ranking_sp')
@include('trainings.ranking_pc')

<script>
    document.addEventListener('DOMContentLoaded', function () {
        const applyTabStyle = function (tab, isActive) {
            const group = tab.dataset.rankingGroup;

            tab.style.transition = 'all 0.18s ease';
            tab.style.cursor = 'pointer';

            /**
             * PC版：青いカード上にあるボタン
             * 選択中：黄色
             * 未選択：白ベース
             */
            if (group === 'pc-ranking') {
                tab.style.borderRadius = '12px';
                tab.style.borderWidth = '1px';
                tab.style.borderStyle = 'solid';
                tab.style.padding = '12px 16px';
                tab.style.fontWeight = '900';
                tab.style.fontSize = '16px';

                if (isActive) {
                    tab.style.background = '#FACC15';
                    tab.style.color = '#071433';
                    tab.style.borderColor = '#FDE68A';
                    tab.style.boxShadow = '0 8px 18px rgba(250, 204, 21, 0.28)';
                } else {
                    tab.style.background = '#FFFFFF';
                    tab.style.color = '#0D4FE8';
                    tab.style.borderColor = 'rgba(255,255,255,0.85)';
                    tab.style.boxShadow = '0 6px 14px rgba(15,43,95,0.10)';
                }

                return;
            }

            /**
             * SP版：白背景上にあるボタン
             * 選択中：青
             * 未選択：白
             */
            tab.style.borderRadius = '12px';
            tab.style.borderWidth = '1px';
            tab.style.borderStyle = 'solid';
            tab.style.padding = '12px 16px';
            tab.style.fontWeight = '900';
            tab.style.fontSize = '22px';

            if (isActive) {
                tab.style.background = '#0D4FE8';
                tab.style.color = '#FFFFFF';
                tab.style.borderColor = '#0D4FE8';
                tab.style.boxShadow = '0 8px 18px rgba(13,79,232,0.18)';
            } else {
                tab.style.background = '#FFFFFF';
                tab.style.color = '#071433';
                tab.style.borderColor = '#DDE6F5';
                tab.style.boxShadow = '0 8px 18px rgba(15,43,95,0.06)';
            }
        };

        const switchRankingTab = function (group, selected) {
            document
                .querySelectorAll('[data-ranking-tab][data-ranking-group="' + group + '"]')
                .forEach(function (tab) {
                    const isActive = tab.dataset.rankingTab === selected;
                    applyTabStyle(tab, isActive);
                    tab.setAttribute('aria-selected', isActive ? 'true' : 'false');
                });

            document
                .querySelectorAll('[data-ranking-panel][data-ranking-group="' + group + '"]')
                .forEach(function (panel) {
                    const isActivePanel = panel.dataset.rankingPanel === selected;

                    if (isActivePanel) {
                        panel.classList.remove('hidden');
                        panel.style.display = 'block';
                    } else {
                        panel.classList.add('hidden');
                        panel.style.display = 'none';
                    }
                });
        };

        document.querySelectorAll('[data-ranking-tab]').forEach(function (button) {
            button.addEventListener('click', function () {
                const group = button.dataset.rankingGroup;
                const selected = button.dataset.rankingTab;

                if (!group || !selected) {
                    return;
                }

                switchRankingTab(group, selected);
            });
        });

        /**
         * 初期表示を明示的にセット
         * これにより、SP/PCどちらも初期状態で月間が選択中になります。
         */
        if (document.querySelector('[data-ranking-tab][data-ranking-group="sp-ranking"]')) {
            switchRankingTab('sp-ranking', 'monthly');
        }

        if (document.querySelector('[data-ranking-tab][data-ranking-group="pc-ranking"]')) {
            switchRankingTab('pc-ranking', 'monthly');
        }
    });
</script>
@endsection
