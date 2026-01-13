<?php

namespace Database\Seeders;

use App\Enums\UserRole;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;

class RolePermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Reset cached roles and permissions
        app()[PermissionRegistrar::class]->forgetCachedPermissions();

        // Create permissions
        $permissions = [
            'manage users',
            'manage patients',
            'manage appointments',
            'manage encounters',
            'manage discussions',
        ];

        foreach ($permissions as $permission) {
            Permission::create(['name' => $permission]);
        }

        // Create roles and assign permissions
        $admin = Role::create(['name' => UserRole::Admin->value]);
        $admin->givePermissionTo(Permission::all());

        $manager = Role::create(['name' => UserRole::Doctor->value]);
        $manager->givePermissionTo([
            'manage patients',
            'manage appointments',
            'manage encounters',
            'manage discussions',
        ]);

        $nurse = Role::create(['name' => UserRole::Nurse->value]);
        $nurse->givePermissionTo([
            'manage patients',
            'manage encounters',
            'manage discussions',
        ]);

        $front_desk = Role::create(['name' => UserRole::FrontDesk->value]);
        $front_desk->givePermissionTo([
            'manage patients',
            'manage appointments'
        ]);
    }
}
