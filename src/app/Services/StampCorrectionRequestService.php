<?php

namespace App\Services;

use App\Models\Attendance;
use App\Models\StampCorrectionRequest;

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

            'old_clock_in' => $attendance->clock_in,
            'old_clock_out' => $attendance->clock_out,
            'old_breaks' => json_encode($oldBreaks),

            'new_clock_in'  => $request->clock_in,
            'new_clock_out' => $request->clock_out,
            'new_breaks'    => json_encode($newBreaks),

            'note' => $request->note,
            'status' => 'pending',
        ]);
    }

    public function getUserRequests($userId)
    {
        return StampCorrectionRequest::where('user_id', $userId)
            ->orderBy('created_at')
            ->get();
    }

    public function getListByTab(string $tab, int $userId)
    {
        return StampCorrectionRequest::where('user_id', $userId)
            ->when($tab === 'pending', function($q){
                $q->where('status', 'pending');
            })
            ->when($tab === 'approved', function($q){
                $q->where('status', 'approved');
            })
            ->latest()
            ->get();
    }

}