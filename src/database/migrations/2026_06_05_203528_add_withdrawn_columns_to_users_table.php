<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            if (! Schema::hasColumn('users', 'withdrawn_at')) {
                $table->timestamp('withdrawn_at')->nullable()->after('status');
            }

            if (! Schema::hasColumn('users', 'withdrawal_reason')) {
                $table->text('withdrawal_reason')->nullable()->after('withdrawn_at');
            }

            if (! Schema::hasColumn('users', 'withdrawal_type')) {
                $table->string('withdrawal_type', 20)->nullable()->after('withdrawal_reason');
            }

            if (! Schema::hasColumn('users', 'withdrawn_by_admin_id')) {
                $table->foreignId('withdrawn_by_admin_id')
                    ->nullable()
                    ->after('withdrawal_type')
                    ->constrained('admins')
                    ->nullOnDelete();
            }

            if (! Schema::hasColumn('users', 'suspended_at')) {
                $table->timestamp('suspended_at')->nullable()->after('withdrawn_by_admin_id');
            }

            if (! Schema::hasColumn('users', 'suspension_reason')) {
                $table->text('suspension_reason')->nullable()->after('suspended_at');
            }

            if (! Schema::hasColumn('users', 'suspended_by_admin_id')) {
                $table->foreignId('suspended_by_admin_id')
                    ->nullable()
                    ->after('suspension_reason')
                    ->constrained('admins')
                    ->nullOnDelete();
            }
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            if (Schema::hasColumn('users', 'suspended_by_admin_id')) {
                $table->dropConstrainedForeignId('suspended_by_admin_id');
            }

            if (Schema::hasColumn('users', 'withdrawn_by_admin_id')) {
                $table->dropConstrainedForeignId('withdrawn_by_admin_id');
            }

            foreach ([
                'suspension_reason',
                'suspended_at',
                'withdrawal_type',
                'withdrawal_reason',
                'withdrawn_at',
            ] as $column) {
                if (Schema::hasColumn('users', $column)) {
                    $table->dropColumn($column);
                }
            }
        });
    }
};
