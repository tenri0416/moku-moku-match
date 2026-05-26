<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Report;

class AdminReportController extends Controller
{
    public function index()
    {
        $reports = Report::with(['reporter.profile', 'reportedUser.profile', 'workPost'])
            ->latest()
            ->paginate(20);

        return view('admin.reports.index', compact('reports'));
    }

    public function show(Report $report)
    {
        $report->load(['reporter.profile', 'reportedUser.profile', 'workPost']);

        return view('admin.reports.show', compact('report'));
    }

    public function inProgress(Report $report)
    {
        $report->update(['status' => Report::STATUS_IN_PROGRESS]);

        return back()->with('success', '通報を対応中にしました。');
    }

    public function close(Report $report)
    {
        $report->update(['status' => Report::STATUS_CLOSED]);

        return back()->with('success', '通報を対応済みにしました。');
    }
}
