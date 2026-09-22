<?php

namespace Tests\Unit;

use App\Models\StockEvent;
use App\Models\StockWatchlist;
use App\Services\Stocks\GroqStockAnalyzer;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class GroqStockAnalyzerTest extends TestCase
{
    use RefreshDatabase;

    public function test_it_normalizes_structured_groq_response(): void
    {
        config()->set('stock_alert.groq.api_key', 'test-key');
        config()->set('stock_alert.groq.model', 'openai/gpt-oss-20b');
        config()->set('stock_alert.groq.endpoint', 'https://api.groq.com/openai/v1/chat/completions');

        Http::fake([
            'api.groq.com/*' => Http::response([
                'choices' => [[
                    'message' => [
                        'content' => json_encode([
                            'impact' => 'negative',
                            'impact_score' => -70,
                            'confidence' => 82,
                            'importance' => 'high',
                            'event_type' => 'guidance',
                            'summary' => '業績予想の下方修正です。',
                            'reason' => '利益見通しの悪化を示します。',
                            'risk' => '既に織り込まれている可能性があります。',
                        ], JSON_UNESCAPED_UNICODE),
                    ],
                ]],
            ], 200),
        ]);

        $watchlist = StockWatchlist::query()->create([
            'stock_code' => '1234',
            'company_name' => 'テスト株式会社',
        ]);

        $event = StockEvent::query()->create([
            'stock_watchlist_id' => $watchlist->id,
            'source' => 'テスト新聞',
            'event_type' => 'news',
            'title' => '業績予想を下方修正',
            'content_hash' => hash('sha256', 'test'),
        ]);

        $result = app(GroqStockAnalyzer::class)->analyze($event);

        $this->assertSame('negative', $result['impact']);
        $this->assertSame('high', $result['importance']);
        $this->assertTrue($result['should_alert']);
        $this->assertSame(82, $result['confidence']);
    }
}
