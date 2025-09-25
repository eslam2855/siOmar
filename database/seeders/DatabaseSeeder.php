<?php

namespace Database\Seeders;

use App\Models\User;
use App\SeederHelper;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    use SeederHelper;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Seed roles and permissions first
        $this->call([
            RoleAndPermissionSeeder::class,
            UnitTypeSeeder::class,
            AmenitySeeder::class,
            SliderSeeder::class,
            ReviewSeeder::class,
            UnitMonthPriceSeeder::class,
            CancellationPolicySeeder::class,
        ]);

        // Create admin user
        $admin = $this->createIfNotExists(
            User::class,
            ['email' => 'admin@example.com'],
            [
                'name' => 'Admin User',
                'password' => Hash::make('password'),
            ]
        );
        
        // Assign admin role if not already assigned
        if (!$admin->hasRole('admin')) {
            $admin->assignRole('admin');
        }

        // Create regular user
        $user = $this->createIfNotExists(
            User::class,
            ['email' => 'test@example.com'],
            [
                'name' => 'Test User',
                'password' => Hash::make('password'),
            ]
        );
        
        // Assign user role if not already assigned
        if (!$user->hasRole('user')) {
            $user->assignRole('user');
        }
    }
}
