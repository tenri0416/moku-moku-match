<?php

namespace App\Http\Controllers;

use App\Models\Article;
use App\Models\WorkPost;
use App\Support\ApiActionLogger;
use App\Models\UserTrainingPointHistory;

class HomeController extends Controller
{
    public function index()
    {
        ApiActionLogger::info(
            'HomeController::index',
            'トップページにアクセス',
            [
                'user_id' => auth()->id(),
            ]
        );

        $keyword = request('keyword');
        $purpose = request('purpose');
        $locationType = request('location_type');
        $timeZone = request('time_zone');
        
        $homeWorkPosts = WorkPost::query()
            ->with(['user.profile'])
            ->when($keyword, function ($query, $keyword) {
                $query->where(function ($query) use ($keyword) {
                    $query->where('title', 'like', "%{$keyword}%")
                        ->orWhere('body', 'like', "%{$keyword}%")
                        ->orWhere('purpose', 'like', "%{$keyword}%")
                        ->orWhere('meeting_tool', 'like', "%{$keyword}%");
                });
            })
            ->when($purpose, fn ($query, $purpose) => $query->where('purpose', $purpose))
            ->when($locationType, fn ($query, $locationType) => $query->where('location_type', $locationType))
            ->when($timeZone, fn ($query, $timeZone) => $query->where('time_zone', $timeZone))
            ->latest()
            ->paginate(10)
            ->withQueryString();
        
        $allWorkPostCount = WorkPost::query()
            ->when($keyword, function ($query, $keyword) {
                $query->where(function ($query) use ($keyword) {
                    $query->where('title', 'like', "%{$keyword}%")
                        ->orWhere('body', 'like', "%{$keyword}%")
                        ->orWhere('purpose', 'like', "%{$keyword}%")
                        ->orWhere('meeting_tool', 'like', "%{$keyword}%");
                });
            })
            ->when($purpose, fn ($query, $purpose) => $query->where('purpose', $purpose))
            ->when($locationType, fn ($query, $locationType) => $query->where('location_type', $locationType))
            ->when($timeZone, fn ($query, $timeZone) => $query->where('time_zone', $timeZone))
            ->count();

        $latestArticles = Article::query()
            ->public()
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
            
            return view('home', compact(
                'latestArticles',
                'homeWorkPosts',
                'allWorkPostCount',
                'homeMonthlyTrainingRankings',
                'homeTotalTrainingRankings',
            ));
    }
}
