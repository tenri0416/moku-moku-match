<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * AI出題型トレーニングに模範解答と回答ポイントを追加する。
     */
    public function up(): void
    {
        $tables = [
            'user_summary_trainings',
            'user_verbalization_trainings',
            'user_abstraction_trainings',
            'user_concretization_trainings',
        ];

        foreach ($tables as $tableName) {
            Schema::table($tableName, function (Blueprint $table) use ($tableName) {
                if (! Schema::hasColumn($tableName, 'model_answer')) {
                    $table->text('model_answer')
                        ->nullable()
                        ->after('question_body')
                        ->comment('AIが作成した模範解答例');
                }

                if (! Schema::hasColumn($tableName, 'answer_point')) {
                    $table->text('answer_point')
                        ->nullable()
                        ->after('model_answer')
                        ->comment('回答時に意識するポイント');
                }
            });
        }
    }

    /**
     * 追加したカラムを削除する。
     */
    public function down(): void
    {
        $tables = [
            'user_summary_trainings',
            'user_verbalization_trainings',
            'user_abstraction_trainings',
            'user_concretization_trainings',
        ];

        foreach ($tables as $tableName) {
            Schema::table($tableName, function (Blueprint $table) use ($tableName) {
                $dropColumns = [];

                if (Schema::hasColumn($tableName, 'model_answer')) {
                    $dropColumns[] = 'model_answer';
                }

                if (Schema::hasColumn($tableName, 'answer_point')) {
                    $dropColumns[] = 'answer_point';
                }

                if (! empty($dropColumns)) {
                    $table->dropColumn($dropColumns);
                }
            });
        }
    }
};
