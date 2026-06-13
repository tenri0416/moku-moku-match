<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('vocabulary_print_test_questions', function (Blueprint $table) {
            $table->id();

            $table->foreignId('vocabulary_print_test_id')
                ->constrained()
                ->cascadeOnDelete();

            $table->foreignId('vocabulary_word_id')
                ->nullable()
                ->constrained()
                ->nullOnDelete();

            $table->unsignedInteger('question_number');
            $table->string('question_type');
            $table->text('question_body');
            $table->unsignedInteger('point')->default(10);

            $table->text('answer_text')->nullable();
            $table->text('explanation_text')->nullable();
            $table->json('choices_json')->nullable();
            $table->string('correct_choice')->nullable();
            $table->json('scoring_rule_json')->nullable();

            $table->timestamps();

            $table->unique(['vocabulary_print_test_id', 'question_number'], 'vocab_print_test_question_no_unique');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('vocabulary_print_test_questions');
    }
};
