<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class LoginTest extends TestCase
{
    use RefreshDatabase;

    public function test_メールアドレスが未入力の場合、バリデーションが表示()
    {
        $response = $this->post('/login', [
            'email' => '',
            'password' => 'password123',
        ]);

        $response->assertSessionHasErrors('email');
    }

    public function test_パスワードが未入力の場合、バリデーションが表示()
    {
        $response = $this->post('/login', [
            'email' => 'test123@icloud.com',
            'password' => '',
        ]);

        $response->assertSessionHasErrors('password');
    }

    public function test_登録内容と一致しない場合、バリデーションメッセージが表示される()
    {
        $user = User::factory()->create();

        $response = $this->post('/login', [
            'email' => $user->email,
            'password' => 'password123',
        ]);

        $response->assertSessionHasErrors('email');
    }
}
