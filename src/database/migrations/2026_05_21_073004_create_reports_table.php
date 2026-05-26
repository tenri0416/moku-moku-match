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
        Schema::create('reports', function (Blueprint $table) {
            $table->id()->comment('通報ID');
            $table->foreignId('reporter_id')->constrained('users')->cascadeOnDelete()->comment('通報者ユーザーID');
            $table->foreignId('reported_user_id')->nullable()->constrained('users')->nullOnDelete()->comment('通報対象ユーザーID');
            $table->foreignId('work_post_id')->nullable()->constrained()->nullOnDelete()->comment('通報対象募集ID');
            $table->string('reason', 255)->comment('通報理由');
            $table->text('body')->nullable()->comment('詳細内容');
            $table->unsignedTinyInteger('status')->default(1)->comment('対応状態 1:未対応 2:対応中 3:対応済み');
            $table->timestamps();

            $table->index('reporter_id');
            $table->index('reported_user_id');
            $table->index('work_post_id');
            $table->index('status');
            $table->index('created_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('reports');
    }
};
