<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('vocabulary_words', function (Blueprint $table) {
            $table->id();

            $table->foreignId('user_id')
                ->constrained()
                ->cascadeOnDelete();

            $table->string('word', 120);
            $table->text('meaning');
            $table->text('example_sentence');

            $table->text('memo')->nullable();
            $table->string('source', 255)->nullable();
            $table->string('category', 100)->nullable();

            $table->unsignedTinyInteger('importance')->default(3);

            $table->string('review_status', 30)->default('not_reviewed');
            $table->boolean('is_review_target')->default(true);

            $table->unsignedInteger('review_count')->default(0);
            $table->unsignedInteger('correct_count')->default(0);
            $table->timestamp('last_reviewed_at')->nullable();

            $table->timestamps();
            $table->softDeletes();

            $table->index(['user_id', 'word']);
            $table->index(['user_id', 'review_status']);
            $table->index(['user_id', 'is_review_target']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('vocabulary_words');
    }
};
