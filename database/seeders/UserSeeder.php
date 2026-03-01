<?php

namespace Database\Seeders;

use App\Models\LeaveType;
use App\Models\User;
use App\Services\LeaveBalanceService;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        $balanceService = app(LeaveBalanceService::class);

        $users = [
            [
                'name'            => 'System Admin',
                'email'           => 'admin@company.com',
                'password'        => Hash::make('Admin@123'),
                'department'      => 'Management',
                'leave_join_date' => '2020-01-01',
                'is_active'       => true,
                'email_verified_at' => now(),
                'role'            => 'admin',
            ],
            [
                'name'            => 'HR Manager',
                'email'           => 'hr@company.com',
                'password'        => Hash::make('HR@1234!'),
                'department'      => 'Human Resources',
                'leave_join_date' => '2020-06-01',
                'is_active'       => true,
                'email_verified_at' => now(),
                'role'            => 'hr',
            ],
            [
                'name'            => 'Department Manager',
                'email'           => 'manager@company.com',
                'password'        => Hash::make('Manager@123'),
                'department'      => 'Engineering',
                'leave_join_date' => '2021-01-15',
                'is_active'       => true,
                'email_verified_at' => now(),
                'role'            => 'manager',
            ],
            [
                'name'            => 'John Employee',
                'email'           => 'employee@company.com',
                'password'        => Hash::make('Employee@123'),
                'department'      => 'Engineering',
                'leave_join_date' => '2022-03-01',
                'is_active'       => true,
                'email_verified_at' => now(),
                'role'            => 'employee',
            ],
            [
                'name'            => 'Jane Smith',
                'email'           => 'jane@company.com',
                'password'        => Hash::make('Jane@1234!'),
                'department'      => 'Marketing',
                'leave_join_date' => '2022-07-01',
                'is_active'       => true,
                'email_verified_at' => now(),
                'role'            => 'employee',
            ],
        ];

        foreach ($users as $userData) {
            $role = $userData['role'];
            unset($userData['role']);

            $user = User::updateOrCreate(
                ['email' => $userData['email']],
                $userData
            );

            $user->syncRoles([$role]);
            $balanceService->initializeForUser($user);
        }
    }
}
