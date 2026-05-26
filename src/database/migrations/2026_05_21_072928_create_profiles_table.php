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
        Schema::create('profiles', function (Blueprint $table) {
            $table->id()->comment('プロフィールID');
            $table->foreignId('user_id')->unique()->constrained()->cascadeOnDelete()->comment('ユーザーID');
            $table->string('display_name', 50)->comment('表示名');
            $table->string('job_type', 100)->nullable()->comment('職種');
            $table->string('prefecture', 50)->nullable()->comment('都道府県');
            $table->text('skills')->nullable()->comment('スキル');
            $table->text('bio')->nullable()->comment('自己紹介');
            $table->string('purpose', 255)->nullable()->comment('利用目的');
            $table->string('work_style', 255)->nullable()->comment('希望作業スタイル');
            $table->timestamps();

            $table->index('display_name');
            $table->index('job_type');
            $table->index('prefecture');
            $table->index('purpose');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('profiles');
    }
};
