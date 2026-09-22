<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('stock_ai_analyses', function (Blueprint $table) {
            $table->id();
            $table->foreignId('stock_event_id')->unique()->constrained()->cascadeOnDelete();
            $table->string('provider', 50);
            $table->string('model', 100)->nullable();
            $table->string('impact', 20)->default('uncertain');
            $table->smallInteger('impact_score')->default(0);
            $table->unsignedTinyInteger('confidence')->default(0);
            $table->string('importance', 20)->default('low');
            $table->string('classified_event_type', 50)->default('other');
            $table->boolean('should_alert')->default(false);
            $table->text('summary');
            $table->text('reason');
            $table->text('risk')->nullable();
            $table->json('raw_json')->nullable();
            $table->timestamp('analyzed_at');
            $table->timestamps();

            $table->index(['importance', 'analyzed_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('stock_ai_analyses');
    }
};
