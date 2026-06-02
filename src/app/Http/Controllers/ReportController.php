<?php

namespace App\Http\Controllers;

use App\Http\Requests\ReportStoreRequest;
use App\Models\Report;
use App\Models\User;
use App\Models\WorkPost;
use App\Support\ApiActionLogger;
use Illuminate\Http\Request;

class ReportController extends Controller
{
    public function create(Request $request)
    {
        ApiActionLogger::info(
            'ReportController::create',
            '通報作成画面にアクセス',
            [
                'user_id' => auth()->id(),
                'reported_user_id' => $request->query('reported_user_id'),
                'work_post_id' => $request->query('work_post_id'),
            ]
        );

        $reportedUser = User::findOrFail($request->query('reported_user_id'));
        $workPost = WorkPost::findOrFail($request->query('work_post_id'));

        return view('reports.create', compact('reportedUser', 'workPost'));
    }

    public function store(ReportStoreRequest $request)
    {
        ApiActionLogger::info(
            'ReportController::store',
            '通報送信処理開始',
            [
                'user_id' => auth()->id(),
                'validated' => $request->validated(),
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

        // 通報完了後は通報した募集の詳細ページへリダイレクトする
        return redirect()
        ->route('work-posts.show', $report->work_post_id)
        ->with('success', '通報を送信しました。');
    }
}
