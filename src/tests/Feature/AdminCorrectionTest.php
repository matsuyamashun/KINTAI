<?php

namespace Tests\Feature;

use App\Models\Attendance;
use App\Models\StampCorrectionRequest;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminCorrectionTest extends TestCase
{
    use RefreshDatabase;

    public function test_承認待ちの修正申請が表示されている()
    {
        $admin = User::factory()->create();
        $users = User::factory()->count(2)->create();

        foreach ($users as $user) {
            $attendance = Attendance::factory()->create([
                'user_id' => $user->id,
            ]);

            StampCorrectionRequest::factory()->create([
                'attendance_id' => $attendance->id,
                'user_id' => $user->id,
                'status' => 'pending',
            ]);
        }

        $response = $this->actingAs($admin, 'admin')->get('/admin/stamp_correction_request/list?tab=pending');

        $response->assertStatus(200);
    }

    public function test_承認済みの修正申請が表示されている()
    {
        $admin = User::factory()->create();

        StampCorrectionRequest::factory()->create([
                'status' => 'approved',
            ]);

        $response = $this->actingAs($admin, 'admin')->get('/admin/stamp_correction_request/list?tab=approved');

        $response->assertStatus(200);
    }

    public function test_修正申請の詳細内容が正しく表示されている()
    {
        $admin = User::factory()->create();

        $request = StampCorrectionRequest::factory()->create([
            'new_clock_in' => '10:00',
            'new_clock_out' => '20:00',
            'note' => '修正しまっす',
        ]);

        $response = $this->actingAs($admin, 'admin')->get("/admin/stamp_correction_request/detail/{$request->id}");

        $response->assertSee('10:00');
        $response->assertSee('20:00');
        $response->assertSee('修正しまっす');
    }

    public function test_修正申請の承認処理が正しく行われる()
    {
        $user = User::factory()->create();
        $admin = User::factory()->create();

        $date = '2025-12-25';

        $attendance = Attendance::factory()->create([
            'user_id' => $user->id,
            'date' => $date,
            'clock_in' => $date . ' 09:00:00',
            'clock_out' => $date . ' 18:00:00',
            'note' => '修正',
        ]);

        $request = StampCorrectionRequest::create([
            'attendance_id' => $attendance->id,
            'user_id' => $user->id,
            'new_clock_in' => '10:00',
            'new_clock_out' => '20:00',
            'note' => '修正しまっす',
            'status' => 'pending',
            'new_breaks' => [],//ここ大事
        ]);

        //承認する
        $response = $this->actingAs($admin, 'admin')->patch(route('admin.correction_approve', $request->id));

        //リダイレクトする
        $response->assertRedirect(route('admin.correction_list', ['tab' => 'approved']));

        //承認済みになる
        $this->assertDatabaseHas('stamp_correction_requests', [
            'id' => $request->id,
            'status' => 'approved',
        ]);

        //更新（DB）
        $this->assertDatabaseHas('attendances', [
            'id' => $attendance->id,
            'clock_in' => '2025-12-25 10:00:00',
            'clock_out' => '2025-12-25 20:00:00',
            'note' => '修正しまっす',
        ]);
    }
}
