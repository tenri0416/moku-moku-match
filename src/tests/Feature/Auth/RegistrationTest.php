<?php

namespace Tests\Feature\Auth;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\Attributes\DataProvider;


class RegistrationTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function 会員登録画面へアクセスした時_正常に表示される(): void
    {
        // Arrange

        // Act
        $response = $this->get('/register');

        // Assert
        $response->assertOk();
    }

    #[Test]
    public function 会員登録で正しい情報を送信した時_ユーザー登録されマイページへ遷移する(): void
    {
        // Arrange

        // Act
        $response = $this->post('/register', [
            'name' => 'Test User',
            'email' => 'test@example.com',
            'password' => 'password',
            'password_confirmation' => 'password',
        ]);

        // Assert
        $this->assertAuthenticated();

        $this->assertDatabaseHas('users', [
            'name' => 'Test User',
            'email' => 'test@example.com',
        ]);

        $this->assertSame(1, User::where('email', 'test@example.com')->count());

        $response->assertRedirect('/mypage');
    }
}
