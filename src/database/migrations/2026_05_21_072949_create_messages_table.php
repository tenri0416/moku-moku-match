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
        Schema::create('messages', function (Blueprint $table) {
            $table->foreignId('work_post_id')->constrained()->cascadeOnDelete()->comment('募集ID');
            $table->foreignId('sender_id')->constrained('users')->cascadeOnDelete()->comment('送信者ユーザーID');
            $table->foreignId('receiver_id')->constrained('users')->cascadeOnDelete()->comment('受信者ユーザーID');
            $table->text('body')->comment('メッセージ本文');
            $table->dateTime('read_at')->nullable()->comment('既読日時');
            $table->timestamps();

            $table->index('work_post_id');
            $table->index('sender_id');
            $table->index('receiver_id');
            $table->index('read_at');
            $table->index('created_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('messages');
    }
};
