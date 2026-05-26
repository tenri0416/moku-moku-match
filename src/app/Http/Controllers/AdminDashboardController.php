<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Report;
use App\Models\User;
use App\Models\WorkPost;


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

        return view('admin.dashboard', compact('userCount', 'workPostCount', 'openReportCount', 'latestReports'));
    }
}
