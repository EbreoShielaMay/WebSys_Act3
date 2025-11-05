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
        // User::factory(10)->create();

        // Create a single admin user. Credentials are taken from environment variables.
        // Defaults: ADMIN_NAME=Administrator, ADMIN_EMAIL=admin@example.com, ADMIN_PASSWORD=password
        $adminEmail = env('ADMIN_EMAIL', 'admin@example.com');
        $adminName = env('ADMIN_NAME', 'Administrator');
        $adminPassword = env('ADMIN_PASSWORD', 'password');

        // Only create the admin if it doesn't already exist
        if (! User::where('email', $adminEmail)->exists()) {
            User::factory()->create([
                'name' => $adminName,
                'email' => $adminEmail,
                'password' => \Illuminate\Support\Facades\Hash::make($adminPassword),
            ]);
        }
    }
}
