<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class BreakTest extends TestCase
{
    use RefreshDatabase;

    public function test_休憩ボタンが正しく機能できる()
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        //出勤
        $this->post('/attendance/start');
        //休憩
        $this->post('/break/start');

        $response = $this->get('/attendance');

        $response->assertSee('休憩中');
    }

    public function test_休憩は1日に何回もできる()
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        //出勤
        $this->post('/attendance/start');
        //休憩
        $this->post('/break/start');
        //休憩戻る
        $this->post('/break/end');

        $response = $this->get('/attendance');

        $response->assertSee('休憩入');
    }

    public function test_休憩戻るボタンが正しく機能できる()
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        $this->post('/attendance/start');
        $this->post('/break/start');
        $this->post('/break/end');

        $response = $this->get('/attendance');

        $response->assertSee('出勤中');
    }

    public function test_休憩戻るは1日に何回もできる()
    {
        $user = User::factory()->create();
        $this->actingAs($user);


        $this->post('/attendance/start');
        $this->post('/break/start');
        $this->post('/break/end');
        $this->post('/break/start');

        $response = $this->get('/attendance');

        $response->assertSee('休憩戻');
    }

    public function test_休憩時刻が勤怠一覧画面で確認できる()
    {
        $user = User::factory()->create();
        $this->actingAs($user);


        $this->post('/attendance/start');
        $this->post('/break/start');
        $this->post('/break/end');

        $response = $this->get('/attendance/list');

        $response->assertSee(now()->format('H:i'));
    }
}