<?php

namespace Tests\Feature;

use App\models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AttendanceListTest extends TestCase
{
    use RefreshDatabase;

    public function test_自分が行った勤怠情報がすべて表示する()
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        //出勤
        $this->post('/attendance/start');
        //休憩入
        $this->post('/break/start');
        //休憩戻
        $this->post('/break/end');
        //休憩戻
        $this->post('/attendance/end');

        $response = $this->get('/attendance/list');

        $response->assertSee(now()->format('H:i'));
    }

    public function test_勤怠一覧に行った際現在の月が表示する()
    {

        $user = User::factory()->create();
        $this->actingAs($user);

        $response = $this->get('/attendance/list');

        $response->assertSee(now()->format('Y/m'));
    }

    public function test_「前月」を押下した時に表示月の前月の情報が表示される()
    {

        $user = User::factory()->create();
        $this->actingAs($user);

        //前の月
        $prevMonth = now()->subMonth();
        $ym = $prevMonth->format('Y-m');

        $response = $this->get("/attendance/list/{$ym}");

        $response->assertSee($prevMonth->format('Y/m'));
    }

    public function test_「翌月」を押下した時に表示月の翌月の情報が表示される()
    {

        $user = User::factory()->create();
        $this->actingAs($user);

        //次の月
        $nextMonth = now()->addMonth();
        $ym = $nextMonth->format('Y-m');

        $response = $this->get("/attendance/list/{$ym}");

        $response->assertSee($nextMonth->format('Y/m'));
    }

    public function test_詳細を押すとその日の勤怠詳細画面に遷移する()
    {

        $user = User::factory()->create();
        $this->actingAs($user);

        //1回勤怠する
        $this->post('/attendance/start');
        $this->post('/attendance/end');

        //今日の勤怠とる
        $attendance = $user->attendances()
            ->whereDate('clock_in', now()->today())
            ->first();

        $response = $this->get("/attendance/detail/{$attendance->id}");

        $response->assertStatus(200);
    }
}