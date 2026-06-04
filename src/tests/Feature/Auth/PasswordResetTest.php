<?php

namespace Tests\Feature\Auth;

use App\Models\User;
use Illuminate\Auth\Notifications\ResetPassword;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Notification;
use Tests\TestCase;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\Attributes\DataProvider;


class PasswordResetTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function パスワード再設定リンク申請画面へアクセスした時_正常に表示される(): void
    {
        // Arrange

        // Act
        $response = $this->get('/forgot-password');

        // Assert
        $response->assertOk();
    }

    #[Test]
    public function パスワード再設定リンクを申請した時_通知が送信される(): void
    {
        // Arrange
        Notification::fake();

        $user = User::factory()->create();

        // Act
        $this->post('/forgot-password', [
            'email' => $user->email,
        ]);

        // Assert
        Notification::assertSentTo($user, ResetPassword::class);
    }

    #[Test]
    public function パスワード再設定URLへアクセスした時_再設定画面が表示される(): void
    {
        // Arrange
        Notification::fake();

        $user = User::factory()->create();

        $this->post('/forgot-password', [
            'email' => $user->email,
        ]);

        // Act & Assert
        Notification::assertSentTo($user, ResetPassword::class, function ($notification) {
            $response = $this->get('/reset-password/' . $notification->token);

            $response->assertOk();

            return true;
        });
    }

    #[Test]
    public function 正しいトークンでパスワード再設定した時_パスワードが更新されログイン画面へ遷移する(): void
    {
        // Arrange
        Notification::fake();

        $user = User::factory()->create();

        $this->post('/forgot-password', [
            'email' => $user->email,
        ]);

        // Act & Assert
        Notification::assertSentTo($user, ResetPassword::class, function ($notification) use ($user) {
            $response = $this->post('/reset-password', [
                'token' => $notification->token,
                'email' => $user->email,
                'password' => 'new-password',
                'password_confirmation' => 'new-password',
            ]);

            $response
                ->assertSessionHasNoErrors()
                ->assertRedirect(route('login'));

            $this->assertTrue(Hash::check('new-password', $user->fresh()->password));

            return true;
        });
    }
}
