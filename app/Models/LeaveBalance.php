<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class LeaveBalance extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'leave_type_id',
        'total_days',
        'used_days',
        'remaining_days',
        'year',
    ];

    protected function casts(): array
    {
        return [
            'total_days'     => 'integer',
            'used_days'      => 'integer',
            'remaining_days' => 'integer',
            'year'           => 'integer',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function leaveType(): BelongsTo
    {
        return $this->belongsTo(LeaveType::class);
    }

    public function syncRemaining(): void
    {
        $this->remaining_days = max(0, $this->total_days - $this->used_days);
        $this->save();
    }

    public function hasEnoughBalance(int $days): bool
    {
        return $this->remaining_days >= $days;
    }
}
