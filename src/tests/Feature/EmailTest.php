<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Auth\Notifications\VerifyEmail;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\URL;
use Tests\TestCase;

class EmailTest extends TestCase
{
    use RefreshDatabase;

    public function test_会員登録後、認証メールが送信される()
    {
        //メール送信
        Notification::fake();

        $this->post('/register', [
            'name' => '松山',
            'email' => 'test123@icloud.com',
            'password' => 'password123',
            'password_confirmation' => 'password123',
        ]);

        $user = User::where('email', 'test123@icloud.com')->first();

        Notification::assertSentTo($user, VerifyEmail::class);//VerifyEmailはNotification
    }

    public function test_メール認証誘導画面で「認証はこちらから」ボタンを押下するとメール認証サイトに遷移する()
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->get(route('verification.notice'));

        $response->assertSee('認証はこちらから');
    }

    public function test_メール認証サイトのメール認証を完了すると、勤怠登録画面に遷移する()
    {
        $user = User::factory()->create();

        $verificationUrl = URL::temporarySignedRoute(
            'verification.verify',
            now()->addMinute(60),
            [
                'id' => $user->id,
                'hash' => sha1($user->email),
            ]
            );

        $response = $this->actingAs($user)->get($verificationUrl);

        $response->assertRedirect(route('attendance'));
    }
}
