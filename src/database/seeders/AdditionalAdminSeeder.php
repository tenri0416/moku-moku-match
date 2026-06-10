<?php

namespace Database\Seeders;

use App\Models\Admin;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class AdditionalAdminSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $admins = [
            [
                'email' => 'yuki.fujimoto.free@gmail.com',
                'name' => '管理者',
            ],
            [
                'email' => 'ramen.like.tonkotu@gmail.com',
                'name' => '管理者',
            ],
        ];

        foreach ($admins as $admin) {
            Admin::updateOrCreate(
                [
                    'email' => $admin['email'],
                ],
                [
                    'name' => $admin['name'],
                    'password' => Hash::make('Password123'),
                    'status' => 1,
                ]
            );
        }
    }
}
