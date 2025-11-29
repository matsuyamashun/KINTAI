<?php

namespace App\Http\Controllers;

use App\Models\Attendance;

class BreakController extends Controller
{
    public function start()
    {
        $attendance = Attendance::getTodayAttendance(auth()->id());

        $attendance->breaks()->create([
            'start_time' => now(),
        ]);

        return redirect()->route('attendance');
    }

    public function end()
    {
        $attendance = Attendance::getTodayAttendance(auth()->id());

        $break = $attendance->getCurrentBreak();

        $break->update(['end_time' => now()]);

        return redirect()->route('attendance');
    }
}
