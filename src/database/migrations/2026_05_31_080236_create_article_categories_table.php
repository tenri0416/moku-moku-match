<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * 記事カテゴリーを作成する
     */
    public function up(): void
    {
        Schema::create('article_categories', function (Blueprint $table) {
            $table->id();
            $table->foreignId('parent_id')
                ->nullable()
                ->constrained('article_categories')
                ->nullOnDelete()
                ->comment('親カテゴリーID');

            $table->string('name')->comment('カテゴリー名');
            $table->string('slug')->unique()->comment('URL用スラッグ');
            $table->text('description')->nullable()->comment('説明文');
            $table->unsignedInteger('sort_order')->default(0)->comment('並び順');
            $table->boolean('is_active')->default(true)->comment('有効フラグ');
            $table->timestamps();

            $table->index('parent_id');
            $table->index('slug');
            $table->index('is_active');
            $table->comment('記事カテゴリー');
        });
    }

    /**
     * 記事カテゴリーを削除する
     */
    public function down(): void
    {
        Schema::dropIfExists('article_categories');
    }
};
