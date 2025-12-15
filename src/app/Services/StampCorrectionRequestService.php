<?php

namespace App\Services;

use App\Models\Attendance;
use App\Models\StampCorrectionRequest;
use Carbon\Carbon;

class StampCorrectionRequestService
{
    //申請作成
    public function create(Attendance $attendance, $request)
    {
        $oldBreaks = $attendance->breaks->map(function ($break) {
            return [
                'start_time' => $break->start_time,
                'end_time' => $break->end_time,
            ];
        });

        $newBreaks = [];
        if($request->breaks) {
            foreach($request->breaks as $break) {
                $newBreaks[] = [
                    'start_time' => $break['start_time'],
                    'end_time' => $break['end_time'] ?? null,
                ];
            }
        }

        return StampCorrectionRequest::create([
            'attendance_id' => $attendance->id,
            'user_id' => auth()->id(),

            'new_clock_in'  => $request->clock_in,
            'new_clock_out' => $request->clock_out,
            'new_breaks'    => json_encode($newBreaks),

            'note' => $request->note,
            'status' => StampCorrectionRequest::STATUS_PENDING,
        ]);
    }

    public function getUserRequests(int $userId)
    {
        return StampCorrectionRequest::where('user_id', $userId)
            ->orderBy('created_at')
            ->get();
    }

    public function getListByTab(string $tab, int $userId)
    {
        return StampCorrectionRequest::where('user_id', $userId)
            ->when($tab === StampCorrectionRequest::STATUS_PENDING, function($q){
                $q->where('status', StampCorrectionRequest::STATUS_PENDING);
            })
            ->when($tab === StampCorrectionRequest::STATUS_APPROVED, function($q){
                $q->where('status', StampCorrectionRequest::STATUS_APPROVED);
            })
            ->latest()
            ->get();
    }

    public function getMonthlyAttendance($userId, Carbon $date)
    {
        $lastDay = $date->copy()->endOfMonth()->day;
        $attendanceList = [];

        for ($i = 1; $i <= $lastDay; $i++) {
            $day = $date->copy()->day($i);

            $attendanceList[$day->isoFormat('M/D(dd)')] =
                Attendance::getAttendanceByDate($userId, $day);
        }

        return $attendanceList;
    }
}