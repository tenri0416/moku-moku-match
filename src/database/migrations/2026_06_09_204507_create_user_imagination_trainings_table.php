<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('user_imagination_trainings', function (Blueprint $table) {
            $table->id();

            $table->foreignId('user_id')
                ->constrained()
                ->cascadeOnDelete();

            $table->date('training_date');

            $table->string('question_title')->default('想像力トレーニング');
            $table->string('question_type', 50)->default('状況想像型');
            $table->string('difficulty_label', 20)->default('初級');
            $table->text('question_body');
            $table->string('normalized_question_key', 255)->index();

            $table->text('model_answer')->nullable();
            $table->text('alternative_answer')->nullable();
            $table->string('answer_point', 255)->nullable();

            $table->text('answer_body')->nullable();

            $table->unsignedTinyInteger('total_score')->nullable();
            $table->unsignedTinyInteger('imagination_score')->nullable();
            $table->unsignedTinyInteger('reason_score')->nullable();
            $table->unsignedTinyInteger('perspective_score')->nullable();
            $table->unsignedTinyInteger('expression_score')->nullable();

            $table->text('good_point')->nullable();
            $table->text('improvement_point')->nullable();
            $table->text('next_task')->nullable();

            $table->unsignedTinyInteger('earned_points')->default(0);

            $table->string('ai_provider', 50)->nullable();
            $table->string('ai_model', 100)->nullable();
            $table->string('ai_status', 50)->nullable();
            $table->text('ai_error_message')->nullable();
            $table->boolean('is_fallback')->default(false);
            $table->unsignedTinyInteger('ai_attempts')->default(0);

            $table->timestamps();

            $table->unique(['user_id', 'training_date'], 'user_imagination_trainings_user_date_unique');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('user_imagination_trainings');
    }
};
