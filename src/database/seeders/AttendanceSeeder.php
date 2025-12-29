<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Attendance;
use Carbon\Carbon;

class AttendanceSeeder extends Seeder
{
    public function run()
    {
        $users = User::where('role', User::ROLE_USER)->get();

        $startDate = now()->subMonths(1)->startOfMonth();
        $endDate   = now()->endOfMonth();

        foreach ($users as $user) {

            $date = $startDate->copy();

            while ($date->lte($endDate)) {

                // 土日は除外
                if ($date->isWeekend()) {
                    $date->addDay();
                    continue;
                }

                $attendance = Attendance::create([
                    'user_id'   => $user->id,
                    'date'      => $date->toDateString(),
                    'clock_in'  => $date->copy()->setTime(9, 0),
                    'clock_out' => $date->copy()->setTime(18, 0),
                    'note'      => '勤怠',
                ]);

                // 休憩1回
                $attendance->breaks()->create([
                    'start_time' => $date->copy()->setTime(12, 0),
                    'end_time'   => $date->copy()->setTime(13, 0),
                ]);

                $date->addDay();
            }
        }
    }
}
