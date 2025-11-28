<?php

namespace App\Http\Controllers;

use App\Models\Attendance;
use Carbon\Carbon;
use Illuminate\Http\Request;


class AttendanceController extends Controller
{
    public function index()
    {
        $attendance =
        Attendance::where('user_id',auth()->id())
            ->whereDate('date',today())
            ->first();

        $todayBreak = null;

        if($attendance) {
            $todayBreak = $attendance->breaks()
                ->whereNull('end_time')
                ->latest()
                ->first();
        }

        if (!$attendance) {
            $status = '勤務外';
        } elseif ($attendance->clock_out) {
            $status = '退勤済';
        } elseif ($todayBreak) {
            $status ='休憩中';
        } else {
            $status ='出勤中';
        }

        $todayDate = Carbon::now()->isoFormat('Y年M月D日 (ddd) ');
        $currentTime = Carbon::now()->format('H:i');

        return view('attendance',compact('attendance','status','todayDate','currentTime'));
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
        $attendance =
        Attendance::where('user_id',auth()->id())
            ->whereDate('date',today())
            ->first();

        $attendance->update(['clock_out' => now()]);

        return redirect()->route('attendance');
    }
}
