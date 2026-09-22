<?php

namespace Tests\Unit;

use App\Services\Stocks\GoogleNewsService;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class GoogleNewsServiceTest extends TestCase
{
    public function test_it_parses_google_news_rss(): void
    {
        Http::fake([
            'news.google.com/*' => Http::response(<<<'XML'
<?xml version="1.0" encoding="UTF-8"?>
<rss version="2.0"><channel>
<item>
<title>テスト株式会社が業績予想を修正</title>
<link>https://news.google.com/example</link>
<guid>abc-123</guid>
<pubDate>Mon, 21 Sep 2026 23:00:00 GMT</pubDate>
<description><![CDATA[<p>テスト本文です。</p>]]></description>
<source>テスト新聞</source>
</item>
</channel></rss>
XML, 200, ['Content-Type' => 'application/rss+xml']),
        ]);

        $items = app(GoogleNewsService::class)->search('1234', 'テスト株式会社');

        $this->assertCount(1, $items);
        $this->assertSame('abc-123', $items[0]['source_id']);
        $this->assertSame('テスト株式会社が業績予想を修正', $items[0]['title']);
        $this->assertSame('テスト新聞', $items[0]['source']);
    }
}
