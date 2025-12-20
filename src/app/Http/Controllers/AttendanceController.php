<?php

namespace App\Http\Controllers;

use App\Models\Attendance;
use App\Services\AttendanceService;
use Carbon\Carbon;

class AttendanceController extends Controller
{
    private AttendanceService $attendanceService;

    public function __construct(AttendanceService $attendanceService)
    {
        $this->attendanceService = $attendanceService;
    }

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

        $attendanceList = $this->attendanceService->getMonthlyAttendance(auth()->id(), $date);

        return view('list', compact('date', 'prevDate', 'nextDate', 'attendanceList'));
    }

    public function show(Attendance $attendance)
    {
        $pendingRequest = $attendance->getPendingRequest();

        // 承認待ち申請がある
        $isLocked = isset($pendingRequest);

        // 出勤・退勤の表示値
        $clockIn = $isLocked
            ? Carbon::parse($pendingRequest->new_clock_in)->format('H:i')
            : Carbon::parse($attendance->clock_in)->format('H:i');

        $clockOut = $isLocked && $pendingRequest->new_clock_out
            ? Carbon::parse($pendingRequest->new_clock_out)->format('H:i')
            : ($attendance->clock_out ? Carbon::parse($attendance->clock_out)->format('H:i') : '');

        if ($isLocked) {
            $breaks = $pendingRequest->new_breaks ?? [];
        } else {
            $breaks = $attendance->breaks->map(fn($b) => [
                'start_time' => $b->start_time ? Carbon::parse($b->start_time)->format('H:i') : '',
                'end_time' => $b->start_time ? Carbon::parse($b->end_time)->format('H:i') : '',
            ])->toArray();
        }

        if (!is_array($breaks)) {
            $breaks = [];
        }

        if (count($breaks) === 0) {
            $breaks[] = ['start_time' => '', 'end_time' => ''];
        }

        return view('detail', compact('attendance', 'pendingRequest', 'breaks', 'isLocked', 'clockIn', 'clockOut'));
    }
}
