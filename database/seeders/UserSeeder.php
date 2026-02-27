<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        if (User::where('username', 'admin')->doesntExist()) {
            User::create([
                'name' => 'مدير النظام',
                'username' => 'admin',
                'email' => 'admin@goodaid.com',
                'password' => Hash::make('admin123'),
                'role' => 'admin',
            ]);
        }
    }
}
