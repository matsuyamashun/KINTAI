<?php

namespace App\Http\Controllers;

use App\Models\Attendance;
use App\Models\BreakTime;
use Illuminate\Http\Request;


class BreakController extends Controller
{
    public function start()
    {
        $attendance =
        Attendance::where('user_id', auth()->id())
            ->whereDate('date', today())
            ->first();

        $attendance->breaks()->create([
            'start_time' => now(),
        ]);

        return redirect()->route('attendance');
    }

    public function end()
    {
        $attendance = Attendance::where('user_id', auth()->id())
                        ->whereDate('date', today())
                        ->first();

        $break = $attendance->breaks()->whereNull('end_time')->first();

        $break->update(['end_time' => now()]);

        return redirect()->route('attendance');
    }
}
