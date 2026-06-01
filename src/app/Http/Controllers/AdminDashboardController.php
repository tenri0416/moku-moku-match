<?php

namespace App\Http\Controllers;

use App\Models\Report;
use App\Models\User;
use App\Models\WorkPost;
use App\Support\ApiActionLogger;
use Illuminate\Support\Facades\Auth;

class AdminDashboardController extends Controller
{
    public function index()
    {
        $userCount = User::count();
        $workPostCount = WorkPost::count();
        $openReportCount = Report::where('status', Report::STATUS_OPEN)->count();

        $latestReports = Report::with(['reporter.profile', 'reportedUser.profile', 'workPost'])
            ->latest()
            ->limit(5)
            ->get();

        ApiActionLogger::info(
            'AdminDashboardController::index',
            '管理者ダッシュボードにアクセス',
            [
                'admin_id' => Auth::guard('admin')->id(),
                'user_count' => $userCount,
                'work_post_count' => $workPostCount,
                'open_report_count' => $openReportCount,
            ]
        );

        return view('admin.dashboard', compact('userCount', 'workPostCount', 'openReportCount', 'latestReports'));
    }
}
