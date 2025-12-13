<?php

namespace App\Http\Controllers;

use App\Models\Attendance;
use Carbon\Carbon;

class AdminAttendanceController extends Controller
{
    public function list($targetDate = null)
    {
        $date = $targetDate ? Carbon::parse($targetDate) : Carbon::today();

        $attendances = Attendance::getAllByDate($date);

        //前日.翌日
        $yesterday = $date->copy()->subDay()->format('Y-m-d');
        $tomorrow = $date->copy()->addDay()->format('Y-m-d');

        return view('admin.attendance_list', compact('date', 'attendances', 'yesterday', 'tomorrow'));
    }

    public function show()
    {
        return view('admin.attendance_detail');
    }
}