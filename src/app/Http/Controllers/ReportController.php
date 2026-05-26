<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests\ReportStoreRequest;
use App\Models\Report;

class ReportController extends Controller
{
    public function create()
    {
        return view('reports.create');
    }

    public function store(ReportStoreRequest $request)
    {
        Report::create([
            ...$request->validated(),
            'reporter_id' => auth()->id(),
            'status' => Report::STATUS_OPEN,
        ]);

        return redirect()->route('work-posts.index')->with('success', '通報を送信しました。');
    }
}
