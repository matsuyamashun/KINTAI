<?php

namespace App\Http\Controllers;

use App\Models\Attendance;
use Carbon\Carbon;

class AttendanceController extends Controller
{
    public function index()
    {
        $attendance = Attendance::getTodayAttendance(auth()->id());

        $todayBreak = $attendance?->getCurrentBreak();

        $status = $attendance ? $attendance->getStatus() : Attendance::STATUS_OFF;

        $todayDate = Carbon::now()->isoFormat('Y年M月D日 (ddd) ');
        $currentTime = Carbon::now()->format('H:i');

        return view('attendance',compact('status', 'todayDate', 'currentTime'));
    }

    public function start()
    {
        Attendance::create([
            'user_id' => auth()->id(),
            'date' => today(),
            'clock_in' => now(),
        ]);

        return redirect()->route('attendance');
    }

    public function end()
    {
        $attendance = Attendance::getTodayAttendance(auth()->id());

        $attendance->update(['clock_out' => now()]);

        return redirect()->route('attendance');
    }

    public function list($year = null, $month = null)
    {
        $date = Carbon::create($year ?? now()->year, $month ?? now()->month)
                ->startOfMonth();

        $prevDate = $date->copy()->subMonth();
        $nextDate = $date->copy()->addMonth();

        $attendances = Attendance::getMonthlyAttendance(auth()->id(), $date);

        
        return view('list',compact('date', 'prevDate', 'nextDate', 'attendances'));
    }
}
