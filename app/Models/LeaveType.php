<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class LeaveType extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'name',
        'max_days',
        'paid',
        'carry_forward',
        'requires_attachment',
    ];

    protected function casts(): array
    {
        return [
            'paid'                => 'boolean',
            'carry_forward'       => 'boolean',
            'requires_attachment' => 'boolean',
            'max_days'            => 'integer',
        ];
    }

    public function leaveBalances(): HasMany
    {
        return $this->hasMany(LeaveBalance::class);
    }

    public function leaveRequests(): HasMany
    {
        return $this->hasMany(LeaveRequest::class);
    }
}
