<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StampCorrectionRequest extends Model
{
    use HasFactory;

    protected $fillable = [
        'attendance_id',
        'user_id',
        'old_clock_in',
        'old_clock_out',
        'old_breaks',
        'new_clock_in',
        'new_clock_out',
        'new_breaks',
        'note',
        'status',
    ];

    protected $casts = [
        'old_breaks' => 'array',
        'new_breaks' => 'array',
    ];

    public function attendance()
    {
        return $this->belongsTo(Attendance::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
