<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * 読書振り返りトレーニングテーブルを作成する。
     *
     * 暫定機能のため、既存のAIトレーニング・ポイント・ランキングとは分離する。
     */
    public function up(): void
    {
        Schema::create('user_reading_reflection_trainings', function (Blueprint $table) {
            $table->id();

            $table->foreignId('user_id')
                ->comment('ユーザーID')
                ->constrained()
                ->cascadeOnDelete();

            $table->date('read_on')
                ->comment('読書日');

            $table->string('book_title', 100)
                ->nullable()
                ->comment('本のタイトル');

            $table->unsignedSmallInteger('read_minutes')
                ->default(10)
                ->comment('読書時間（分）');

            $table->string('mood', 20)
                ->nullable()
                ->comment('読書後の感覚 good/normal/difficult');

            $table->text('reflection_text')
                ->comment('自分なりの解釈・感想');

            $table->timestamps();

            $table->unique(['user_id', 'read_on'], 'reading_reflection_user_date_unique');
            $table->index('read_on');
        });
    }

    /**
     * 読書振り返りトレーニングテーブルを削除する。
     */
    public function down(): void
    {
        Schema::dropIfExists('user_reading_reflection_trainings');
    }
};
