<?php

namespace Tests\Feature;

use App\Models\Attendance;
use App\Models\BreakTime;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminDatailTest extends TestCase
{
    use RefreshDatabase;

    public function test_勤怠詳細画面に表示されるデータが選択したものになっている()
    {
        $user = User::factory()->create();
        $admin = User::factory()->create();

        $date = '2025-12-25';

        //勤怠データ
        $attendance = $this->createAttendanceData($user, $date);

        $response = $this->actingAs($admin, 'admin')->get("/admin/attendance_detail/{$attendance->id}");

        $response->assertStatus(200);

        $response->assertSee('09:00');
        $response->assertSee('18:00');
    }

    public function test_出勤時間が退勤時間より後になっている場合、エラーメッセージが表示される()
    {
        $user = User::factory()->create();
        $admin = User::factory()->create();

        $date = '2025-12-25';

        //勤怠データ
        $attendance = $this->createAttendanceData($user, $date);

        $response = $this->actingAs($admin, 'admin')->patch("/admin/attendance_detail/{$attendance->id}", [
            'clock_in' => '19:00',
            'clock_out' => '18:00',
            'note' => '修正',
        ]);

        $response->assertSessionHasErrors([
            'clock_in' => '出勤時刻が不適切な値です',
        ]);
    }

    public function test_休憩開始時間が退勤時間より後になっている場合、エラーメッセージが表示される()
    {
        $user = User::factory()->create();
        $admin = User::factory()->create();

        $date = '2025-12-25';

        $attendance = $this->createAttendanceData($user, $date);

        $response = $this->actingAs($admin, 'admin')->patch("/admin/attendance_detail/{$attendance->id}",
            [
                'clock_in' => '09:00',
                'clock_out' => '18:00',
                'breaks' => [
                    [
                        'start_time' => '12:00',
                        'end_time' => '11:00',
                    ],
                ],
                'note' => '修正',
            ]
        );

        $response->assertSessionHasErrors([
            'breaks.0.start_time' => '休憩開始時間が不適切な値です',
        ]);
    }

    public function test_休憩終了時間が退勤時間より後になっている場合、エラーメッセージが表示される()
    {
        $user = User::factory()->create();
        $admin = User::factory()->create();

        $date = '2025-12-25';

        $attendance = $this->createAttendanceData($user, $date);

        $response = $this->actingAs($admin, 'admin')->patch("/admin/attendance_detail/{$attendance->id}",
            [
                'clock_in' => '09:00',
                'clock_out' => '18:00',
                'breaks' => [
                    [
                        'start_time' => '17:00',
                        'end_time' => '19:00',
                    ],
                ],
                'note' => '修正',
            ]
        );

        $response->assertSessionHasErrors([
            'breaks.0.end_time' => '休憩終了時間が不適切な値です',
        ]);
    }


    public function test_備考欄が未入力になっている場合、エラーメッセージが表示される()
    {
        $user = User::factory()->create();
        $admin = User::factory()->create();

        $date = '2025-12-25';
        //ユーザー情報
        $attendance = $this->createAttendanceData($user, $date);

        $response = $this->actingAs($admin, 'admin')->patch("/admin/attendance_detail/{$attendance->id}",
            [
                'clock_in' => '09:00',
                'clock_out' => '18:00',
                'breaks' => [
                    [
                        'start_time' => '12:00',
                        'end_time' => '13:00',
                    ],
                ],
                'note' => '',
            ]
        );

        $response->assertSessionHasErrors([
            'note' => '備考を記入してください',
        ]);
    }

    private function createAttendanceData($user, $date)
    {
        $attendance = Attendance::factory()->create([
            'user_id' => $user->id,
            'date' => $date,
            'clock_in' => $date . ' 09:00:00',
            'clock_out' => $date . ' 18:00:00',
        ]);

        BreakTime::factory()->create([
            'attendance_id' => $attendance->id,
            'start_time' => $date . ' 12:00:00',
            'end_time' => $date . ' 13:00:00',
        ]);

        return $attendance;
    }
}
