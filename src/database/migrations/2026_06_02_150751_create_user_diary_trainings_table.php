<?php

use App\Models\User;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * ユーザー日記トレーニングテーブル
     */
    public function up(): void
    {
        Schema::create('user_diary_trainings', function (Blueprint $table) {
            $table->id();

            $table->foreignIdFor(User::class)
                ->constrained()
                ->cascadeOnDelete()
                ->comment('ユーザーID');

            $table->date('training_date')->comment('実施日');

            $table->longText('diary_body')->comment('日記本文');

            $table->unsignedTinyInteger('total_score')->nullable()->comment('総合点');
            $table->unsignedTinyInteger('readability_score')->nullable()->comment('読みやすさ');
            $table->unsignedTinyInteger('specificity_score')->nullable()->comment('具体性');
            $table->unsignedTinyInteger('structure_score')->nullable()->comment('構成');
            $table->unsignedTinyInteger('expression_score')->nullable()->comment('表現力');

            $table->text('good_point')->nullable()->comment('良い点');
            $table->text('improvement_point')->nullable()->comment('改善点');
            $table->text('next_task')->nullable()->comment('次回の課題');

            $table->unsignedInteger('earned_points')->default(0)->comment('獲得ポイント');
            $table->json('ai_response')->nullable()->comment('AIレスポンス');

            $table->timestamps();

            $table->unique(['user_id', 'training_date'], 'user_diary_daily_unique');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('user_diary_trainings');
    }
};
