<?php

namespace App\Models;

use Carbon\Carbon;
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
        'note',
    ];

    //定数
    const STATUS_OFF = '勤務外';
    const STATUS_WORKING = '出勤中';
    const STATUS_BREAK = '休憩中';
    const STATUS_DONE = '退勤済';

        //リレーション
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function breaks()
    {
        return $this->hasMany(BreakTime::class);
    }

    public function stampCorrectionRequests()
    {
        return $this->hasMany(StampCorrectionRequest::class);
    }

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

    public static function getAttendanceByDate($userId, $date)
    {
        return self::where('user_id', $userId)
            ->whereDate('date', $date)
            ->first();
    }

    public static function getTodayAttendance($userId)
    {
        return self::getAttendanceByDate($userId, today());
    }

    public static function getMonthlyAttendance($userId, Carbon $date)
    {
        return self::where('user_id', $userId)
            ->whereYear('date', $date->year)
            ->whereMonth('date', $date->month)
            ->orderBy('date')
            ->get();
    }

    public function totalBreakTime()
    {
        $minutes = $this->breaks->sum(function ($break) {
            if (!$break->end_time) return 0;

            return Carbon::parse($break->start_time)
                ->diffInMinutes(Carbon::parse($break->end_time));
        });

        return $this->formatToTime($minutes); 
    }

    public function workingHours()
    {
        if (!$this->clock_in || !$this->clock_out) {
            return null;
        }

        $workMinutes = Carbon::parse($this->clock_in)
            ->diffInMinutes(Carbon::parse($this->clock_out));

        $breakMinutes = $this->totalBreakTimeRaw(); 
        $minutes = $workMinutes - $breakMinutes;

        return $this->formatToTime($minutes); 
    }

    private function formatToTime($minutes)
    {
        if (is_null($minutes)) {
        return;
    }

        return sprintf('%02d:%02d', floor($minutes / 60), $minutes % 60);
    }

    private function totalBreakTimeRaw()
    {
        return $this->breaks->sum(function ($break) {
            if (!$break->end_time) return 0;

            return Carbon::parse($break->start_time)
                ->diffInMinutes(Carbon::parse($break->end_time));
        });
    }

        public function getPendingRequest()
    {
        return $this->stampCorrectionRequests()
            ->where('status', 'pending')
            ->latest()
            ->first();
    }
}