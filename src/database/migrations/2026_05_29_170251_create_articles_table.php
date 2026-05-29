<?php

use App\Models\Article;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * 記事テーブルを作成する
     */
    public function up(): void
    {
        Schema::create('articles', function (Blueprint $table) {
            $table->id()->comment('記事ID');

            $table->foreignId('admin_id')
                ->nullable()
                ->constrained()
                ->nullOnDelete()
                ->comment('作成管理者管理者ID');

            $table->foreignId('prefecture_id')
                ->nullable()
                ->constrained('prefectures')
                ->nullOnDelete()
                ->comment('対象都道府県ID');

            $table->string('title', 150)->comment('記事タイトル');

            // 通常の記事URL: /articles/nara-freelance-work-partner
            $table->string('slug', 150)->unique()->comment('記事URL用スラッグ');

            // 短縮SEO URL: /nara のようにしたい場合だけ使う
            $table->string('short_slug', 150)->nullable()->unique()->comment('短縮URL用スラッグ');

            $table->string('seo_title', 150)->nullable()->comment('SEOタイトル');
            $table->string('seo_description', 255)->nullable()->comment('SEOディスクリプション');
            $table->string('h1_title', 150)->nullable()->comment('H1見出し');
            $table->string('excerpt', 255)->nullable()->comment('記事概要');

            // WordPress風にHTMLを保存する
            $table->longText('body_html')->comment('本文HTML');

            $table->string('thumbnail_path')->nullable()->comment('サムネイル画像パス');

            $table->unsignedTinyInteger('status')
                ->default(Article::STATUS_DRAFT)
                ->comment('公開状態 1:下書き 2:公開 3:非公開');

            $table->timestamp('published_at')->nullable()->comment('公開日時');

            $table->softDeletes()->comment('削除日時');
            $table->timestamps();

            $table->index('admin_id');
            $table->index('prefecture_id');
            $table->index('slug');
            $table->index('short_slug');
            $table->index('status');
            $table->index('published_at');
            $table->index('deleted_at');
        });
    }

    /**
     * 記事テーブルを削除する
     */
    public function down(): void
    {
        Schema::dropIfExists('articles');
    }
};
