<?php

namespace Database\Seeders;

use App\SeederHelper;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class RoleAndPermissionSeeder extends Seeder
{
    use SeederHelper;

    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Reset cached roles and permissions
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        // Create permissions
        $permissions = [
            'view units',
            'create units',
            'edit units',
            'delete units',
            'view reservations',
            'create reservations',
            'edit reservations',
            'delete reservations',
            'approve reservations',
            'reject reservations',
            'view users',
            'create users',
            'edit users',
            'delete users',
            'view reviews',
            'approve reviews',
            'delete reviews',
        ];

        $createdPermissions = [];
        foreach ($permissions as $permission) {
            $createdPermissions[] = $this->createIfNotExists(Permission::class, ['name' => $permission], ['name' => $permission]);
        }

        // Create roles and assign permissions
        $adminRole = $this->createIfNotExists(Role::class, ['name' => 'admin'], ['name' => 'admin']);
        $adminRole->syncPermissions($createdPermissions);

        $userRole = $this->createIfNotExists(Role::class, ['name' => 'user'], ['name' => 'user']);
        $userPermissions = Permission::whereIn('name', [
            'view units',
            'view reservations',
            'create reservations',
            'edit reservations',
            'delete reservations',
            'view reviews',
        ])->get();
        $userRole->syncPermissions($userPermissions);
    }
}
