<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * 具体・抽象トレーニング用テーブルを作成する。
     */
    public function up(): void
    {
        Schema::create('user_concept_trainings', function (Blueprint $table) {
            $table->id();

            $table->foreignId('user_id')
                ->constrained()
                ->cascadeOnDelete();

            $table->date('training_date');

            // 問題情報
            $table->string('question_title')->default('具体・抽象トレーニング');
            $table->string('theme_a', 100);
            $table->string('theme_b', 100);
            $table->string('normalized_pair_key', 255)->index();
            $table->string('difficulty_label', 20)->default('初級');
            $table->text('question_body');

            // AIが作成した参考情報
            $table->text('model_answer')->nullable();
            $table->text('alternative_answer')->nullable();
            $table->string('answer_point', 255)->nullable();

            // ユーザー回答
            $table->text('answer_body')->nullable();

            // 採点
            $table->unsignedTinyInteger('total_score')->nullable();
            $table->unsignedTinyInteger('common_point_score')->nullable();
            $table->unsignedTinyInteger('essence_score')->nullable();
            $table->unsignedTinyInteger('viewpoint_score')->nullable();
            $table->unsignedTinyInteger('explanation_score')->nullable();

            $table->text('good_point')->nullable();
            $table->text('improvement_point')->nullable();
            $table->text('next_task')->nullable();

            $table->unsignedTinyInteger('earned_points')->default(0);

            // AI情報
            $table->string('ai_provider', 50)->nullable();
            $table->string('ai_model', 100)->nullable();
            $table->string('ai_status', 50)->nullable();
            $table->text('ai_error_message')->nullable();
            $table->boolean('is_fallback')->default(false);
            $table->unsignedTinyInteger('ai_attempts')->default(0);

            $table->timestamps();

            // 1日1回制御
            $table->unique(['user_id', 'training_date'], 'user_concept_trainings_user_date_unique');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('user_concept_trainings');
    }
};
