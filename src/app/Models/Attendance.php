<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Attendance extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'date',
        'clock_in',
        'clock_out',
    ];

    //定数
    const STATUS_OFF = '勤務外';
    const STATUS_WORKING = '出勤中';
    const STATUS_BREAK = '休憩中';
    const STATUS_DONE = '退勤済';

    public function getStatus()
    {
        if (!$this->clock_in) {
            return self::STATUS_OFF;
        }

        if ($this->clock_out) {
            return self::STATUS_DONE;
        }

        if ($this->getCurrentBreak()) {
            return self::STATUS_BREAK;
        }

        return self::STATUS_WORKING;
    }

    public function getCurrentBreak()
    {
        return $this->breaks()
            ->whereNull('end_time')
            ->latest()
            ->first();
    }

    public static function getTodayAttendance($userId)
    {
        return self::where('user_id', $userId)
            ->whereDate('date', today())
            ->first();
    }

    //リレーション
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function breaks()
    {
        return $this->hasMany(BreakTime::class);
    }
}
