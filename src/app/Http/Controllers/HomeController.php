<?php

namespace App\Http\Controllers;

use App\Models\Article;
use App\Models\Prefecture;
use App\Models\UserTrainingPointHistory;
use App\Models\WorkPost;
use App\Support\ApiActionLogger;
use Illuminate\Http\Request;
use Illuminate\View\View;
use App\Models\UserSatisfactionSurvey;
use Illuminate\Support\Facades\Auth;

class HomeController extends Controller
{
    public function index(Request $request): View
    {
        ApiActionLogger::info(
            'HomeController::index',
            'トップページにアクセス',
            [
                'user_id' => auth()->id(),
                'keyword' => $request->keyword,
                'purpose' => $request->purpose,
                'location_type' => $request->location_type,
                'time_zone' => $request->time_zone,
                'prefecture_id' => $request->prefecture_id,
                'ranking_mode' => $request->ranking_mode,
            ]
        );

        $shouldShowSatisfactionSurvey = false;

        if (Auth::check()) {
            $user = Auth::user();

            $minAccountAgeDays = (int) config('satisfaction_survey.min_account_age_days', 7);

            $isTargetUser = $user->created_at
                && $user->created_at->lte(now()->subDays($minAccountAgeDays));

            if ($isTargetUser) {
                $latestSurvey = UserSatisfactionSurvey::query()
                    ->where('user_id', $user->id)
                    ->latest()
                    ->first();

                $shouldShowSatisfactionSurvey = ! $latestSurvey
                    || ! $latestSurvey->next_display_at
                    || $latestSurvey->next_display_at->lte(now());
            }
        }

        $rankingMode = $request->input('ranking_mode', 'monthly');

        $homeWorkPostsQuery = WorkPost::query()
            ->with([
                'user.profile.prefecture',
            ])
            ->withCount('applications')
            ->when($request->keyword, function ($query, $keyword) {
                $query->where(function ($query) use ($keyword) {
                    $query->where('title', 'like', "%{$keyword}%")
                        ->orWhere('body', 'like', "%{$keyword}%")
                        ->orWhere('purpose', 'like', "%{$keyword}%")
                        ->orWhere('meeting_tool', 'like', "%{$keyword}%");
                });
            })
            ->when($request->purpose, function ($query, $purpose) {
                $query->where('purpose', $purpose);
            })
            ->when($request->location_type, function ($query, $locationType) {
                $query->where('location_type', $locationType);
            })
            ->when($request->time_zone, function ($query, $timeZone) {
                $query->where('time_zone', $timeZone);
            })
            ->when($request->prefecture_id, function ($query, $prefectureId) {
                $query->where('prefecture_id', $prefectureId);
            });

        $homeWorkPosts = $homeWorkPostsQuery
            ->latest()
            ->paginate(10)
            ->withQueryString();

        $allWorkPostCount = $homeWorkPosts->total();

        $homeArticles = Article::query()
            ->when(method_exists(Article::class, 'scopePublic'), function ($query) {
                $query->public();
            })
            ->with('prefecture')
            ->latest('published_at')
            ->take(6)
            ->get();

        $homeMonthlyTrainingRankings = UserTrainingPointHistory::query()
            ->select('user_id')
            ->selectRaw('SUM(points) as total_points')
            ->selectRaw('COUNT(*) as training_count')
            ->whereBetween('earned_on', [
                now()->startOfMonth()->toDateString(),
                now()->endOfMonth()->toDateString(),
            ])
            ->with('user.profile')
            ->groupBy('user_id')
            ->orderByDesc('total_points')
            ->limit(20)
            ->get();

        $homeTotalTrainingRankings = UserTrainingPointHistory::query()
            ->select('user_id')
            ->selectRaw('SUM(points) as total_points')
            ->selectRaw('COUNT(*) as training_count')
            ->with('user.profile')
            ->groupBy('user_id')
            ->orderByDesc('total_points')
            ->limit(20)
            ->get();

        $homeRankingUsers = $rankingMode === 'total'
            ? $homeTotalTrainingRankings
            : $homeMonthlyTrainingRankings;

        $prefectures = Prefecture::query()
            ->orderBy('id')
            ->get();

        $heroImageUrl = asset('images/home-top.png');
        $heroSpImageUrl = asset('images/home-top-sp.png');

        $quickFilterLinks = [
            ['label' => 'すべて', 'icon' => '◎', 'params' => []],
            ['label' => '黙々作業', 'icon' => '▣', 'params' => ['purpose' => '黙々作業']],
            ['label' => '勉強', 'icon' => '📖', 'params' => ['purpose' => '勉強']],
            ['label' => '情報交換', 'icon' => '💬', 'params' => ['purpose' => '情報交換']],
            ['label' => '朝', 'icon' => '🌅', 'params' => ['time_zone' => 'morning']],
            ['label' => '昼', 'icon' => '☀️', 'params' => ['time_zone' => 'daytime']],
            ['label' => '夜', 'icon' => '🌙', 'params' => ['time_zone' => 'night']],
            ['label' => 'オンライン', 'icon' => '🖥️', 'params' => ['location_type' => 'online']],
            ['label' => 'オフライン', 'icon' => '📍', 'params' => ['location_type' => 'offline']],
            ['label' => 'どちらでも可', 'icon' => '🔗', 'params' => ['location_type' => 'both']],
        ];

        $formatLocationType = function ($locationType) {
            return match ($locationType) {
                'online' => 'オンライン',
                'offline' => 'オフライン',
                'both' => 'どちらでも可',
                default => '未設定',
            };
        };

        $formatTimeZone = function ($timeZone) {
            return match ($timeZone) {
                'morning' => '朝',
                'daytime' => '昼',
                'night' => '夜',
                default => $timeZone ?: '未設定',
            };
        };

        $avatarUrl = function ($user) {
            $profile = $user?->profile;
            $avatarPath = $profile?->avatar_path;

            return $avatarPath
                ? asset('storage/' . ltrim($avatarPath, '/'))
                : asset('images/default-avatar.png');
        };

        $displayName = function ($user) {
            return $user?->profile?->display_name
                ?? $user?->name
                ?? 'ユーザー';
        };

        $jobType = function ($user) {
            return $user?->profile?->job_type
                ?? '職種未設定';
        };

        $topRankingUser = $homeRankingUsers->first();

        return view('home', compact(
            'rankingMode',
            'homeWorkPosts',
            'allWorkPostCount',
            'homeArticles',
            'homeMonthlyTrainingRankings',
            'homeTotalTrainingRankings',
            'homeRankingUsers',
            'topRankingUser',
            'prefectures',
            'heroImageUrl',
            'heroSpImageUrl',
            'quickFilterLinks',
            'formatLocationType',
            'formatTimeZone',
            'avatarUrl',
            'displayName',
            'jobType',
            'shouldShowSatisfactionSurvey'
        ));
    }
}
