<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * AI会社ごとの試行ログを保存する
     */
    public function up(): void
    {
        Schema::create('ai_provider_attempt_logs', function (Blueprint $table) {
            $table->id();
            $table->string('provider')->comment('AI会社 google/openrouter/groq');
            $table->string('model')->nullable()->comment('AIモデル名');
            $table->string('status')->comment('success/failed/skipped');
            $table->unsignedSmallInteger('status_code')->nullable()->comment('HTTPステータス');
            $table->text('error_message')->nullable()->comment('エラーメッセージ');
            $table->unsignedInteger('retry_after_seconds')->nullable()->comment('再試行可能までの秒数');
            $table->timestamp('retry_available_at')->nullable()->comment('再試行可能予定日時');
            $table->unsignedTinyInteger('attempt')->nullable()->comment('試行回数');
            $table->boolean('is_fallback')->default(false)->comment('フォールバック実行か');
            $table->string('action_name')->nullable()->comment('対象処理名');
            $table->timestamp('attempted_at')->useCurrent()->comment('試行日時');
            $table->timestamps();

            $table->index(['provider', 'attempted_at']);
            $table->index(['status', 'attempted_at']);
            $table->index('retry_available_at');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('ai_provider_attempt_logs');
    }
};
