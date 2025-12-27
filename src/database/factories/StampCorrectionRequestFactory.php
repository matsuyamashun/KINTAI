<?php

namespace Database\Factories;

use App\Models\StampCorrectionRequest;
use App\Models\Attendance;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class StampCorrectionRequestFactory extends Factory
{
    protected $model = StampCorrectionRequest::class;

    public function definition()
    {
        return [
            'attendance_id' => Attendance::factory(),
            'user_id' => User::factory(),
            'new_clock_in' => '08:00',
            'new_clock_out' => '18:00',
            'new_breaks' => [],
            'note' => '修正申請',
            'status' => 'pending',
        ];
    }

    public function approved()
    {
        return $this->state(fn() => [
            'status' => 'approved',
        ]);
    }
}
