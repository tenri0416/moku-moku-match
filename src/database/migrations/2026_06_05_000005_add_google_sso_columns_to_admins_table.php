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
        if (! Schema::hasTable('admins')) {
            return;
        }

        if (! Schema::hasColumn('admins', 'provider')) {
            Schema::table('admins', function (Blueprint $table) {
                $column = $table->string('provider')
                    ->nullable()
                    ->comment('OAuthプロバイダー');

                if (Schema::hasColumn('admins', 'password')) {
                    $column->after('password');
                }
            });
        }

        if (! Schema::hasColumn('admins', 'provider_id')) {
            Schema::table('admins', function (Blueprint $table) {
                $column = $table->string('provider_id')
                    ->nullable()
                    ->comment('OAuthプロバイダーID');

                if (Schema::hasColumn('admins', 'provider')) {
                    $column->after('provider');
                }
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (! Schema::hasTable('admins')) {
            return;
        }

        Schema::table('admins', function (Blueprint $table) {
            if (Schema::hasColumn('admins', 'provider_id')) {
                $table->dropColumn('provider_id');
            }

            if (Schema::hasColumn('admins', 'provider')) {
                $table->dropColumn('provider');
            }
        });
    }
};
