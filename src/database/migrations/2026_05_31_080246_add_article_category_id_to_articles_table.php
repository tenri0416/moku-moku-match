<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * 記事にカテゴリーIDを追加する
     */
    public function up(): void
    {
        Schema::table('articles', function (Blueprint $table) {
            $table->foreignId('article_category_id')
                ->nullable()
                ->after('id')
                ->constrained('article_categories')
                ->nullOnDelete()
                ->comment('記事カテゴリーID');

            $table->index('article_category_id');
        });
    }

    /**
     * 記事からカテゴリーIDを削除する
     */
    public function down(): void
    {
        Schema::table('articles', function (Blueprint $table) {
            $table->dropConstrainedForeignId('article_category_id');
        });
    }
};
