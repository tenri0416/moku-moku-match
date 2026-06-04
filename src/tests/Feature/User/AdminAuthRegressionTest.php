<?php

namespace Tests\Feature\Admin;

use App\Models\Admin;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class AdminAuthRegressionTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function 管理者ログイン画面へアクセスした時_正常に表示できる(): void
    {
        // Arrange

        // Act
        $response = $this->get(route('admin.login'));

        // Assert
        $response->assertOk();
    }

    #[Test]
    public function 管理者ログインで_正しい認証情報なら二段階認証画面へ遷移する(): void
    {
        // Arrange
        $admin = $this->管理者を作成する();

        // Act
        $response = $this->post(route('admin.login.store'), [
            'email' => $admin->email,
            'password' => 'password',
        ]);

        // Assert
        $response->assertRedirect();
        $this->assertGuest('admin');
    }

    #[Test]
    public function 管理者ログインで_誤ったパスワードならログインできない(): void
    {
        // Arrange
        $admin = $this->管理者を作成する();

        // Act
        $response = $this->from(route('admin.login'))
            ->post(route('admin.login.store'), [
                'email' => $admin->email,
                'password' => 'wrong-password',
            ]);

        // Assert
        $response->assertRedirect(route('admin.login'));
        $response->assertSessionHasErrors();
        $this->assertGuest('admin');
    }

    #[Test]
    public function 未ログインで管理者ダッシュボードへアクセスした場合_管理者ログイン画面へリダイレクトされる(): void
    {
        // Arrange

        // Act
        $response = $this->get(route('admin.dashboard'));

        // Assert
        $response->assertRedirect(route('admin.login'));
    }

    #[Test]
    public function 管理者ログイン済みでログアウトした時_管理者ログイン画面へ遷移する(): void
    {
        // Arrange
        $admin = $this->管理者を作成する();

        // Act
        $response = $this
            ->actingAs($admin, 'admin')
            ->post(route('admin.logout'));

        // Assert
        $this->assertGuest('admin');
        $response->assertRedirect(route('admin.login'));
    }

    private function 管理者を作成する(): Admin
    {
        return Admin::create([
            'name' => '認証テスト管理者',
            'email' => 'admin-auth-regression@example.com',
            'email_verified_at' => now(),
            'password' => Hash::make('password'),
            'status' => 1,
            'remember_token' => null,
        ]);
    }
}
