<?php

namespace App\Http\Controllers;

use App\Models\StampCorrectionRequest;
use App\Services\StampCorrectionRequestService;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class AdminAttendanceCorrectionController extends Controller
{
    protected $stampCorrectionRequestService;

    public function __construct(StampCorrectionRequestService $stampCorrectionRequestService)
    {
         $this->stampCorrectionRequestService = $stampCorrectionRequestService;
    }

    public function index()
    {
        $tab = request()->query('tab', 'pending');

        $requests = $this->stampCorrectionRequestService->getAdminListByTab($tab);

        return view('admin.correction_list', compact('requests', 'tab'));
    }

    public function show(StampCorrectionRequest $request)
    {
        $breaks = $request->new_breaks;

        return view('admin.correction_detail', compact('request', 'breaks'));
    }

    public function approve(StampCorrectionRequest $request)
    {
        DB::transaction(function () use ($request) {

            $attendance = $request->attendance;

            //出勤、勤怠、備考反映
            $attendance->update([
                'clock_in' => Carbon::parse($attendance->date . '' . $request->new_clock_in),
                'clock_out' => Carbon::parse($attendance->date . '' . $request->new_clock_out),
                'note' => $request->note,
            ]);

            //1回削除作りなおす
            $attendance->breaks()->delete();

            //休憩更新
            foreach ($request->new_breaks as $break) {
                $attendance->breaks()->create([
                    'start_time' => Carbon::parse($attendance->date . '' . $break['start_time']),
                    'end_time' =>  Carbon::parse($attendance->date . '' . $break['end_time']),
                ]);
            }

            //承認済みへ
            $request->update([
                'status' => 'approved',
            ]);
        });
        return redirect()->route('admin.correction_list', ['tab' => 'approve']);
    }
}
