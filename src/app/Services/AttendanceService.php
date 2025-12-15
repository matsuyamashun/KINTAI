<?php

namespace App\Services;

use App\Models\Attendance;
use Carbon\Carbon;

class AttendanceService
{
    public function getMonthlyAttendance($userId, Carbon $date)
    {
        $lastDay = $date->copy()->endOfMonth()->day;
        $attendanceList = [];

        for ($i = 1; $i <= $lastDay; $i++) {
            $day = $date->copy()->day($i);

            $attendanceList[$day->isoFormat('M/D(dd)')] =
                Attendance::getAttendanceByDate($userId, $day);
        }

        return $attendanceList;
    }
}
