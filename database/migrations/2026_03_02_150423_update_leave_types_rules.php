<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('leave_types', function (Blueprint $table) {
            $table->boolean('hr_only')->default(false)->after('requires_attachment');
        });

        // Annual Leave: 21 → 15 days
        DB::table('leave_types')
            ->where('name', 'Annual Leave')
            ->update(['max_days' => 15]);

        // Paternity Leave: soft-delete
        DB::table('leave_types')
            ->where('name', 'Paternity Leave')
            ->update(['deleted_at' => now()]);

        // Unpaid Leave: HR-only
        DB::table('leave_types')
            ->where('name', 'Unpaid Leave')
            ->update(['hr_only' => true]);
    }

    public function down(): void
    {
        DB::table('leave_types')
            ->where('name', 'Annual Leave')
            ->update(['max_days' => 21]);

        DB::table('leave_types')
            ->where('name', 'Paternity Leave')
            ->update(['deleted_at' => null]);

        DB::table('leave_types')
            ->where('name', 'Unpaid Leave')
            ->update(['hr_only' => false]);

        Schema::table('leave_types', function (Blueprint $table) {
            $table->dropColumn('hr_only');
        });
    }
};
