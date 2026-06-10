<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('vocabulary_reviews', function (Blueprint $table) {
            $table->id();

            $table->foreignId('user_id')
                ->constrained()
                ->cascadeOnDelete();

            $table->foreignId('vocabulary_word_id')
                ->constrained('vocabulary_words')
                ->cascadeOnDelete();

            $table->string('question_type', 50);
            $table->text('question_body');
            $table->text('answer_body');

            $table->unsignedTinyInteger('total_score')->nullable();
            $table->unsignedTinyInteger('meaning_score')->nullable();
            $table->unsignedTinyInteger('explanation_score')->nullable();
            $table->unsignedTinyInteger('usage_score')->nullable();
            $table->unsignedTinyInteger('retention_score')->nullable();

            $table->text('good_point')->nullable();
            $table->text('improvement_point')->nullable();
            $table->text('correct_meaning')->nullable();
            $table->text('next_task')->nullable();

            $table->unsignedTinyInteger('earned_points')->default(0);

            $table->string('ai_provider', 50)->nullable();
            $table->string('ai_model', 100)->nullable();
            $table->string('ai_status', 50)->nullable();
            $table->text('ai_error_message')->nullable();
            $table->boolean('is_fallback')->default(false);
            $table->unsignedTinyInteger('ai_attempts')->default(0);

            $table->timestamp('reviewed_at')->nullable();

            $table->timestamps();

            $table->index(['user_id', 'reviewed_at']);
            $table->index(['user_id', 'vocabulary_word_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('vocabulary_reviews');
    }
};
