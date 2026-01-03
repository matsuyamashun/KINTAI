<?php

namespace Database\Factories;

use App\Models\Attendance;
use Illuminate\Database\Eloquent\Factories\Factory;

class BreakTimeFactory extends Factory
{
    public function definition()
    {
        $date = $this->faker->date();

        return [
            'attendance_id' => Attendance::factory(),
            'start_time' => $date . ' 12:00:00',
            'end_time' => $date . ' 13:00:00',
        ];
    }
}
