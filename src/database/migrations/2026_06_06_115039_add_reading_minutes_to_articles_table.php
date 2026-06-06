<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * 記事に読了時間を追加する。
     */
    public function up(): void
    {
        Schema::table('articles', function (Blueprint $table) {
            $table->unsignedSmallInteger('reading_minutes')
                ->default(3)
                ->after('excerpt')
                ->comment('読了時間（分）');

                $table->foreignId('author_user_id')
                ->nullable()
                ->after('id')
                ->constrained('users')
                ->nullOnDelete()
                ->comment('記事の著者として表示するユーザーID');
        });
    }

    public function down(): void
    {
        Schema::table('articles', function (Blueprint $table) {
            $table->dropColumn('reading_minutes');
            $table->foreignId('author_user_id')
                ->nullable()
                ->after('id')
                ->constrained('users')
                ->nullOnDelete()
                ->comment('記事の著者として表示するユーザーID');
        });
    }
};
