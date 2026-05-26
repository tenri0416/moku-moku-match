<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('work_posts', function (Blueprint $table) {
            $table->id()->comment('募集ID');
            $table->foreignId('user_id')->constrained()->cascadeOnDelete()->comment('投稿者ユーザーID');
            $table->string('title', 100)->comment('タイトル');
            $table->text('body')->comment('募集内容');
            $table->string('purpose', 50)->comment('目的');
            $table->string('location_type', 20)->default('online')->comment('開催形式 online:オンライン offline:オフライン both:どちらでも可');
            $table->string('meeting_tool', 100)->nullable()->comment('使用ツール');
            $table->string('prefecture', 50)->nullable()->comment('都道府県');
            $table->dateTime('start_at')->nullable()->comment('開始日時');
            $table->dateTime('end_at')->nullable()->comment('終了日時');
            $table->string('time_zone', 20)->nullable()->comment('時間帯 morning:朝 daytime:昼 night:夜');
            $table->unsignedInteger('max_participants')->nullable()->comment('募集人数');
            $table->unsignedTinyInteger('status')->default(1)->comment('募集状態 1:募集中 2:終了 3:非公開');
            $table->timestamps();

            $table->index('user_id');
            $table->index('title');
            $table->index('purpose');
            $table->index('location_type');
            $table->index('prefecture');
            $table->index('start_at');
            $table->index('time_zone');
            $table->index('status');
            $table->index('created_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('work_posts');
    }
};
