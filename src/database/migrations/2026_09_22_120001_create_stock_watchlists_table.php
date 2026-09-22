<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('stock_watchlists', function (Blueprint $table) {
            $table->id();
            $table->string('stock_code', 4)->unique();
            $table->string('company_name', 255);
            $table->decimal('purchase_price', 12, 2)->nullable();
            $table->unsignedInteger('shares')->nullable();
            $table->boolean('is_active')->default(true);
            $table->boolean('notify_news')->default(true);
            $table->timestamp('initialized_at')->nullable();
            $table->timestamp('last_checked_at')->nullable();
            $table->text('last_error')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('stock_watchlists');
    }
};
