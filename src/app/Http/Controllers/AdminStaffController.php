<?php

namespace App\Http\Controllers;

use App\Models\Attendance;
use App\Models\User;
use App\Services\AttendanceCsvService;
use Carbon\Carbon;

class AdminStaffController extends Controller
{
    public function index()
    {
        $users = User::select('id', 'name', 'email')->get();

        return view('admin.staff_list', compact('users'));
    }

    public function show(User $user, $year = null, $month = null)
    {
        $date = Carbon::create($year ?? now()->year, $month ?? now()->month)
            ->startOfMonth();

        $prevDate = $date->copy()->subMonth();
        $nextDate = $date->copy()->addMonth();

        $lastDay = $date->copy()->endOfMonth()->day;

        $attendanceList = [];

        for ($i = 1; $i <= $lastDay; $i++) {
            $day = $date->copy()->day($i);

            $attendanceList[$day->isoFormat('M/D(dd)')] =
                Attendance::getAttendanceByDate($user->id, $day);
        }

        return view('admin.attendance_staff', compact('user', 'date', 'prevDate', 'nextDate', 'attendanceList'));
    }


    public function exportCsv(
        User $user,
        int $year,
        int $month,
        AttendanceCsvService $csvService
    ) {
        $attendances = $csvService->getMonthlyAttendances($user, $year, $month);
        $fileName = "{$user->name}_{$year}_{$month}_attendance.csv";

        return response()->streamDownload(function () use ($attendances, $csvService) {
            $handle = fopen('php://output', 'w');

            // ヘッダ-
            fputcsv($handle, array_map(
                fn($v) => mb_convert_encoding($v, 'SJIS-win', 'UTF-8'),
                $csvService->headers()
            ));

            foreach ($attendances as $attendance) {
                fputcsv($handle, array_map(
                    fn($v) => mb_convert_encoding($v, 'SJIS-win', 'UTF-8'),
                    $csvService->row($attendance)
                ));
            }

            fclose($handle);
        }, $fileName, [
            'Content-Type' => 'text/csv; charset=SJIS-win',
        ]);
    }
}
