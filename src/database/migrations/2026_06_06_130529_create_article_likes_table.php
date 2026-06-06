<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * 記事いいねを保存する。
     */
    public function up(): void
    {
        Schema::create('article_likes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('article_id')->constrained()->cascadeOnDelete();
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            $table->string('browser_key', 100)->nullable()->comment('ゲスト判定用のブラウザ識別キー');
            $table->string('ip_address', 45)->nullable();
            $table->text('user_agent')->nullable();
            $table->timestamps();

            $table->unique(['article_id', 'user_id'], 'article_likes_article_user_unique');
            $table->unique(['article_id', 'browser_key'], 'article_likes_article_browser_unique');
            $table->index('article_id');
        });
    }

    /**
     * 記事いいねテーブルを削除する。
     */
    public function down(): void
    {
        Schema::dropIfExists('article_likes');
    }
};
