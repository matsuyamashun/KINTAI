<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Services\AttendanceCsvService;
use App\Services\AttendanceService;
use Carbon\Carbon;

class AdminStaffController extends Controller
{
    private AttendanceService $attendanceService;

    public function __construct(AttendanceService $attendanceService)
    {
        $this->attendanceService = $attendanceService;
    }

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

        $attendanceList = $this->attendanceService
            ->getMonthlyAttendance($user->id, $date);

        return view('admin.attendance_staff', compact('user', 'date', 'prevDate', 'nextDate', 'attendanceList'));
    }


    public function exportCsv(User $user, $year, $month, AttendanceCsvService $csvService)
    {
        $attendances = $csvService->getMonthlyAttendances($user, $year, $month);
        $fileName = "{$user->name}_{$year}_{$month}_attendance.csv";

        return response()->streamDownload(function () use ($attendances, $csvService) {
            $handle = fopen('php://output', 'write');

            // ヘッダ-
            fputcsv($handle, array_map(
                fn($value) => mb_convert_encoding($value, 'SJIS-win', 'UTF-8'),
                $csvService->headers()
            ));

            foreach ($attendances as $attendance) {
                fputcsv($handle, array_map(
                    fn($value) => mb_convert_encoding($value, 'SJIS-win', 'UTF-8'),
                    $csvService->row($attendance)
                ));
            }

            fclose($handle);
        }, $fileName, [
            'Content-Type' => 'text/csv; charset=SJIS-win',
        ]);
    }
}
