<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('admin_impersonation_logs', function (Blueprint $table) {
            $table->id();

            // admin guard 側の管理者IDを保存します。
            // 管理者テーブル名が環境によって admins / users に分かれる可能性があるため、
            // 外部キー制約は付けず、ログ用途として安全に保存します。
            $table->unsignedBigInteger('admin_id')->index();

            $table->foreignId('user_id')
                ->constrained('users')
                ->cascadeOnDelete();

            $table->timestamp('started_at')->nullable()->index();
            $table->timestamp('ended_at')->nullable();
            $table->timestamp('last_activity_at')->nullable();

            $table->string('start_ip', 45)->nullable();
            $table->string('end_ip', 45)->nullable();
            $table->text('user_agent')->nullable();

            $table->string('status', 20)->default('active')->index();

            $table->timestamps();

            $table->index(['admin_id', 'user_id', 'status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('admin_impersonation_logs');
    }
};
