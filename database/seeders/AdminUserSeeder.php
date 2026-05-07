<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class AdminUserSeeder extends Seeder
{
    public function run(): void
    {
        User::firstOrCreate(
            ['email' => 'info@arsus.nl'],
            [
                'name'     => 'ARSUS',
                'password' => Hash::make('arsus@29'),
                'role'     => 'admin',
            ]
        );
    }
}
