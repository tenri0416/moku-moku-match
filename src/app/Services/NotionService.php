<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;

class NotionService
{
    public function hasTodayDiary(): bool
    {
        $today = now('Asia/Tokyo')->toDateString();

        $response = Http::withToken(config('services.notion.token'))
            ->withHeaders([
                'Notion-Version' => '2026-03-11',
                'Content-Type' => 'application/json',
            ])
            ->post(
                'https://api.notion.com/v1/data_sources/'
                . config('services.notion.data_source_id')
                . '/query',
                [
                    'filter' => [
                        'property' => '日付',
                        'date' => [
                            'equals' => $today,
                        ],
                    ],
                    'page_size' => 1,
                ]
            );

        $response->throw();

        return count($response->json('results', [])) > 0;
    }
}
