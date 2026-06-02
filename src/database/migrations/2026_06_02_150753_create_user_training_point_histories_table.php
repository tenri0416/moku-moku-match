<?php

use App\Models\User;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * ユーザートレーニングポイント履歴テーブル
     */
    public function up(): void
    {
        Schema::create('user_training_point_histories', function (Blueprint $table) {
            $table->id();

            $table->foreignIdFor(User::class)
                ->constrained()
                ->cascadeOnDelete()
                ->comment('ユーザーID');

            $table->string('training_type', 50)->comment('トレーニング種別');
            $table->unsignedBigInteger('training_id')->comment('各トレーニングテーブルのID');

            $table->string('point_type', 50)->default('training')->comment('ポイント種別');
            $table->integer('points')->comment('獲得ポイント');
            $table->date('earned_on')->comment('獲得日');
            $table->string('note')->nullable()->comment('備考');

            $table->timestamps();

            $table->index(['user_id', 'earned_on']);
            $table->index(['training_type', 'training_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('user_training_point_histories');
    }
};
