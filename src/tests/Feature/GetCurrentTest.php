<?php

namespace Tests\Feature;

use App\Models\User;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class GetCurrentTest extends TestCase
{
    use RefreshDatabase;

    public function test_現在の日時情報がUIと同じ形式で出力されている()
    {
        Carbon::setTestNow(new Carbon('2025-12-22 18:26:50'));

        //ログインさせる
        $user = User::factory()->create();

        $response = $this->actingAs($user)->get('/attendance');

        //viewの表示
        $response->assertSee('2025年12月22日 (月)');
        $response->assertSee('18:26');
    }
}