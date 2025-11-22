<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class WorkSchedule extends Model
{
    protected $fillable = [
        'branch_id',
        'position_id',
        'name',
        'check_in_time',
        'break_start',
        'break_end',
        'check_out_time',
        'late_tolerance',
        'working_days',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'working_days' => 'array',
    ];

    public function branch(): BelongsTo
    {
        return $this->belongsTo(Branch::class);
    }

    public function position(): BelongsTo
    {
        return $this->belongsTo(Position::class);
    }
}
