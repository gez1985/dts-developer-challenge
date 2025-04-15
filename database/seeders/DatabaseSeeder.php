<?php

namespace Database\Seeders;

use App\Models\Task;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call(RoleSeeder::class);

        $superAdmin = User::factory()->create([
            'name' => 'Super Admin',
            'email' => 'super@super.com',
            'password' => Hash::make('Admin123'),
        ]);

        // Assign the admin role using Spatie
        $superAdmin->assignRole('super-admin');

        $admin = User::factory()->create([
            'name' => 'Admin',
            'email' => 'admin@admin.com',
            'password' => Hash::make('Admin123'),
        ]);

        // Assign the admin role using Spatie
        $admin->assignRole('admin');

        $users = User::factory(5)->create();
        foreach ($users as $user) {
            $user->assignRole('user');  // Assign 'user' role to each created user
        }

        Task::factory(50)->create();
    }
}
