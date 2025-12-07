<?php

namespace App\Http\Controllers;

use App\Models\Attendance;
use Carbon\Carbon;
use Illuminate\Http\Request;

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

        $lastDay = $date->copy()->endOfMonth()->day;

        $attendanceList = [];

        for ($i = 1; $i <= $lastDay; $i++) {
            $day = $date->copy()->day($i);

            $attendanceList[] = [
                'date' => $day,
                'attendance' => Attendance::getAttendanceByDate(auth()->id(), $day)
            ];
        }

        return view('list', compact('date', 'prevDate', 'nextDate', 'attendanceList'));
    }

    public function show(Attendance $attendance)
    {
        $pendingRequest = $attendance->getPendingRequest();

        $breaks = $attendance->breaks->map(function ($break) {
            return [
                'start_time' => $break->start_time,
                'end_time'   => $break->end_time,
            ];
        })->toArray();

        return view('detail', compact('attendance', 'pendingRequest', 'breaks'));
    }
}
