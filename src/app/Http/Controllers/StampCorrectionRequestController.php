<?php

namespace App\Http\Controllers;

use App\Http\Requests\DetailAttendanceRequest;
use App\Models\Attendance;
use App\Services\StampCorrectionRequestService;

class StampCorrectionRequestController extends Controller
{
    protected $stampCorrectionRequestService;

    public function __construct(StampCorrectionRequestService $stampCorrectionRequestService)
    {
         $this->stampCorrectionRequestService = $stampCorrectionRequestService;
    }

    public function index()
    {
        $tab = request()->query('tab', 'pending');

        $requests = $this->stampCorrectionRequestService->getListByTab($tab, auth()->id());

        return view('correction.list', compact('requests', 'tab'));
    }

    public function store(DetailAttendanceRequest $request, Attendance $attendance)
    {
         $this->stampCorrectionRequestService->create($attendance, $request);

        return redirect()->route('correction.list', ['tab' => 'pending']);
    }
}