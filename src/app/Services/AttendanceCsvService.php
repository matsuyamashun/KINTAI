<?php

namespace App\Services;

use App\Models\Attendance;
use App\Models\User;
use Carbon\Carbon;

class AttendanceCsvService
{
    public function getMonthlyAttendances(User $user, $year, $month)
    {
        $start = Carbon::create($year, $month)->startOfMonth();
        $end   = $start->copy()->endOfMonth();

        return Attendance::where('user_id', $user->id)
            ->whereBetween('date', [$start, $end])
            ->orderBy('date')
            ->get();
    }

    public function headers()
    {
        return [
            '日付',
            '出勤',
            '退勤',
            '休憩時間',
            '勤務時間',
        ];
    }

    public function row(Attendance $attendance)
    {
        return [
            $attendance->date,
            $attendance->clock_in ? \Carbon\Carbon::parse($attendance->clock_in)->format('H:i') : '',
            $attendance->clock_out ? \Carbon\Carbon::parse($attendance->clock_out)->format('H:i') : '',
            $attendance->totalBreakTime(),
            $attendance->workingHours(),
        ];
    }
}