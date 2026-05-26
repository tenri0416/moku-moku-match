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
        Schema::create('applications', function (Blueprint $table) {
            $table->id()->comment('申請ID');
            $table->foreignId('work_post_id')->constrained()->cascadeOnDelete()->comment('募集ID');
            $table->foreignId('user_id')->constrained()->cascadeOnDelete()->comment('申請者ユーザーID');
            $table->text('message')->nullable()->comment('申請メッセージ');
            $table->unsignedTinyInteger('status')->default(1)->comment('申請状態 1:承認待ち 2:承認済み 3:否認');
            $table->timestamps();

            $table->unique(['work_post_id', 'user_id']);
            $table->index('work_post_id');
            $table->index('user_id');
            $table->index('status');
            $table->index('created_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('applications');
    }
};
