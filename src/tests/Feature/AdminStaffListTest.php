<?php

namespace Tests\Feature;

use App\Models\Attendance;
use App\models\User;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminStaffListTest extends TestCase
{
    use RefreshDatabase;

    public function test_管理者ユーザーが全一般ユーザーの「氏名」「メールアドレス」を確認できる()
    {
        //複数人作る（人）
        $users = User::factory()->count(5)->create();
        $admin = User::factory()->create();

        $response = $this->actingAs($admin, 'admin')->get('/admin/staff_list');

        foreach($users as $user) {
            $response->assertSee($user->name);
            $response->assertSee($user->email);
        }
    }

    public function test_ユーザーの勤怠情報が正しく表示される()
    {
        $user = User::factory()->create();
        $admin = User::factory()->create();

        $date = Carbon::now()->format('Y-m-d');

        Attendance::factory()->create([
            'user_id' => $user->id,
            'date' => $date,
            'clock_in' => $date . ' 09:00:00',
            'clock_out' => $date . ' 18:00:00',
        ]);

        $response = $this->actingAs($admin, 'admin')->get("/admin/attendance_staff/{$user->id}");

        $response->assertSee('09:00');
        $response->assertSee('18:00');
    }

    public function test_「前月」を押下した時に表示月の前月の情報が表示される()
    {
        $user = User::factory()->create();
        $admin = User::factory()->create();

        Attendance::factory()->create([
            'user_id' => $user->id,
            'date' => '2025-12-25',
        ]);

        Attendance::factory()->create([
            'user_id' => $user->id,
            'date' => '2025-11-25',
        ]);

        $response = $this->actingAs($admin, 'admin')->get("/admin/attendance_staff/{$user->id}/2025/11]");
        $response->assertSee('2025-11-25');
    }

    public function test_「翌月」を押下した時に表示月の翌月の情報が表示される()
    {
        $user = User::factory()->create();
        $admin = User::factory()->create();

        Attendance::factory()->create([
            'user_id' => $user->id,
            'date' => '2025-12-25',
        ]);

        Attendance::factory()->create([
            'user_id' => $user->id,
            'date' => '2026-1-25',
        ]);

        $response = $this->actingAs($admin, 'admin')->get("/admin/attendance_staff/{$user->id}/2026/1]");
        $response->assertSee('2026-1-25');
    }

    public function test_「詳細」を押下すると、その日の勤怠詳細画面に遷移する()
    {
        $admin = User::factory()->create();
        $user = User::factory()->create();

        $date = '2025-12-25';

        $attendance = Attendance::factory()->create([
            'user_id' => $user->id,
            'date' => $date,
            'clock_in' => $date . ' 09:00:00',
            'clock_out' => $date . ' 18:00:00',
        ]);

        $response = $this->actingAs($admin, 'admin')->get("/admin/attendance_detail/{$attendance->id}");

        // その日の勤怠データが表示されている
        $response->assertSee('2025年12月25日');
        $response->assertSee($user->name);
        $response->assertSee('09:00');
        $response->assertSee('18:00');
    }
}