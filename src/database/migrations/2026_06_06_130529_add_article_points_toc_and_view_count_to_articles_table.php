<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * 記事に「この記事のポイント」「目次」「閲覧数」を追加する。
     */
    public function up(): void
    {
        Schema::table('articles', function (Blueprint $table) {
            $table->text('point_text')->nullable()->after('body_css')->comment('この記事のポイント。1行ごとに箇条書きとして表示する');
            $table->text('toc_text')->nullable()->after('point_text')->comment('目次。1行ごとに箇条書きとして表示する');
            $table->unsignedInteger('view_count')->default(0)->after('reading_minutes')->comment('記事詳細の閲覧数');
        });
    }

    /**
     * 追加したカラムを削除する。
     */
    public function down(): void
    {
        Schema::table('articles', function (Blueprint $table) {
            $table->dropColumn([
                'point_text',
                'toc_text',
                'view_count',
            ]);
        });
    }
};
