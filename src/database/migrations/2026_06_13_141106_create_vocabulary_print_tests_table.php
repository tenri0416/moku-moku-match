<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('vocabulary_print_tests', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();

            $table->string('title')->default('ボキャブラリー印刷テスト');
            $table->unsignedInteger('question_count');
            $table->unsignedInteger('time_limit_minutes');
            $table->string('target_filter')->default('review_target');
            $table->string('category')->nullable();
            $table->unsignedTinyInteger('importance')->nullable();
            $table->json('question_types_json');
            $table->unsignedInteger('total_score')->default(100);

            $table->string('status')->default('completed');
            $table->text('error_message')->nullable();
            $table->timestamp('generated_at')->nullable();

            $table->timestamps();

            $table->index(['user_id', 'created_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('vocabulary_print_tests');
    }
};
