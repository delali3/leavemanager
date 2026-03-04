<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;

class RolePermissionSeeder extends Seeder
{
    public function run(): void
    {
        // Reset cached roles and permissions
        app()[PermissionRegistrar::class]->forgetCachedPermissions();

        // Define permissions
        $permissions = [
            // Leave Types
            'view leave types',
            'create leave types',
            'edit leave types',
            'delete leave types',

            // Leave Requests
            'view own leave requests',
            'view all leave requests',
            'create leave requests',
            'approve leave requests',
            'reject leave requests',
            'delete leave requests',

            // Users
            'view users',
            'create users',
            'edit users',
            'delete users',

            // Reports
            'view reports',
        ];

        foreach ($permissions as $permission) {
            Permission::firstOrCreate(['name' => $permission]);
        }

        // Admin — all permissions (also covered by Gate::before in AppServiceProvider)
        $admin = Role::firstOrCreate(['name' => 'admin']);
        $admin->syncPermissions(Permission::all());

        // HR
        $hr = Role::firstOrCreate(['name' => 'hr']);
        $hr->syncPermissions([
            'view leave types',
            'create leave types',
            'edit leave types',
            'delete leave types',
            'view all leave requests',
            'create leave requests',
            'approve leave requests',
            'reject leave requests',
            'delete leave requests',
            'view users',
            'create users',
            'edit users',
            'view reports',
        ]);

        // Manager — can view and create requests (including on behalf of employees), but cannot approve/reject
        $manager = Role::firstOrCreate(['name' => 'manager']);
        $manager->syncPermissions([
            'view leave types',
            'view all leave requests',
            'create leave requests',
            'view own leave requests',
            'view reports',
        ]);

        // Employee
        $employee = Role::firstOrCreate(['name' => 'employee']);
        $employee->syncPermissions([
            'view leave types',
            'create leave requests',
            'view own leave requests',
        ]);
    }
}
