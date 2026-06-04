<?php

namespace Tests\Feature\Auth;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\Attributes\DataProvider;

class AuthenticationTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function ログイン画面へアクセスした時_正常に表示される(): void
    {
        // Arrange

        // Act
        $response = $this->get('/login');

        // Assert
        $response->assertOk();
    }

    #[Test]
    public function ログイン画面で正しい認証情報を送信した時_ログインできてマイページへ遷移する(): void
    {
        // Arrange
        $user = User::factory()->create();

        // Act
        $response = $this->post('/login', [
            'email' => $user->email,
            'password' => 'password',
        ]);

        // Assert
        $this->assertAuthenticatedAs($user);
        $response->assertRedirect('/mypage');
    }

    #[Test]
    public function ログイン画面で誤ったパスワードを送信した時_ログインできない(): void
    {
        // Arrange
        $user = User::factory()->create();

        // Act
        $this->post('/login', [
            'email' => $user->email,
            'password' => 'wrong-password',
        ]);

        // Assert
        $this->assertGuest();
    }

    #[Test]
    public function ログアウトした時_ゲスト状態になりトップページへ遷移する(): void
    {
        // Arrange
        $user = User::factory()->create();

        // Act
        $response = $this
            ->actingAs($user)
            ->post('/logout');

        // Assert
        $this->assertGuest();
        $response->assertRedirect('/');
    }
}
