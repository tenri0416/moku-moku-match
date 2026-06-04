<?php

namespace Tests\Feature\Auth;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\Attributes\DataProvider;

class PasswordUpdateTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function パスワード変更で正しい現在のパスワードを送信した時_パスワードが更新される(): void
    {
        // Arrange
        $user = User::factory()->create();

        // Act
        $response = $this
            ->actingAs($user)
            ->from('/profile/edit')
            ->put('/password', [
                'current_password' => 'password',
                'password' => 'new-password',
                'password_confirmation' => 'new-password',
            ]);

        // Assert
        $response
            ->assertSessionHasNoErrors()
            ->assertRedirect('/profile/edit');

        $this->assertTrue(Hash::check('new-password', $user->refresh()->password));
    }

    #[Test]
    public function パスワード変更で誤った現在のパスワードを送信した時_バリデーションエラーになる(): void
    {
        // Arrange
        $user = User::factory()->create();

        // Act
        $response = $this
            ->actingAs($user)
            ->from('/profile/edit')
            ->put('/password', [
                'current_password' => 'wrong-password',
                'password' => 'new-password',
                'password_confirmation' => 'new-password',
            ]);

        // Assert
        $response
            ->assertSessionHasErrorsIn('updatePassword', 'current_password')
            ->assertRedirect('/profile/edit');
    }
}
