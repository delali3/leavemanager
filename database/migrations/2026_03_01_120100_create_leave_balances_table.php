<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('leave_balances', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('leave_type_id')->constrained()->cascadeOnDelete();
            $table->unsignedInteger('total_days')->default(0);
            $table->unsignedInteger('used_days')->default(0);
            $table->unsignedInteger('remaining_days')->default(0);
            $table->year('year');
            $table->timestamps();
            $table->unique(['user_id', 'leave_type_id', 'year'], 'leave_balances_unique');
            $table->index(['user_id', 'year']);
            $table->index('leave_type_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('leave_balances');
    }
};
