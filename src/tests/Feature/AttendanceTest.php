<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AttendanceTest extends TestCase
{
    use RefreshDatabase;

    public function test_勤務外の場合、勤怠ステータスが正しく表示()
    {
        $user = User::factory()->create();
        $this->actingAs($user)->post('/attendance.start');

        $response = $this->get('/attendance');

        $response->assertSee('勤務外');
    }

    public function test_出勤中の場合、勤怠ステータスが正しく表示()
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        //1回状態作る
        $this->post('/attendance/start');

        $response = $this->get('/attendance');

        $response->assertSee('出勤中');
    }

    public function test_休憩中の場合、勤怠ステータスが正しく表示()
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        //1回出勤させる
        $this->post('/attendance/start');
        $this->post('/break/start');

        $response = $this->get('/attendance');

        $response->assertSee('休憩中');
    }

    public function test_退勤済みの場合、勤怠ステータスが正しく表示()
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        //1回出勤させる
        $this->post('/attendance/start');
        $this->post('/attendance/end');

        $response = $this->get('/attendance');

        $response->assertSee('退勤済');
    }
}