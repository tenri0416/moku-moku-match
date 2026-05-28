<?php

use App\Models\Admin;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * 管理者ログイン認証コードテーブルを作成する
     */
    public function up(): void
    {
        Schema::create('admin_login_codes', function (Blueprint $table) {
            $table->id();

            $table->foreignIdFor(Admin::class)
                ->constrained()
                ->cascadeOnDelete()
                ->comment('管理者ID');

            $table->string('code_hash')
                ->comment('6桁認証コードのハッシュ値');

            $table->timestamp('expires_at')
                ->comment('有効期限');

            $table->timestamp('used_at')
                ->nullable()
                ->comment('使用日時');

            $table->timestamps();

            $table->index('admin_id');
            $table->index('expires_at');
        });
    }

    /**
     * 管理者ログイン認証コードテーブルを削除する
     */
    public function down(): void
    {
        Schema::dropIfExists('admin_login_codes');
    }
};
