@extends('layouts.admin')

@section('content')
<style>
  .ai-bg {
      width: 100%;
      max-width: 100%;
      overflow-x: hidden;
      background:
          radial-gradient(circle at top left, rgba(251, 146, 60, .18), transparent 32%),
          radial-gradient(circle at top right, rgba(59, 130, 246, .14), transparent 30%),
          linear-gradient(135deg, #fff7ed 0%, #f8fafc 52%, #eef2ff 100%);
  }

  .glass-card {
      background: rgba(255, 255, 255, .82);
      backdrop-filter: blur(16px);
      border: 1px solid rgba(255, 255, 255, .75);
      box-shadow: 0 20px 55px rgba(15, 23, 42, .07);
  }

  .metric-card {
      background: linear-gradient(180deg, rgba(255,255,255,.94), rgba(255,255,255,.78));
      border: 1px solid rgba(255,255,255,.85);
      box-shadow: 0 14px 34px rgba(15, 23, 42, .055);
  }

  .provider-card {
      position: relative;
      overflow: hidden;
      border-radius: 24px;
      background: rgba(255, 255, 255, .88);
      border: 1px solid rgba(255,255,255,.9);
      box-shadow: 0 16px 42px rgba(15, 23, 42, .07);
      min-width: 0;
  }

  .provider-card::before {
      content: "";
      position: absolute;
      inset: -70px -70px auto auto;
      width: 160px;
      height: 160px;
      border-radius: 999px;
      background: rgba(249, 115, 22, .13);
      pointer-events: none;
  }

  .status-dot {
      width: 10px;
      height: 10px;
      border-radius: 999px;
      display: inline-block;
      flex-shrink: 0;
  }

  .status-available { background: #22c55e; }
  .status-cooldown { background: #f97316; }
  .status-limit_reached { background: #ef4444; }
  .status-warning { background: #eab308; }

  .mini-bar {
      height: 9px;
      border-radius: 999px;
      background: #e5e7eb;
      overflow: hidden;
  }

  .mini-bar > span {
      display: block;
      height: 100%;
      border-radius: 999px;
      background: linear-gradient(90deg, #fb923c, #f97316);
  }

  .chart-column {
      width: 100%;
      border-radius: 12px 12px 6px 6px;
      background: linear-gradient(180deg, #fb923c, #fdba74);
      min-height: 6px;
  }

  .ai-table-wrap {
      max-width: 100%;
      overflow-x: auto;
  }

  .ai-break {
      word-break: break-word;
      overflow-wrap: anywhere;
  }
</style>

<div class="ai-bg min-h-screen w-full overflow-x-hidden px-4 py-6 md:px-6 md:py-8">
  <div class="w-full max-w-[1500px] mx-auto">
            <div class="glass-card rounded-[32px] p-6 md:p-8 mb-8">
                <div class="flex flex-col lg:flex-row lg:items-end lg:justify-between gap-6">
                    <div>
                        <p class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-orange-100 text-orange-700 text-xs font-bold mb-4">
                            <span class="w-2 h-2 rounded-full bg-orange-500"></span>
                            AI Operations
                        </p>

                        <h1 class="text-3xl md:text-4xl font-black tracking-tight text-gray-950">
                            AI利用状況ダッシュボード
                        </h1>

                        <p class="mt-3 text-sm md:text-base text-gray-600 leading-7">
                            Gemini / OpenRouter / Groq の稼働状況、残り回数の推定、制限解除までの時間、成功率を確認できます。
                        </p>
                    </div>

                    <form method="GET" action="{{ route('admin.ai-usage.index') }}" class="bg-white/80 border border-white rounded-2xl p-3 shadow-sm">
                        <div class="flex flex-col sm:flex-row gap-2">
                            <input type="date"
                                   name="from"
                                   value="{{ $from }}"
                                   class="rounded-xl border-gray-200 text-sm">

                            <input type="date"
                                   name="to"
                                   value="{{ $to }}"
                                   class="rounded-xl border-gray-200 text-sm">

                            <button type="submit"
                                    class="rounded-xl bg-gray-950 px-5 py-2.5 text-sm font-bold text-white">
                                更新
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-8">
                <div class="metric-card rounded-3xl p-5">
                    <p class="text-xs font-bold text-gray-500">総試行数</p>
                    <p class="mt-2 text-3xl font-black text-gray-950">{{ number_format($totalAttempts) }}</p>
                    <p class="mt-1 text-xs text-gray-500">期間内のAIリクエスト総数</p>
                </div>

                <div class="metric-card rounded-3xl p-5">
                    <p class="text-xs font-bold text-gray-500">成功数</p>
                    <p class="mt-2 text-3xl font-black text-green-600">{{ number_format($successAttempts) }}</p>
                    <p class="mt-1 text-xs text-gray-500">AI応答成功</p>
                </div>

                <div class="metric-card rounded-3xl p-5">
                    <p class="text-xs font-bold text-gray-500">失敗数</p>
                    <p class="mt-2 text-3xl font-black text-red-600">{{ number_format($failedAttempts) }}</p>
                    <p class="mt-1 text-xs text-gray-500">上限・通信・モデルエラー等</p>
                </div>

                <div class="metric-card rounded-3xl p-5">
                    <p class="text-xs font-bold text-gray-500">成功率</p>
                    <p class="mt-2 text-3xl font-black text-blue-600">{{ $successRate }}%</p>
                    <p class="mt-1 text-xs text-gray-500">期間内の成功割合</p>
                </div>
            </div>

            <div class="grid grid-cols-1 xl:grid-cols-3 gap-5 mb-8">
                @foreach ($providerCards as $card)
                    @php
                        $dailyLimit = $card['daily_limit'] > 0 ? $card['daily_limit'] : 1;
                        $usedPercent = min(100, round(($card['used_today'] / $dailyLimit) * 100));
                    @endphp

                    <div class="provider-card p-6">
                        <div class="relative">
                            <div class="flex items-start justify-between gap-4">
                                <div>
                                    <div class="flex items-center gap-2">
                                        <span class="status-dot status-{{ $card['status'] }}"></span>
                                        <span class="text-xs font-black uppercase tracking-wider text-gray-500">
                                            {{ $card['status_label'] }}
                                        </span>
                                    </div>

                                    <h2 class="mt-3 text-2xl font-black text-gray-950">
                                        {{ $card['label'] }}
                                    </h2>

                                    <p class="mt-1 text-xs text-gray-500 break-all">
                                        {{ $card['model'] }}
                                    </p>
                                </div>

                                <div class="text-right">
                                    <p class="text-xs font-bold text-gray-500">今日の推定残り</p>
                                    <p class="text-2xl font-black {{ $card['remaining_today'] === 0 ? 'text-red-600' : 'text-gray-950' }}">
                                        @if ($card['remaining_today'] === null)
                                            -
                                        @else
                                            {{ $card['remaining_today'] }}
                                        @endif
                                    </p>
                                </div>
                            </div>

                            <div class="mt-6">
                                <div class="flex justify-between text-xs font-bold text-gray-500 mb-2">
                                    <span>使用 {{ $card['used_today'] }}</span>
                                    <span>上限 {{ $card['daily_limit'] ?: '-' }}</span>
                                </div>

                                <div class="mini-bar">
                                    <span style="width: {{ $usedPercent }}%"></span>
                                </div>
                            </div>

                            <div class="mt-5 grid grid-cols-2 gap-3">
                                <div class="rounded-2xl bg-green-50 p-3">
                                    <p class="text-xs font-bold text-green-700">今日の成功</p>
                                    <p class="text-xl font-black text-green-700">{{ $card['success_today'] }}</p>
                                </div>

                                <div class="rounded-2xl bg-red-50 p-3">
                                    <p class="text-xs font-bold text-red-700">今日の失敗</p>
                                    <p class="text-xl font-black text-red-700">{{ $card['failed_today'] }}</p>
                                </div>
                            </div>

                            <div class="mt-5 rounded-2xl bg-gray-50 p-4">
                                @if ($card['retry_available_at'])
                                    <p class="text-xs font-bold text-gray-500 mb-1">
                                        制限解除まで
                                    </p>

                                    <p class="text-lg font-black text-orange-600 ai-countdown"
                                       data-retry-at="{{ $card['retry_available_at'] }}">
                                        計算中...
                                    </p>

                                    <p class="mt-1 text-xs text-gray-500">
                                        予定時刻：{{ $card['retry_available_at_text'] }}
                                    </p>
                                @elseif ($card['remaining_today'] === 0)
                                    <p class="text-sm font-bold text-red-600">
                                        今日は推定上限に達しています。
                                    </p>
                                @else
                                    <p class="text-sm font-bold text-green-600">
                                        現在利用できる可能性が高いです。
                                    </p>
                                @endif
                            </div>

                            @if (!empty($card['last_error_message']))
                                <details class="mt-4">
                                    <summary class="cursor-pointer text-xs font-bold text-gray-500">
                                        直近エラーを見る
                                    </summary>
                                    <p class="mt-2 text-xs text-red-700 bg-red-50 rounded-xl p-3 break-words">
                                        {{ $card['last_error_message'] }}
                                    </p>
                                </details>
                            @endif
                        </div>
                    </div>
                @endforeach
            </div>

            <div class="grid grid-cols-1 xl:grid-cols-5 gap-6 mb-8">
                <div class="xl:col-span-3 glass-card rounded-[28px] p-6">
                    <div class="flex items-center justify-between mb-6">
                        <div>
                            <h2 class="text-lg font-black text-gray-950">日別AI利用トレンド</h2>
                            <p class="text-xs text-gray-500 mt-1">期間内の日別リクエスト数</p>
                        </div>
                    </div>

                    @php
                        $maxDaily = max(1, $dailyStats->max('total_count') ?? 1);
                    @endphp

                    <div class="h-72 flex items-end gap-2">
                        @forelse ($dailyStats as $day)
                            @php
                                $height = max(6, round(($day->total_count / $maxDaily) * 230));
                            @endphp

                            <div class="flex-1 flex flex-col items-center justify-end gap-2">
                                <div class="w-full flex items-end gap-1 h-[230px]">
                                    <div class="chart-column"
                                         title="total: {{ $day->total_count }}"
                                         style="height: {{ $height }}px"></div>
                                </div>
                                <p class="text-[10px] text-gray-500 rotate-[-35deg] whitespace-nowrap">
                                    {{ \Illuminate\Support\Carbon::parse($day->usage_date)->format('m/d') }}
                                </p>
                            </div>
                        @empty
                            <div class="w-full h-full flex items-center justify-center text-gray-500">
                                データがありません。
                            </div>
                        @endforelse
                    </div>
                </div>

                <div class="xl:col-span-2 glass-card rounded-[28px] p-6">
                    <h2 class="text-lg font-black text-gray-950 mb-1">AI会社別 成功/失敗</h2>
                    <p class="text-xs text-gray-500 mb-6">各AI会社の試行結果</p>

                    <div class="space-y-5">
                        @forelse ($providerStats as $stat)
                            @php
                                $max = max(1, $stat->total_count);
                                $successWidth = round(($stat->success_count / $max) * 100);
                                $failedWidth = round(($stat->failed_count / $max) * 100);
                            @endphp

                            <div>
                                <div class="flex justify-between mb-2">
                                    <p class="text-sm font-black text-gray-800">{{ $stat->provider }}</p>
                                    <p class="text-xs font-bold text-gray-500">{{ $stat->total_count }}件</p>
                                </div>

                                <div class="h-4 rounded-full overflow-hidden bg-gray-100 flex">
                                    <div class="bg-green-400" style="width: {{ $successWidth }}%"></div>
                                    <div class="bg-red-400" style="width: {{ $failedWidth }}%"></div>
                                </div>

                                <div class="mt-1 flex justify-between text-[11px] text-gray-500">
                                    <span>成功 {{ $stat->success_count }}</span>
                                    <span>失敗 {{ $stat->failed_count }}</span>
                                </div>
                            </div>
                        @empty
                            <div class="text-center text-gray-500 py-12">
                                データがありません。
                            </div>
                        @endforelse
                    </div>
                </div>
            </div>

            <div class="grid grid-cols-1 xl:grid-cols-2 gap-6">
                <div class="glass-card rounded-[28px] overflow-hidden">
                    <div class="px-6 py-5 border-b border-white/70">
                        <h2 class="text-lg font-black text-gray-950">直近のAI試行ログ</h2>
                    </div>

                    <div class="overflow-x-auto">
                        <table class="min-w-full text-sm">
                            <thead class="bg-white/50 text-gray-500">
                                <tr>
                                    <th class="px-4 py-3 text-left">日時</th>
                                    <th class="px-4 py-3 text-left">AI</th>
                                    <th class="px-4 py-3 text-left">状態</th>
                                    <th class="px-4 py-3 text-left">処理</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-white/70">
                                @forelse ($recentAttempts as $log)
                                    <tr>
                                        <td class="px-4 py-3 whitespace-nowrap text-xs text-gray-500">
                                            {{ $log->attempted_at?->format('m/d H:i:s') }}
                                        </td>
                                        <td class="px-4 py-3">
                                            <p class="font-bold text-gray-900">{{ $log->provider }}</p>
                                            <p class="text-xs text-gray-500">{{ $log->model }}</p>
                                        </td>
                                        <td class="px-4 py-3">
                                            @if ($log->status === 'success')
                                                <span class="px-2 py-1 rounded-full bg-green-100 text-green-700 text-xs font-bold">
                                                    success
                                                </span>
                                            @elseif ($log->status === 'failed')
                                                <span class="px-2 py-1 rounded-full bg-red-100 text-red-700 text-xs font-bold">
                                                    failed
                                                </span>
                                            @else
                                                <span class="px-2 py-1 rounded-full bg-gray-100 text-gray-700 text-xs font-bold">
                                                    skipped
                                                </span>
                                            @endif
                                        </td>
                                        <td class="px-4 py-3 text-xs text-gray-600">
                                            {{ $log->action_name ?? '-' }}
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="4" class="px-4 py-10 text-center text-gray-500">
                                            データがありません。
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>

                <div class="glass-card rounded-[28px] overflow-hidden">
                    <div class="px-6 py-5 border-b border-white/70">
                        <h2 class="text-lg font-black text-gray-950">直近のエラー</h2>
                    </div>

                    <div class="divide-y divide-white/70">
                        @forelse ($recentErrors as $error)
                            <div class="p-5">
                                <div class="flex items-center justify-between gap-3">
                                    <div>
                                        <p class="text-sm font-black text-gray-900">
                                            {{ $error->provider }} / {{ $error->model }}
                                        </p>
                                        <p class="text-xs text-gray-500">
                                            {{ $error->attempted_at?->format('Y-m-d H:i:s') }}
                                        </p>
                                    </div>

                                    @if ($error->retry_available_at && $error->retry_available_at->isFuture())
                                        <span class="px-3 py-1 rounded-full bg-orange-100 text-orange-700 text-xs font-bold ai-countdown"
                                              data-retry-at="{{ $error->retry_available_at->toIso8601String() }}">
                                            計算中...
                                        </span>
                                    @endif
                                </div>

                                <p class="mt-3 text-sm text-red-700 bg-red-50 rounded-2xl p-3 break-words">
                                    {{ $error->error_message }}
                                </p>
                            </div>
                        @empty
                            <div class="p-10 text-center text-gray-500">
                                エラーはありません。
                            </div>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        function updateAiCountdowns() {
            document.querySelectorAll('.ai-countdown').forEach(function (element) {
                const retryAt = element.dataset.retryAt;

                if (!retryAt) {
                    return;
                }

                const target = new Date(retryAt).getTime();
                const now = new Date().getTime();
                const diff = Math.max(0, target - now);

                if (diff <= 0) {
                    element.textContent = '再試行可能です';
                    element.classList.remove('text-orange-600');
                    element.classList.add('text-green-600');
                    return;
                }

                const totalSeconds = Math.ceil(diff / 1000);
                const minutes = Math.floor(totalSeconds / 60);
                const seconds = totalSeconds % 60;

                if (minutes > 0) {
                    element.textContent = 'あと ' + minutes + '分' + seconds + '秒';
                } else {
                    element.textContent = 'あと ' + seconds + '秒';
                }
            });
        }

        updateAiCountdowns();
        setInterval(updateAiCountdowns, 1000);
    </script>
@endsection
