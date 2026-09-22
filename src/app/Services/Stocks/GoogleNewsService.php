<?php

namespace App\Services\Stocks;

use Carbon\CarbonImmutable;
use Illuminate\Support\Facades\Http;
use RuntimeException;

class GoogleNewsService
{
    /**
     * @return array<int, array{source:string,source_id:?string,title:string,summary_text:?string,source_url:?string,published_at:?\Carbon\CarbonImmutable,raw_payload:array}>
     */
    public function search(string $stockCode, string $companyName): array
    {
        $query = sprintf('"%s" OR "%s 東証"', $companyName, $stockCode);
        $url = 'https://news.google.com/rss/search';

        $response = Http::accept('application/rss+xml, application/xml, text/xml')
            ->withHeaders(['User-Agent' => 'MokuMokuMatch-StockMonitor/1.0'])
            ->timeout(config('stock_alert.news.timeout', 15))
            ->retry(2, 500)
            ->get($url, [
                'q' => $query,
                'hl' => 'ja',
                'gl' => 'JP',
                'ceid' => 'JP:ja',
            ]);

        if (!$response->successful()) {
            throw new RuntimeException('Google News RSS request failed: HTTP '.$response->status());
        }

        return $this->parse($response->body());
    }

    /**
     * @return array<int, array{source:string,source_id:?string,title:string,summary_text:?string,source_url:?string,published_at:?\Carbon\CarbonImmutable,raw_payload:array}>
     */
    public function parse(string $xmlBody): array
    {
        $previous = libxml_use_internal_errors(true);

        try {
            $xml = simplexml_load_string($xmlBody, 'SimpleXMLElement', LIBXML_NOCDATA | LIBXML_NONET);
            if ($xml === false || !isset($xml->channel->item)) {
                throw new RuntimeException('Google News RSS XML parse failed.');
            }

            $items = [];
            foreach ($xml->channel->item as $item) {
                $title = trim((string) $item->title);
                if ($title === '') {
                    continue;
                }

                $publishedAt = null;
                $pubDate = trim((string) $item->pubDate);
                if ($pubDate !== '') {
                    try {
                        $publishedAt = CarbonImmutable::parse($pubDate)->timezone('Asia/Tokyo');
                    } catch (\Throwable) {
                        $publishedAt = null;
                    }
                }

                $sourceName = isset($item->source) ? trim((string) $item->source) : 'Google News';
                $description = trim(strip_tags(html_entity_decode((string) $item->description, ENT_QUOTES | ENT_HTML5, 'UTF-8')));
                $guid = trim((string) $item->guid);

                $items[] = [
                    'source' => $sourceName !== '' ? $sourceName : 'Google News',
                    'source_id' => $guid !== '' ? $guid : null,
                    'title' => $title,
                    'summary_text' => $description !== '' ? $description : null,
                    'source_url' => trim((string) $item->link) ?: null,
                    'published_at' => $publishedAt,
                    'raw_payload' => [
                        'guid' => $guid !== '' ? $guid : null,
                        'pub_date' => $pubDate !== '' ? $pubDate : null,
                    ],
                ];
            }

            usort($items, function (array $a, array $b): int {
                $aTs = $a['published_at']?->getTimestamp() ?? 0;
                $bTs = $b['published_at']?->getTimestamp() ?? 0;
                return $bTs <=> $aTs;
            });

            return array_slice($items, 0, config('stock_alert.news.max_items', 20));
        } finally {
            libxml_clear_errors();
            libxml_use_internal_errors($previous);
        }
    }
}
