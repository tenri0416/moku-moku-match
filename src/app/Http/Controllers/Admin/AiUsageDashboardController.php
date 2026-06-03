<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AiProviderAttemptLog;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class AiUsageDashboardController extends Controller
{
    public function index(Request $request): View
    {
        $from = $request->input('from', now()->subDays(14)->toDateString());
        $to = $request->input('to', now()->toDateString());

        $providerCards = collect(['google', 'openrouter', 'groq'])
            ->map(fn (string $provider) => $this->buildProviderCard($provider))
            ->values();

        $totalAttempts = AiProviderAttemptLog::query()
            ->whereBetween('attempted_at', [$from . ' 00:00:00', $to . ' 23:59:59'])
            ->count();

        $successAttempts = AiProviderAttemptLog::query()
            ->whereBetween('attempted_at', [$from . ' 00:00:00', $to . ' 23:59:59'])
            ->where('status', 'success')
            ->count();

        $failedAttempts = AiProviderAttemptLog::query()
            ->whereBetween('attempted_at', [$from . ' 00:00:00', $to . ' 23:59:59'])
            ->where('status', 'failed')
            ->count();

        $fallbackAttempts = AiProviderAttemptLog::query()
            ->whereBetween('attempted_at', [$from . ' 00:00:00', $to . ' 23:59:59'])
            ->where('is_fallback', true)
            ->count();

        $successRate = $totalAttempts > 0
            ? round(($successAttempts / $totalAttempts) * 100, 1)
            : 0;

        $providerStats = AiProviderAttemptLog::query()
            ->select([
                'provider',
                DB::raw('COUNT(*) as total_count'),
                DB::raw("SUM(CASE WHEN status = 'success' THEN 1 ELSE 0 END) as success_count"),
                DB::raw("SUM(CASE WHEN status = 'failed' THEN 1 ELSE 0 END) as failed_count"),
                DB::raw("SUM(CASE WHEN is_fallback = 1 THEN 1 ELSE 0 END) as fallback_count"),
            ])
            ->whereBetween('attempted_at', [$from . ' 00:00:00', $to . ' 23:59:59'])
            ->groupBy('provider')
            ->orderByDesc('total_count')
            ->get();

        $dailyStats = AiProviderAttemptLog::query()
            ->select([
                DB::raw('DATE(attempted_at) as usage_date'),
                DB::raw('COUNT(*) as total_count'),
                DB::raw("SUM(CASE WHEN provider = 'google' THEN 1 ELSE 0 END) as google_count"),
                DB::raw("SUM(CASE WHEN provider = 'openrouter' THEN 1 ELSE 0 END) as openrouter_count"),
                DB::raw("SUM(CASE WHEN provider = 'groq' THEN 1 ELSE 0 END) as groq_count"),
                DB::raw("SUM(CASE WHEN status = 'failed' THEN 1 ELSE 0 END) as failed_count"),
            ])
            ->whereBetween('attempted_at', [$from . ' 00:00:00', $to . ' 23:59:59'])
            ->groupBy(DB::raw('DATE(attempted_at)'))
            ->orderBy('usage_date')
            ->get();

        $recentAttempts = AiProviderAttemptLog::query()
            ->orderByDesc('attempted_at')
            ->limit(30)
            ->get();

        $recentErrors = AiProviderAttemptLog::query()
            ->where('status', 'failed')
            ->orderByDesc('attempted_at')
            ->limit(12)
            ->get();

        return view('admin.ai-usage.index', compact(
            'from',
            'to',
            'providerCards',
            'totalAttempts',
            'successAttempts',
            'failedAttempts',
            'fallbackAttempts',
            'successRate',
            'providerStats',
            'dailyStats',
            'recentAttempts',
            'recentErrors'
        ));
    }

    private function buildProviderCard(string $provider): array
    {
        $dailyLimit = (int) config("services.ai.provider_daily_limits.{$provider}", 0);

        $usedToday = AiProviderAttemptLog::query()
            ->where('provider', $provider)
            ->whereDate('attempted_at', today())
            ->whereIn('status', ['success', 'failed'])
            ->count();

        $remainingToday = $dailyLimit > 0
            ? max(0, $dailyLimit - $usedToday)
            : null;

        $latestLog = AiProviderAttemptLog::query()
            ->where('provider', $provider)
            ->orderByDesc('attempted_at')
            ->first();

        $retryAvailableAt = AiProviderAttemptLog::query()
            ->where('provider', $provider)
            ->whereNotNull('retry_available_at')
            ->where('retry_available_at', '>', now())
            ->orderByDesc('retry_available_at')
            ->value('retry_available_at');

        $retryAvailableAt = $retryAvailableAt
            ? Carbon::parse($retryAvailableAt)
            : null;

        $isCoolingDown = $retryAvailableAt !== null && $retryAvailableAt->isFuture();
        $isLimitReachedToday = $dailyLimit > 0 && $remainingToday === 0;

        $status = 'available';
        $statusLabel = '利用可能';

        if ($isCoolingDown) {
            $status = 'cooldown';
            $statusLabel = '一時制限中';
        } elseif ($isLimitReachedToday) {
            $status = 'limit_reached';
            $statusLabel = '本日の推定上限';
        } elseif ($latestLog && $latestLog->status === 'failed') {
            $status = 'warning';
            $statusLabel = '直近失敗あり';
        }

        $successToday = AiProviderAttemptLog::query()
            ->where('provider', $provider)
            ->whereDate('attempted_at', today())
            ->where('status', 'success')
            ->count();

        $failedToday = AiProviderAttemptLog::query()
            ->where('provider', $provider)
            ->whereDate('attempted_at', today())
            ->where('status', 'failed')
            ->count();

        return [
            'provider' => $provider,
            'label' => $this->providerLabel($provider),
            'model' => $latestLog?->model ?? $this->defaultModel($provider),
            'status' => $status,
            'status_label' => $statusLabel,
            'daily_limit' => $dailyLimit,
            'used_today' => $usedToday,
            'remaining_today' => $remainingToday,
            'success_today' => $successToday,
            'failed_today' => $failedToday,
            'retry_available_at' => $retryAvailableAt?->toIso8601String(),
            'retry_available_at_text' => $retryAvailableAt?->format('Y-m-d H:i:s'),
            'last_error_message' => $latestLog?->status === 'failed' ? $latestLog->error_message : null,
            'last_attempted_at' => $latestLog?->attempted_at?->format('Y-m-d H:i:s'),
        ];
    }

    private function providerLabel(string $provider): string
    {
        return match ($provider) {
            'google' => 'Gemini',
            'openrouter' => 'OpenRouter',
            'groq' => 'Groq',
            default => $provider,
        };
    }

    private function defaultModel(string $provider): string
    {
        return match ($provider) {
            'google' => (string) config('services.google_ai.model', 'gemini-2.5-flash'),
            'openrouter' => (string) config('services.openrouter.model', 'openrouter/free'),
            'groq' => (string) config('services.groq.model', 'llama-3.1-8b-instant'),
            default => '-',
        };
    }
}
