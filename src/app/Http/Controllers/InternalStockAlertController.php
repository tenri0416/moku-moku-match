<?php

namespace App\Http\Controllers;

use App\Services\Stocks\StockMonitorService;
use App\Services\Stocks\StockMorningSummaryService;
use Illuminate\Http\JsonResponse;

class InternalStockAlertController extends Controller
{
    public function scan(StockMonitorService $service): JsonResponse
    {
        $result = $service->scan(true);

        return response()->json([
            'ok' => $result['errors'] === 0,
            'result' => $result,
        ], $result['errors'] === 0 ? 200 : 207);
    }

    public function morningSummary(StockMorningSummaryService $service): JsonResponse
    {
        try {
            $sent = $service->send();

            return response()->json([
                'ok' => true,
                'sent' => $sent,
            ]);
        } catch (\Throwable $e) {
            report($e);

            return response()->json([
                'ok' => false,
                'message' => $e->getMessage(),
            ], 500);
        }
    }
}
