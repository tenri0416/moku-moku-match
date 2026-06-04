<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * 満足度調査アンケートテーブルを作成する。
     */
    public function up(): void
    {
        Schema::create('user_satisfaction_surveys', function (Blueprint $table) {
            $table->id();

            $table->foreignId('user_id')
                ->constrained()
                ->cascadeOnDelete()
                ->comment('回答ユーザーID');

            $table->string('status', 20)
                ->comment('状態 answered:回答済み skipped:今月は回答しない');

            $table->unsignedTinyInteger('satisfaction')
                ->nullable()
                ->comment('満足度 1:不満 2:やや不満 3:普通 4:満足 5:とても満足');

            $table->text('improvement_text')
                ->nullable()
                ->comment('改善してほしい点');

            $table->timestamp('next_display_at')
                ->nullable()
                ->comment('次回表示可能日時');

            $table->timestamps();

            $table->index(['user_id', 'next_display_at']);
            $table->index(['status', 'created_at']);
        });
    }

    /**
     * 満足度調査アンケートテーブルを削除する。
     */
    public function down(): void
    {
        Schema::dropIfExists('user_satisfaction_surveys');
    }
};
