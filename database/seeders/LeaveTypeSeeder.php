<?php

namespace Database\Seeders;

use App\Models\LeaveType;
use Illuminate\Database\Seeder;

class LeaveTypeSeeder extends Seeder
{
    public function run(): void
    {
        $types = [
            [
                'name'                => 'Annual Leave',
                'max_days'            => 21,
                'paid'                => true,
                'carry_forward'       => true,
                'requires_attachment' => false,
            ],
            [
                'name'                => 'Sick Leave',
                'max_days'            => 14,
                'paid'                => true,
                'carry_forward'       => false,
                'requires_attachment' => true,
            ],
            [
                'name'                => 'Maternity Leave',
                'max_days'            => 84,
                'paid'                => true,
                'carry_forward'       => false,
                'requires_attachment' => true,
            ],
            [
                'name'                => 'Paternity Leave',
                'max_days'            => 5,
                'paid'                => true,
                'carry_forward'       => false,
                'requires_attachment' => false,
            ],
            [
                'name'                => 'Emergency Leave',
                'max_days'            => 3,
                'paid'                => true,
                'carry_forward'       => false,
                'requires_attachment' => false,
            ],
            [
                'name'                => 'Unpaid Leave',
                'max_days'            => 30,
                'paid'                => false,
                'carry_forward'       => false,
                'requires_attachment' => false,
            ],
        ];

        foreach ($types as $type) {
            LeaveType::firstOrCreate(['name' => $type['name']], $type);
        }
    }
}
