<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\UserSatisfactionSurvey;
use Illuminate\Http\Request;
use Illuminate\View\View;

class UserSatisfactionSurveyController extends Controller
{
    /**
     * 満足度調査アンケート一覧を表示する。
     */
    public function index(Request $request): View
    {
        $surveys = UserSatisfactionSurvey::query()
            ->with('user')
            ->when($request->filled('status'), function ($query) use ($request) {
                $query->where('status', $request->status);
            })
            ->when($request->filled('satisfaction'), function ($query) use ($request) {
                $query->where('satisfaction', $request->satisfaction);
            })
            ->latest()
            ->paginate(20)
            ->withQueryString();

        $totalCount = UserSatisfactionSurvey::count();

        $answeredCount = UserSatisfactionSurvey::where(
            'status',
            UserSatisfactionSurvey::STATUS_ANSWERED
        )->count();

        $skippedCount = UserSatisfactionSurvey::where(
            'status',
            UserSatisfactionSurvey::STATUS_SKIPPED
        )->count();

        $averageSatisfaction = UserSatisfactionSurvey::where(
            'status',
            UserSatisfactionSurvey::STATUS_ANSWERED
        )
            ->whereNotNull('satisfaction')
            ->avg('satisfaction');

        return view('admin.satisfaction-surveys.index', compact(
            'surveys',
            'totalCount',
            'answeredCount',
            'skippedCount',
            'averageSatisfaction'
        ));
    }
}
