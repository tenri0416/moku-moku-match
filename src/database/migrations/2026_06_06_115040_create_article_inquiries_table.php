<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * YomuWorksお問い合わせテーブルを作成する。
     */
    public function up(): void
    {
        Schema::create('article_inquiries', function (Blueprint $table) {
            $table->id();
            $table->string('email')->comment('問い合わせメールアドレス');
            $table->text('body')->comment('問い合わせ内容');
            $table->text('admin_reply_body')->nullable()->comment('管理者返信内容');
            $table->timestamp('replied_at')->nullable()->comment('返信日時');
            $table->unsignedTinyInteger('status')->default(1)->comment('1:未対応 2:返信済み');
            $table->timestamps();

            $table->index('status');
            $table->index('created_at');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('article_inquiries');
    }
};
