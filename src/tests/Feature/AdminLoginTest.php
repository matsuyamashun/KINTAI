<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminLoginTest extends TestCase
{
    use RefreshDatabase;

    public function test_メールアドレスが未入力の場合、バリデーションメッセージが表示()
    {
        $response = $this->post('/admin/login',[
            'email' => '',
            'password' => 'password123'
        ]);

        $response->assertSessionHasErrors('email');
    }

    public function test_パスワードが未入力の場合、バリデーションメッセージが表示()
    {
        $response = $this->post('/admin/login', [
            'email' => 'admin@example.com',
            'password' => ''
        ]);

        $response->assertSessionHasErrors('password');
    }

    public function test_登録内容と一致しない場合、バリデーションメッセージが表示()
    {
        $response = $this->post('/admin/login', [
            'email' => 'admin@example.com',
            'password' => 'password1234'
        ]);

        $response->assertSessionHasErrors('email');
    }
}