<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Create admin user via UserSeeder
        $this->call([
            UserSeeder::class,
        ]);

        // Create some test users if they don't exist
        if (User::where('email', 'test@example.com')->doesntExist()) {
        User::factory()->create([
            'name' => 'Test User',
                'username' => 'testuser',
            'email' => 'test@example.com',
                'role' => 'user',
        ]);
        }

        // Create additional users (only if we have less than 10 users)
        if (User::count() < 10) {
            $needed = 10 - User::count();
            User::factory($needed)->create();
        }

        $this->call([
            BeneficiarySeeder::class,
            PermissionSeeder::class,
        ]);
    }
}
