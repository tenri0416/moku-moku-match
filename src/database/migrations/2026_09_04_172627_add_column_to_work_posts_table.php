<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('work_posts', function (Blueprint $table) {
            $table->string('meeting_url')->comment('オンラインでの開催の場合のURL')->nullable()->after('location_type');
            $table->string('purpose', 50)->nullable()->change();
            $table->string('location_type', 20)
                ->default('online')
                ->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('work_posts', function (Blueprint $table) {
            $table->dropColumn('meeting_url');
            $table->string('purpose', 50)->nullable(false)->change();
            $table->string('location_type', 20)
                ->default('online')
                ->nullable(false)->change();
        });
    }
};
