<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('stock_alerts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('stock_watchlist_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('stock_event_id')->nullable()->constrained()->nullOnDelete();
            $table->string('kind', 30);
            $table->string('channel', 30)->default('line');
            $table->string('dedupe_key', 191)->unique();
            $table->text('message');
            $table->string('status', 20)->default('pending');
            $table->text('error')->nullable();
            $table->timestamp('sent_at')->nullable();
            $table->timestamps();

            $table->index(['channel', 'status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('stock_alerts');
    }
};
