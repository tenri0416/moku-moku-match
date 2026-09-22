<?php

namespace App\Services\Stocks;

use App\Models\StockEvent;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Http;
use RuntimeException;

class GroqStockAnalyzer
{
    public function analyze(StockEvent $event): array
    {
        $apiKey = (string) config('stock_alert.groq.api_key');
        if ($apiKey === '') {
            throw new RuntimeException('STOCK_GROQ_API_KEY is not configured.');
        }

        $event->loadMissing('watchlist');

        $response = Http::withToken($apiKey)
            ->acceptJson()
            ->asJson()
            ->timeout(config('stock_alert.groq.timeout', 30))
            ->retry(2, 800)
            ->post(config('stock_alert.groq.endpoint'), [
                'model' => config('stock_alert.groq.model'),
                'messages' => [
                    [
                        'role' => 'system',
                        'content' => $this->systemPrompt(),
                    ],
                    [
                        'role' => 'user',
                        'content' => json_encode([
                            'stock_code' => $event->watchlist->stock_code,
                            'company_name' => $event->watchlist->company_name,
                            'news_title' => $event->title,
                            'news_summary' => $event->summary_text,
                            'source' => $event->source,
                            'published_at' => $event->published_at?->toIso8601String(),
                        ], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES),
                    ],
                ],
                'response_format' => [
                    'type' => 'json_schema',
                    'json_schema' => [
                        'name' => 'stock_event_analysis',
                        'strict' => true,
                        'schema' => $this->schema(),
                    ],
                ],
            ]);

        if (!$response->successful()) {
            throw new RuntimeException('Groq API failed: HTTP '.$response->status().' '.$response->body());
        }

        $content = Arr::get($response->json(), 'choices.0.message.content');
        if (!is_string($content) || trim($content) === '') {
            throw new RuntimeException('Groq API returned empty content.');
        }

        $decoded = json_decode($content, true);
        if (!is_array($decoded)) {
            throw new RuntimeException('Groq API returned invalid JSON.');
        }

        return $this->normalize($decoded);
    }

    private function systemPrompt(): string
    {
        return <<<'PROMPT'
あなたは日本株の「情報監視専用」分析エージェントです。
入力されたニュースだけを根拠に、その情報が企業価値や株価材料として一般にどの方向へ働き得るかを整理してください。

重要ルール:
- 売買推奨をしてはいけません。
- 「上がる」「下がる」と断定してはいけません。
- 入力にない数字・事実・背景を作ってはいけません。
- 情報不足なら impact=uncertain、confidence を低くしてください。
- importance=high は、決算・業績修正・増減配・自社株買い・増資・M&A・大型受注・重大な行政処分・重大事故・不祥事など、株価へ大きく反応し得る材料に限定してください。
- 単なる紹介記事、一般論、重複ニュースは high にしないでください。
- reason は具体的な根拠を1〜3文で説明してください。
- risk は「反対方向に働く可能性」または「判断上の不足情報」を簡潔に書いてください。
PROMPT;
    }

    private function schema(): array
    {
        return [
            'type' => 'object',
            'properties' => [
                'impact' => [
                    'type' => 'string',
                    'enum' => ['positive', 'negative', 'neutral', 'uncertain'],
                ],
                'impact_score' => [
                    'type' => 'integer',
                    'minimum' => -100,
                    'maximum' => 100,
                ],
                'confidence' => [
                    'type' => 'integer',
                    'minimum' => 0,
                    'maximum' => 100,
                ],
                'importance' => [
                    'type' => 'string',
                    'enum' => ['low', 'medium', 'high'],
                ],
                'event_type' => [
                    'type' => 'string',
                    'enum' => ['earnings', 'guidance', 'dividend', 'buyback', 'financing', 'ma', 'order', 'product', 'regulatory', 'scandal', 'personnel', 'other'],
                ],
                'summary' => ['type' => 'string'],
                'reason' => ['type' => 'string'],
                'risk' => ['type' => 'string'],
            ],
            'required' => [
                'impact',
                'impact_score',
                'confidence',
                'importance',
                'event_type',
                'summary',
                'reason',
                'risk',
            ],
            'additionalProperties' => false,
        ];
    }

    private function normalize(array $data): array
    {
        $impact = in_array($data['impact'] ?? null, ['positive', 'negative', 'neutral', 'uncertain'], true)
            ? $data['impact']
            : 'uncertain';

        $importance = in_array($data['importance'] ?? null, ['low', 'medium', 'high'], true)
            ? $data['importance']
            : 'low';

        $eventTypes = ['earnings', 'guidance', 'dividend', 'buyback', 'financing', 'ma', 'order', 'product', 'regulatory', 'scandal', 'personnel', 'other'];
        $eventType = in_array($data['event_type'] ?? null, $eventTypes, true)
            ? $data['event_type']
            : 'other';

        $score = max(-100, min(100, (int) ($data['impact_score'] ?? 0)));
        $confidence = max(0, min(100, (int) ($data['confidence'] ?? 0)));
        $shouldAlert = $importance === 'high'
            && $confidence >= config('stock_alert.alert.min_confidence', 60);

        return [
            'provider' => 'groq',
            'model' => (string) config('stock_alert.groq.model'),
            'impact' => $impact,
            'impact_score' => $score,
            'confidence' => $confidence,
            'importance' => $importance,
            'classified_event_type' => $eventType,
            'should_alert' => $shouldAlert,
            'summary' => trim((string) ($data['summary'] ?? '')),
            'reason' => trim((string) ($data['reason'] ?? '')),
            'risk' => trim((string) ($data['risk'] ?? '')),
            'raw_json' => $data,
        ];
    }
}
