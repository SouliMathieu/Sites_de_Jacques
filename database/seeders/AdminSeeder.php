<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class AdminSeeder extends Seeder
{
    public function run()
    {
        User::firstOrCreate(
            ['email' => 'jacqueskientega34@gmail.com'],
            [
                'name' => 'Admin Jackson Energy',
                'password' => Hash::make('Admin123!'),
                'role' => 'admin'
            ]
        );
    }
}
