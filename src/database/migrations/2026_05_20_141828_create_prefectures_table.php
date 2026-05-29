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
        Schema::create('prefectures', function (Blueprint $table) {
            $table->id();
            // URLに使用する英字表記
            // 例：nara, akita
            $table->string('slug')->unique()->comment('URL用都道府県名');

            // 画面に表示する都道府県名
            // 例：奈良県、秋田県
            $table->string('name')->unique()->comment('都道府県名');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('prefectures');
    }
};
