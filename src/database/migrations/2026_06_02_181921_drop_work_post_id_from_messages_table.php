<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('messages', function (Blueprint $table) {
            // 外部キー制約を削除
            $table->dropForeign(['work_post_id']);

            // インデックスがある場合は削除
            $table->dropIndex(['work_post_id']);

            // カラムを削除
            $table->dropColumn('work_post_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('messages', function (Blueprint $table) {
            $table->foreignId('work_post_id')
                ->nullable()
                ->after('id')
                ->constrained('work_posts')
                ->cascadeOnDelete()
                ->comment('募集ID');

            $table->index('work_post_id');
        });
    }
};
