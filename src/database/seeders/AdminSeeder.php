<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Admin;
use Illuminate\Support\Facades\Hash;


class AdminSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Admin::updateOrCreate(
            [
                'email' => 'tenriyu0416@gmail.com',
            ],
            [
                'name' => '管理者',
                'password' => Hash::make('Password123'),
                'status' => 1,
            ]
        );

    }
}
