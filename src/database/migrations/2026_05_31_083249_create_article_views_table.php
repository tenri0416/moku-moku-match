<?php

use App\Models\Article;
use App\Models\User;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * 記事閲覧ログテーブルを作成する
     */
    public function up(): void
    {
        Schema::create('article_views', function (Blueprint $table) {
            $table->id();

            $table->foreignIdFor(Article::class)
                ->constrained()
                ->cascadeOnDelete()
                ->comment('記事ID');

            $table->foreignIdFor(User::class)
                ->nullable()
                ->constrained()
                ->nullOnDelete()
                ->comment('閲覧ユーザーID');

            $table->string('ip_address', 45)->nullable()->comment('IPアドレス');
            $table->text('user_agent')->nullable()->comment('ユーザーエージェント');
            $table->string('referer')->nullable()->comment('参照元URL');
            $table->timestamps();

            $table->index('article_id');
            $table->index('user_id');
            $table->index('created_at');

            $table->comment('記事閲覧ログ');
        });
    }

    /**
     * 記事閲覧ログテーブルを削除する
     */
    public function down(): void
    {
        Schema::dropIfExists('article_views');
    }
};
