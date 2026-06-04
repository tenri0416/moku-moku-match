<?php

namespace Tests\Feature\Auth;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\Attributes\DataProvider;


class PasswordConfirmationTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function パスワード確認画面へアクセスした時_正常に表示される(): void
    {
        // Arrange
        $user = User::factory()->create();

        // Act
        $response = $this
            ->actingAs($user)
            ->get('/confirm-password');

        // Assert
        $response->assertOk();
    }

    #[Test]
    public function パスワード確認で正しいパスワードを送信した時_確認が成功する(): void
    {
        // Arrange
        $user = User::factory()->create();

        // Act
        $response = $this
            ->actingAs($user)
            ->post('/confirm-password', [
                'password' => 'password',
            ]);

        // Assert
        $response->assertRedirect();
        $response->assertSessionHasNoErrors();
    }

    #[Test]
    public function パスワード確認で誤ったパスワードを送信した時_バリデーションエラーになる(): void
    {
        // Arrange
        $user = User::factory()->create();

        // Act
        $response = $this
            ->actingAs($user)
            ->post('/confirm-password', [
                'password' => 'wrong-password',
            ]);

        // Assert
        $response->assertSessionHasErrors();
    }
}
