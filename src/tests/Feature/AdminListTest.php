<?php

namespace Tests\Feature;

use App\Models\Attendance;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminListTest extends TestCase
{
    use RefreshDatabase;

    public function test_その日になされた全ユーザーの勤怠情報が正確に確認できる()
    {
        //一般ユーザー
        $user = User::factory()->create();
        //管理者
        $admin = User::factory()->create();

        //データ修正
        $date = '2025-12-25';

        $attendance = Attendance::factory()->create([
            'user_id' => $user->id,
            'date' => $date,
            'clock_in' => $date . ' 09:00:00',
            'clock_out' => $date . ' 18:00:00',
        ]);

        //修正申請
        $this->actingAs($user)->patch(
            "/attendance/detail/{$attendance->id}",
            [
                'clock_in' => '09:00',
                'clock_out' => '18:00',
                'breaks' => [
                    [
                        'start_time' => '12:00',
                        'end_time' => '13:00',
                    ],
                ],
                'note' => '修正',
            ]
        );

        $response = $this->actingAs($admin, 'admin')->get('/admin/attendance_list');

        $response->assertStatus(200);
    }

    public function test_遷移した際に現在の日付が表示される()
    {
        //管理者
        $admin = User::factory()->create();

        $response = $this->actingAs($admin, 'admin')->get('/admin/attendance_list');

        $response->assertSee(now()->format('Y/m/d'));
    }

    public function test_「前日」を押下した時に前の日の勤怠情報が表示される()
    {
        $admin = User::factory()->create();

        $prevDay = now()->subDay();
        $ymd = $prevDay->format('Y-m-d');

        //クエリパラメータ
        $response = $this->actingAs($admin, 'admin')->get("/admin/attendance_list/{$ymd}");

        $response->assertSee($prevDay->format('Y/m/d'));
    }

    public function test_「翌日」を押下した時に次の日の勤怠情報が表示される()
    {
        $admin = User::factory()->create();

        $nextDay = now()->addDay();
        $ymd = $nextDay->format('Y-m-d');

        //クエリパラメータ
        $response = $this->actingAs($admin, 'admin')->get("/admin/attendance_list/{$ymd}");

        $response->assertSee($nextDay->format('Y/m/d'));
    }
}
