<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\StockEvent;
use App\Models\StockSetting;
use App\Models\StockWatchlist;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class StockAlertController extends Controller
{
    public function index(): View
    {
        return view('admin.stocks.index', [
            'watchlists' => StockWatchlist::query()->orderBy('stock_code')->get(),
            'events' => StockEvent::query()
                ->with(['watchlist', 'analysis'])
                ->latest('id')
                ->limit(50)
                ->get(),
            'setting' => StockSetting::singleton(),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $request->merge([
            'stock_code' => strtoupper(trim((string) $request->input('stock_code'))),
        ]);

        $validated = $request->validate([
            'stock_code' => ['required', 'regex:/^[0-9A-Z]{4}$/', 'unique:stock_watchlists,stock_code'],
            'company_name' => ['required', 'string', 'max:255'],
            'purchase_price' => ['nullable', 'numeric', 'min:0', 'max:9999999999.99'],
            'shares' => ['nullable', 'integer', 'min:1', 'max:4294967295'],
        ]);

        StockWatchlist::query()->create(array_merge($validated, [
            'is_active' => true,
            'notify_news' => true,
        ]));

        return back()->with('status', '監視銘柄を追加しました。初回スキャンは既存ニュースをベースライン登録し、即時LINE通知は行いません。');
    }

    public function update(Request $request, StockWatchlist $watchlist): RedirectResponse
    {
        $validated = $request->validate([
            'company_name' => ['required', 'string', 'max:255'],
            'purchase_price' => ['nullable', 'numeric', 'min:0', 'max:9999999999.99'],
            'shares' => ['nullable', 'integer', 'min:1', 'max:4294967295'],
            'is_active' => ['nullable', 'boolean'],
            'notify_news' => ['nullable', 'boolean'],
        ]);

        $watchlist->update([
            'company_name' => $validated['company_name'],
            'purchase_price' => $validated['purchase_price'] ?? null,
            'shares' => $validated['shares'] ?? null,
            'is_active' => $request->boolean('is_active'),
            'notify_news' => $request->boolean('notify_news'),
        ]);

        return back()->with('status', '監視設定を更新しました。');
    }

    public function destroy(StockWatchlist $watchlist): RedirectResponse
    {
        $watchlist->delete();
        return back()->with('status', '監視銘柄を削除しました。');
    }

    public function updateSettings(Request $request): RedirectResponse
    {
        $setting = StockSetting::singleton();
        $setting->update([
            'line_enabled' => $request->boolean('line_enabled'),
            'immediate_alert_enabled' => $request->boolean('immediate_alert_enabled'),
            'morning_summary_enabled' => $request->boolean('morning_summary_enabled'),
        ]);

        return back()->with('status', '通知設定を更新しました。');
    }
}
