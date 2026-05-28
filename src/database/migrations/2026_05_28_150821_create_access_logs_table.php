<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('access_logs', function (Blueprint $table) {
            $table->id();
             // ログインユーザー情報
            $table->foreignId('user_id')
                ->nullable()
                ->constrained()
                ->nullOnDelete()
                ->comment('アクセスしたユーザーID');

            $table->string('user_type', 20)
                ->nullable()
                ->comment('ユーザー種別 guest:user未ログイン user:一般 admin:管理者');

            // リクエスト情報
            $table->string('method', 10)
                ->comment('HTTPメソッド');

            $table->text('url')
                ->comment('アクセスURL');

            $table->text('path')
                ->comment('アクセスパス');

            $table->text('route_name')
                ->nullable()
                ->comment('ルート名');

            $table->text('referer')
                ->nullable()
                ->comment('遷移元URL');

            // アクセス元情報
            $table->string('ip_address', 45)
                ->nullable()
                ->comment('IPアドレス');

            $table->text('user_agent')
                ->nullable()
                ->comment('User-Agent');

            // レスポンス情報
            $table->unsignedSmallInteger('status_code')
                ->nullable()
                ->comment('HTTPステータスコード');

            $table->unsignedInteger('duration_ms')
                ->nullable()
                ->comment('処理時間 ミリ秒');

            // 追加情報
            $table->json('request_data')
                ->nullable()
                ->comment('リクエスト情報 パスワード等は除外');

            $table->index('user_id');
            $table->index('user_type');
            $table->index('method');
            $table->index('status_code');
            $table->index('created_at');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('access_logs');
    }
};
