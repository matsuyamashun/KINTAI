<?php

namespace App\Http\Controllers;

use App\Http\Requests\DetailAttendanceRequest;
use App\Models\Attendance;
use App\Models\BreakTime;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

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

    public function show(Attendance $attendance)
    {
        return view('admin.attendance_detail', compact('attendance'));
    }

    public function update(DetailAttendanceRequest $request, Attendance $attendance)
    {
        //全部成功で保存
        DB::transaction(function () use ($request, $attendance) {

            //勤務時間更新
            $attendance->update([
                'clock_in' => Carbon::createFromFormat('Y-m-d H:i', $attendance->date . ' ' . $request->clock_in),
                'clock_out' => Carbon::createFromFormat('Y-m-d H:i', $attendance->date . ' ' . $request->clock_out),
                'note' => $request->note,
            ]);

            //休憩時間更新
            if ($request->has('breaks')) {
                foreach ($request->breaks as $break) {
                    BreakTime::where('id', $break['id'])->update([
                        'start_time' => Carbon::createFromFormat('Y-m-d H:i', $attendance->date . ' ' . $break['start_time']),
                        'end_time' => Carbon::createFromFormat('Y-m-d H:i', $attendance->date . ' ' . $break['end_time']),
                    ]);
                }
            }
        });
        return redirect()->route('admin.attendance_staff', $attendance->user_id);
    }
}