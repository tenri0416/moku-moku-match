<?php

namespace App\Http\Controllers;

use App\Models\Report;
use App\Support\ApiActionLogger;
use Illuminate\Support\Facades\Auth;

class AdminReportController extends Controller
{
    public function index()
    {
        ApiActionLogger::info(
            'AdminReportController::index',
            '管理者通報一覧画面にアクセス',
            [
                'admin_id' => Auth::guard('admin')->id(),
            ]
        );

        $reports = Report::with(['reporter.profile', 'reportedUser.profile', 'workPost'])
            ->latest()
            ->paginate(20);

        return view('admin.reports.index', compact('reports'));
    }

    public function show(Report $report)
    {
        ApiActionLogger::info(
            'AdminReportController::show',
            '管理者通報詳細画面にアクセス',
            [
                'admin_id' => Auth::guard('admin')->id(),
                'report_id' => $report->id,
                'reporter_id' => $report->reporter_id,
                'reported_user_id' => $report->reported_user_id,
                'work_post_id' => $report->work_post_id,
                'status' => $report->status,
            ]
        );

        $report->load(['reporter.profile', 'reportedUser.profile', 'workPost']);

        return view('admin.reports.show', compact('report'));
    }

    public function inProgress(Report $report)
    {
        ApiActionLogger::info(
            'AdminReportController::inProgress',
            '管理者通報を対応中に変更する処理開始',
            [
                'admin_id' => Auth::guard('admin')->id(),
                'report_id' => $report->id,
                'current_status' => $report->status,
            ]
        );

        $report->update(['status' => Report::STATUS_IN_PROGRESS]);

        ApiActionLogger::info(
            'AdminReportController::inProgress',
            '管理者通報を対応中に変更しました',
            [
                'admin_id' => Auth::guard('admin')->id(),
                'report_id' => $report->id,
                'status' => Report::STATUS_IN_PROGRESS,
            ]
        );

        return back()->with('success', '通報を対応中にしました。');
    }

    public function close(Report $report)
    {
        ApiActionLogger::info(
            'AdminReportController::close',
            '管理者通報を対応済みに変更する処理開始',
            [
                'admin_id' => Auth::guard('admin')->id(),
                'report_id' => $report->id,
                'current_status' => $report->status,
            ]
        );

        $report->update(['status' => Report::STATUS_CLOSED]);

        ApiActionLogger::info(
            'AdminReportController::close',
            '管理者通報を対応済みに変更しました',
            [
                'admin_id' => Auth::guard('admin')->id(),
                'report_id' => $report->id,
                'status' => Report::STATUS_CLOSED,
            ]
        );

        return back()->with('success', '通報を対応済みにしました。');
    }
}
