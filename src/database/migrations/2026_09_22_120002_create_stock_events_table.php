<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('stock_events', function (Blueprint $table) {
            $table->id();
            $table->foreignId('stock_watchlist_id')->constrained()->cascadeOnDelete();
            $table->string('source', 100);
            $table->string('source_id', 255)->nullable();
            $table->string('event_type', 50)->default('news');
            $table->string('title', 1000);
            $table->text('summary_text')->nullable();
            $table->text('source_url')->nullable();
            $table->timestamp('published_at')->nullable();
            $table->char('content_hash', 64);
            $table->json('raw_payload')->nullable();
            $table->timestamps();

            $table->unique(['stock_watchlist_id', 'content_hash'], 'stock_event_watchlist_hash_unique');
            $table->index(['stock_watchlist_id', 'published_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('stock_events');
    }
};
