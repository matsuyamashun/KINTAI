<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ClockOutTest extends TestCase
{
    use RefreshDatabase;

    public function test_退勤ボタンが正しく機能する()
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        //出勤
        $this->post('/attendance/start');
        //退勤
        $this->post('/attendance/end');

        $response = $this->get('/attendance');

        $response->assertSee('退勤済');
    }

    public function test_退勤時刻が勤怠一覧で確認できる()
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        $this->post('/attendance/start');
        $this->post('/attendance/end');

        $response = $this->get('/attendance/list');

        $response->assertSee(now()->format('H:i'));
    }
}