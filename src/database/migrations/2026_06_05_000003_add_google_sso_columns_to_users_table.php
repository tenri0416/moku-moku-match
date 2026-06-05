<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            if (! Schema::hasColumn('users', 'contact_id')) {
                $table->string('contact_id', 12)->nullable()->comment('Contact ID');
            }

            if (! Schema::hasColumn('users', 'saleforce_id')) {
                // ご指定のカラム名どおり saleforce_id で作成します。
                $table->string('saleforce_id', 12)->nullable()->comment('Salesforce ID');
            }

            if (! Schema::hasColumn('users', 'first_name')) {
                $table->string('first_name')->nullable()->comment('姓');
            }

            if (! Schema::hasColumn('users', 'last_name')) {
                $table->string('last_name')->nullable()->comment('名');
            }

            if (! Schema::hasColumn('users', 'first_name_kana')) {
                $table->string('first_name_kana')->nullable()->comment('姓（カナ）');
            }

            if (! Schema::hasColumn('users', 'last_name_kana')) {
                $table->string('last_name_kana')->nullable()->comment('名（カナ）');
            }

            if (! Schema::hasColumn('users', 'phone_number')) {
                $table->string('phone_number')->nullable()->unique()->comment('電話番号');
            }

            if (! Schema::hasColumn('users', 'birthday')) {
                $table->date('birthday')->nullable()->comment('生年月日');
            }

            if (! Schema::hasColumn('users', 'postal_code')) {
                $table->string('postal_code', 10)->nullable()->comment('郵便番号');
            }

            if (! Schema::hasColumn('users', 'prefecture')) {
                $table->string('prefecture')->nullable()->comment('都道府県');
            }

            if (! Schema::hasColumn('users', 'city')) {
                $table->string('city')->nullable()->comment('市区町村');
            }

            if (! Schema::hasColumn('users', 'town_street_building')) {
                $table->string('town_street_building')->nullable()->comment('町域・番地・建物名・部屋番号');
            }

            if (! Schema::hasColumn('users', 'provider')) {
                $table->string('provider')->nullable()->comment('OAuthプロバイダー');
            }

            if (! Schema::hasColumn('users', 'provider_id')) {
                $table->string('provider_id')->nullable()->comment('OAuthプロバイダー側ID');
            }
        });

        $this->makePasswordNullable();
        $this->addProviderUniqueIndex();
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        $this->dropProviderUniqueIndex();

        Schema::table('users', function (Blueprint $table) {
            foreach ([
                'contact_id',
                'saleforce_id',
                'first_name',
                'last_name',
                'first_name_kana',
                'last_name_kana',
                'phone_number',
                'birthday',
                'postal_code',
                'prefecture',
                'city',
                'town_street_building',
                'provider',
                'provider_id',
            ] as $column) {
                if (Schema::hasColumn('users', $column)) {
                    $table->dropColumn($column);
                }
            }
        });

        $this->makePasswordNotNullable();
    }

    private function addProviderUniqueIndex(): void
    {
        if (! Schema::hasColumn('users', 'provider') || ! Schema::hasColumn('users', 'provider_id')) {
            return;
        }

        try {
            Schema::table('users', function (Blueprint $table) {
                $table->unique(['provider', 'provider_id'], 'users_provider_provider_id_unique');
            });
        } catch (Throwable $e) {
            // 既に同名indexが存在する場合などは、マイグレーション全体を止めない。
        }
    }

    private function dropProviderUniqueIndex(): void
    {
        try {
            Schema::table('users', function (Blueprint $table) {
                $table->dropUnique('users_provider_provider_id_unique');
            });
        } catch (Throwable $e) {
            // indexが存在しない場合は無視する。
        }
    }

    private function makePasswordNullable(): void
    {
        if (! Schema::hasColumn('users', 'password')) {
            return;
        }

        $driver = DB::connection()->getDriverName();

        if (in_array($driver, ['mysql', 'mariadb'], true)) {
            DB::statement("ALTER TABLE users MODIFY password VARCHAR(255) NULL COMMENT 'パスワード'");
            return;
        }

        Schema::table('users', function (Blueprint $table) {
            $table->string('password')->nullable()->comment('パスワード')->change();
        });
    }

    private function makePasswordNotNullable(): void
    {
        if (! Schema::hasColumn('users', 'password')) {
            return;
        }

        DB::table('users')
            ->whereNull('password')
            ->update([
                'password' => Hash::make(Str::random(40)),
            ]);

        $driver = DB::connection()->getDriverName();

        if (in_array($driver, ['mysql', 'mariadb'], true)) {
            DB::statement("ALTER TABLE users MODIFY password VARCHAR(255) NOT NULL COMMENT 'パスワード'");
            return;
        }

        Schema::table('users', function (Blueprint $table) {
            $table->string('password')->nullable(false)->comment('パスワード')->change();
        });
    }
};
