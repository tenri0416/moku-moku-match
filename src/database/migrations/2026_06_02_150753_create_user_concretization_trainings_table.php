<?php

use App\Models\User;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * ユーザー具体化力トレーニングテーブル
     */
    public function up(): void
    {
        Schema::create('user_concretization_trainings', function (Blueprint $table) {
            $table->id();

            $table->foreignIdFor(User::class)
                ->constrained()
                ->cascadeOnDelete()
                ->comment('ユーザーID');

            $table->date('training_date')->comment('実施日');

            $table->string('question_title')->comment('問題タイトル');
            $table->longText('question_body')->comment('問題本文');
            $table->longText('answer_body')->nullable()->comment('回答本文');

            $table->unsignedTinyInteger('total_score')->nullable()->comment('総合点');
            $table->unsignedTinyInteger('readability_score')->nullable()->comment('具体例のわかりやすさ');
            $table->unsignedTinyInteger('specificity_score')->nullable()->comment('行動への落とし込み');
            $table->unsignedTinyInteger('structure_score')->nullable()->comment('相手目線');
            $table->unsignedTinyInteger('expression_score')->nullable()->comment('実行しやすさ');

            $table->text('good_point')->nullable()->comment('良い点');
            $table->text('improvement_point')->nullable()->comment('改善点');
            $table->text('next_task')->nullable()->comment('次回の課題');

            $table->unsignedInteger('earned_points')->default(0)->comment('獲得ポイント');
            $table->json('ai_response')->nullable()->comment('AIレスポンス');

            $table->timestamps();

            $table->unique(['user_id', 'training_date'], 'user_concretization_daily_unique');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('user_concretization_trainings');
    }
};
