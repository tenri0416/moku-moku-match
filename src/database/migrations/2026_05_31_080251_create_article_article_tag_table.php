<?php

use App\Models\Article;
use App\Models\ArticleTag;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * 記事とタグの中間テーブルを作成する
     */
    public function up(): void
    {
        Schema::create('article_article_tag', function (Blueprint $table) {
            $table->id();

            $table->foreignIdFor(Article::class)
                ->constrained()
                ->cascadeOnDelete()
                ->comment('記事ID');

            $table->foreignIdFor(ArticleTag::class)
                ->constrained()
                ->cascadeOnDelete()
                ->comment('記事タグID');

            $table->timestamps();

            $table->unique(['article_id', 'article_tag_id']);
            $table->index('article_id');
            $table->index('article_tag_id');
            $table->comment('記事とタグの紐づけ');
        });
    }

    /**
     * 記事とタグの中間テーブルを削除する
     */
    public function down(): void
    {
        Schema::dropIfExists('article_article_tag');
    }
};
