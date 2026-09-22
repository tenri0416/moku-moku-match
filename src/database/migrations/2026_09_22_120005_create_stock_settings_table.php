<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('stock_settings', function (Blueprint $table) {
            $table->id();
            $table->string('line_user_id', 100)->nullable();
            $table->boolean('line_enabled')->default(false);
            $table->boolean('immediate_alert_enabled')->default(true);
            $table->boolean('morning_summary_enabled')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('stock_settings');
    }
};
