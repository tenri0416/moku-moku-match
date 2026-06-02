<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * 管理者用トレーニング記録テーブルを作成する
     */
    public function up(): void
    {
        Schema::create('admin_trainings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('admin_id')->constrained('admins')->cascadeOnDelete();

            // diary: 日記トレーニング / challenge: 今日のチャレンジ
            $table->string('type')->comment('種別 diary:日記 challenge:今日のチャレンジ');

            $table->date('training_date')->comment('対象日');

            $table->string('question_title')->nullable()->comment('AIが作成した問題タイトル');

            // 日記トレーニング用
            $table->text('diary_body')->nullable()->comment('日記本文');
            $table->longText('question_body')->nullable()->comment('AIが作成した問題本文');
            $table->longText('answer_body')->nullable()->comment('管理者の回答');

            // 今日のチャレンジ用
            $table->text('challenged_thing')->nullable()->comment('今日チャレンジしたこと');
            $table->text('completed_thing')->nullable()->comment('できたこと');
            $table->text('difficult_thing')->nullable()->comment('難しかったこと');
            $table->text('next_improvement')->nullable()->comment('次に改善したいこと');

            // 採点結果
            $table->unsignedTinyInteger('total_score')->nullable()->comment('総合点');
            $table->unsignedTinyInteger('readability_score')->nullable()->comment('読みやすさ');
            $table->unsignedTinyInteger('specificity_score')->nullable()->comment('具体性');
            $table->unsignedTinyInteger('structure_score')->nullable()->comment('構成');
            $table->unsignedTinyInteger('expression_score')->nullable()->comment('表現力');

            // アドバイス
            $table->text('good_point')->nullable()->comment('良い点');
            $table->text('improvement_point')->nullable()->comment('改善点');
            $table->text('next_task')->nullable()->comment('次回の課題');

            // AI結果の元データ
            $table->json('ai_response')->nullable()->comment('AI採点レスポンス');

            $table->timestamps();

            $table->index(['admin_id', 'type', 'training_date']);
        });
    }

    /**
     * 管理者用トレーニング記録テーブルを削除する
     */
    public function down(): void
    {
        Schema::dropIfExists('admin_trainings');
    }
};
