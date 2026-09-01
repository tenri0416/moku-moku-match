<?php

namespace App\Console\Commands;

use App\Services\LineNotificationService;
use App\Services\NotionService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class CheckDailyDiary extends Command
{
    /**
     * The name and signature of the console command.
     */
    protected $signature = 'diary:check';

    /**
     * The console command description.
     */
    protected $description = 'Notionに今日の日記が存在しない場合、LINE通知を送信する';

    /**
     * Execute the console command.
     */
    public function handle(
        NotionService $notionService,
        LineNotificationService $lineNotificationService
    ): int {
        try {
            // Notionに今日の日記が存在するか確認
            if ($notionService->hasTodayDiary()) {
                $this->info('今日の日記は作成済みです。');

                return self::SUCCESS;
            }

            // 今日の日記が存在しない場合のみLINE通知
            $lineNotificationService->sendToAdmin(
                $this->buildLineDiaryReminderMessage()
            );

            $this->info('今日の日記が存在しないため、LINE通知を実行しました。');

            return self::SUCCESS;

        } catch (\Throwable $e) {
            Log::error('Notion日記チェック処理でエラーが発生しました。', [
                'message' => $e->getMessage(),
            ]);

            $this->error('Notion日記チェック処理でエラーが発生しました。');
            $this->error($e->getMessage());
            
            return self::FAILURE;
        }
    }

    /**
     * 日記未作成時のLINE通知メッセージを作成する。
     */
    private function buildLineDiaryReminderMessage(): string
    {
        $today = now('Asia/Tokyo')->format('Y年m月d日');

        return <<<TEXT
📝 日記の書き忘れ通知

{$today}の日記がまだ作成されていません。

Notionに今日の日記を書いてください。
TEXT;
    }
}
