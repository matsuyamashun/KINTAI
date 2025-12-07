<?php

namespace App\Http\Controllers;

use App\Http\Requests\DetailAttendanceRequest;
use App\Models\Attendance;
use App\Services\StampCorrectionRequestService;
use Illuminate\Http\Request;

class StampCorrectionRequestController extends Controller
{
    //
    protected $service;

    public function __construct(StampCorrectionRequestService $service)
    {
        $this->service = $service;
    }

    public function index()
    {

        $tab = request()->query('tab', 'pending');

        $requests = $this->service->getListByTab($tab, auth()->id());

        return view('correction.list', compact('requests', 'tab'));
    }

    public function store(DetailAttendanceRequest $request, Attendance $attendance)
    {
        app(StampCorrectionRequestService::class)->create($attendance, $request);

        return redirect()->route('correction.list', ['tab' => 'pending']);
    }
}
