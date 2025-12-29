<?php

namespace Tests\Feature;

use App\Models\User;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ClockInTest extends TestCase
{
    use RefreshDatabase;

    public function test_出勤ボタンが正しく機能する()
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        //1回状態作る
        $this->post('/attendance/start');

        $response = $this->get('/attendance');

        $response->assertSee('出勤中');
    }

    public function test_出勤は1日1回できる()
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        //出勤
        $this->post('/attendance/start');
        //退勤
        $this->post('/attendance/end');

        $response = $this->get('/attendance');

        $response->assertSee('お疲れ様でした。');
        $response->assertDontSee('出勤');
    }

    public function test_出勤時刻が勤怠一覧で確認できる()
    {
        Carbon::setTestNow('2025-12-25 10:00:00');

        $user = User::factory()->create();
        $this->actingAs($user);

        //出勤
        $this->post('/attendance/start');

        $response = $this->get('/attendance/list');

        $response->assertSee('10:00');
    }
}