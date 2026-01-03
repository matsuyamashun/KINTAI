<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RegisterTest extends TestCase
{
    use RefreshDatabase;

    public function test_名前が未入力の場合、バリデーションメッセージが表示()
    {
        $response = $this->post('/register', [
            'name' => '',
            'email' => 'test123@icloud.com',
            'password' => 'password',
            'password_confirmation' => 'password',
        ]);

        $response->assertSessionHasErrors('name');
    }

    public function test_メールアドレスが未入力の場合、バリデーションメッセージが表示()
    {
        $response = $this->post('/register', [
            'name' => '松山',
            'email' => '',
            'password' => 'password',
            'password_confirmation' => 'password',
        ]);

        $response->assertSessionHasErrors('email');
    }

    public function test_パスワードが8文字未満の時バリデーションメッセージが表示()
    {
        $response = $this->post('/register', [
            'name' => '松山',
            'email' => 'test123@icloud.com',
            'password' => 'pass12',
            'password_confirmation' => 'pass12',
        ]);

        $response->assertSessionHasErrors('password');
    }

    public function test_パスワードが一致しないとき時バリデーションメッセージが表示()
    {
        $response = $this->post('/register', [
            'name' => '松山',
            'email' => 'test123@icloud.com',
            'password' => 'pass1234',
            'password_confirmation' => 'pass123',
        ]);

        $response->assertSessionHasErrors('password');
    }

    public function test_パスワードが未入力の時バリデーションメッセージが表示()
    {
        $response = $this->post('/register', [
            'name' => '松山',
            'email' => 'test123@icloud.com',
            'password' => '',
            'password_confirmation' => 'pass123',
        ]);

        $response->assertSessionHasErrors('password');
    }

    public function test_フォームに内容が入力されていた場合、データが正常に保存される()
    {
        $this->post('/register', [
            'name' => '松山',
            'email' => 'test123@icloud.com',
            'password' => 'pass1234',
            'password_confirmation' => 'pass1234',
        ]);

        $this->assertDatabaseHas('users', [
            'name' => '松山',
            'email' => 'test123@icloud.com',
        ]);
    }
}