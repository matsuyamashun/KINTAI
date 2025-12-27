<?php

namespace Tests\Feature;

use App\Models\Attendance;
use App\Models\BreakTime;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AttendanceDetailTest extends TestCase
{
    use RefreshDatabase;

    public function test_勤怠詳細画面の名前がログインユーザーの指名になってる()
    {
        $user = User::factory()->create();
        //ユーザー情報
        $attendance = Attendance::factory()->create([
            'user_id' => $user->id,
        ]);

        $response = $this->actingAs($user)->get("/attendance/detail/{$attendance->id}");

        $response->assertSee($user->name);
    }

    public function test_勤怠詳細画面の「日付」が選択した日付になっている()
    {
        $user = User::factory()->create();

        $date = '2025-12-25';
        //ユーザー情報
        $attendance = Attendance::factory()->create([
            'user_id' => $user->id,
            'date' => $date,
        ]);

        $response = $this->actingAs($user)->get("/attendance/detail/{$attendance->id}");

        $response->assertSee('2025/12/25(木)');
    }

    public function test_「出勤・退勤」にて記されている時間がログインユーザーの打刻と一致している()
    {
        $user = User::factory()->create();

        $date = '2025-12-25';
        //ユーザー情報
        $attendance = Attendance::factory()->create([
            'user_id' => $user->id,
            'date' => $date,
            'clock_in' => $date . ' 09:00:00',
            'clock_out' => $date . ' 18:00:00',
        ]);

        $response = $this->actingAs($user)->get("/attendance/detail/{$attendance->id}");

        $response->assertSee('09:00');
        $response->assertSee('18:00');
    }

    public function test_「休憩」にて記されている時間がログインユーザーの打刻と一致している()
    {
        $user = User::factory()->create();

        $date = '2025-12-25';
        //ユーザー情報
        $attendance = Attendance::factory()->create([
            'user_id' => $user->id,
            'date' => $date,
            'clock_in' => $date . ' 09:00:00',
            'clock_out' => $date . ' 18:00:00',
        ]);
        //休憩
        BreakTime::factory()->create([
            'attendance_id' => $attendance->id,
            'start_time' => $date . ' 12:00:00',
            'end_time' => $date . ' 13:00:00',
        ]);

        $response = $this->actingAs($user)->get("/attendance/detail/{$attendance->id}");

        $response->assertSee('12:00');
        $response->assertSee('13:00');
    }
}
