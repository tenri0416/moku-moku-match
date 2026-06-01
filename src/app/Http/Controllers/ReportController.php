<?php

namespace App\Http\Controllers;

use App\Http\Requests\ReportStoreRequest;
use App\Models\Report;
use App\Support\ApiActionLogger;

class ReportController extends Controller
{
    public function create()
    {
        ApiActionLogger::info(
            'ReportController::create',
            '通報作成画面にアクセス',
            [
                'user_id' => auth()->id(),
            ]
        );

        return view('reports.create');
    }

    public function store(ReportStoreRequest $request)
    {
        ApiActionLogger::info(
            'ReportController::store',
            '通報送信処理開始',
            [
                'user_id' => auth()->id(),
            ]
        );

        $report = Report::create([
            ...$request->validated(),
            'reporter_id' => auth()->id(),
            'status' => Report::STATUS_OPEN,
        ]);

        ApiActionLogger::info(
            'ReportController::store',
            '通報送信成功',
            [
                'user_id' => auth()->id(),
                'report_id' => $report->id,
                'status' => $report->status,
            ]
        );

        return redirect()->route('work-posts.index')->with('success', '通報を送信しました。');
    }
}
