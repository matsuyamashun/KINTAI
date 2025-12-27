<?php

namespace Tests\Feature;

use App\Models\Attendance;
use App\Models\BreakTime;
use App\Models\StampCorrectionRequest;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class UpdateAttendanceDatailTest extends TestCase
{
    use RefreshDatabase;

    public function test_出勤時間が退勤時間より後になっている場合、エラーメッセージが表示される()
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

        $response = $this->actingAs($user)->patch("/attendance/detail/{$attendance->id}",[
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

        $date = '2025-12-25';
        //ユーザー情報
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

        $response = $this->actingAs($user)->patch("/attendance/detail/{$attendance->id}",
            [
                //配列でわたす
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

        $date = '2025-12-25';
        //ユーザー情報
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

        $response = $this->actingAs($user)->patch("/attendance/detail/{$attendance->id}",
            [
                //配列でわたす
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

        $date = '2025-12-25';
        //ユーザー情報
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

        $response = $this->actingAs($user)->patch("/attendance/detail/{$attendance->id}",
            [
                //配列でわたす
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

    public function test_修正申請処理が実行される()
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

        $this->actingAs($user)->patch("/attendance/detail/{$attendance->id}",
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
            ]);

        //申請テーブルに入る
        $this->assertDatabaseHas('stamp_correction_requests', [
            'attendance_id' => $attendance->id,
            'user_id' => $user->id,
            'status' => 'pending',
        ]);

        //申請一覧見る
        $response = $this->actingAs($admin, 'admin')->get('/admin/stamp_correction_request/list');

        $response->assertStatus(200);

        //勤怠詳細
        $request = StampCorrectionRequest::first();

        $response = $this->actingAs($admin, 'admin')->get("/admin/stamp_correction_request/detail/{$request->id}");

        $response->assertStatus(200);
    }

    public function test_「承認待ち」にログインユーザーが行った申請が全て表示されていること()
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

        //pendingで登録
        $this->assertDatabaseHas('stamp_correction_requests', [
            'user_id' => $user->id,
            'attendance_id' => $attendance->id,
            'status' => 'pending',
        ]);

        $response = $this->actingAs($user)->get('/stamp_correction_request/list?tab=pending');

        $response->assertStatus(200);
    }

    public function test_「承認済み」に管理者が承認した申請が全て表示されていること()
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

        $request = StampCorrectionRequest::first();

        //承認
        $this->actingAs($admin)->patch("/admin/stamp_correction_request/detail{$request->id}/approve");

        $response = $this->actingAs($user)->get('/stamp_correction_request/list?tab=approved');

        $response->assertStatus(200);
    }


    public function test_各申請の「詳細」を押下すると勤怠詳細画面に遷移する()
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

        $request = StampCorrectionRequest::first();

        //承認
        $this->actingAs($admin)->patch("/admin/stamp_correction_request/detail{$request->id}/approve");

        $response = $this->actingAs($user)->get('/stamp_correction_request/list');

        $response->assertStatus(200);

        $response = $this->actingAs($admin, 'admin')->get("/admin/attendance_detail/{$attendance->id}");

        $response->assertStatus(200);
    }
}