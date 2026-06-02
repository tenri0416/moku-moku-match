<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    private array $tables = [
        'user_diary_trainings',
        'user_challenge_trainings',
        'user_summary_trainings',
        'user_verbalization_trainings',
        'user_abstraction_trainings',
        'user_concretization_trainings',
    ];

    public function up(): void
    {
        foreach ($this->tables as $tableName) {
            Schema::table($tableName, function (Blueprint $table) {
                if (! Schema::hasColumn($table->getTable(), 'ai_provider')) {
                    $table->string('ai_provider')->nullable()->comment('採点に使用したAI会社 google/openrouter/groq/local');
                }

                if (! Schema::hasColumn($table->getTable(), 'ai_model')) {
                    $table->string('ai_model')->nullable()->comment('採点に使用したAIモデル名');
                }

                if (! Schema::hasColumn($table->getTable(), 'ai_status')) {
                    $table->string('ai_status')->nullable()->comment('AI採点結果 success/fallback/local_failed');
                }

                if (! Schema::hasColumn($table->getTable(), 'ai_error_message')) {
                    $table->text('ai_error_message')->nullable()->comment('AI失敗時のエラーメッセージ');
                }

                if (! Schema::hasColumn($table->getTable(), 'is_fallback')) {
                    $table->boolean('is_fallback')->default(false)->comment('フォールバックで採点されたか');
                }

                if (! Schema::hasColumn($table->getTable(), 'ai_attempts')) {
                    $table->unsignedTinyInteger('ai_attempts')->default(1)->comment('AI採点の試行回数');
                }
            });
        }
    }

    public function down(): void
    {
        foreach ($this->tables as $tableName) {
            Schema::table($tableName, function (Blueprint $table) {
                $table->dropColumn([
                    'ai_provider',
                    'ai_model',
                    'ai_status',
                    'ai_error_message',
                    'is_fallback',
                    'ai_attempts',
                ]);
            });
        }
    }
};
