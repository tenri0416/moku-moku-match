<?php

namespace Tests\Feature\Auth;

use App\Models\User;
use Illuminate\Auth\Events\Verified;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\URL;
use Tests\TestCase;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\Attributes\DataProvider;

class EmailVerificationTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function メール認証案内画面へアクセスした時_正常に表示される(): void
    {
        // Arrange
        $user = User::factory()->unverified()->create();

        // Act
        $response = $this
            ->actingAs($user)
            ->get('/verify-email');

        // Assert
        $response->assertOk();
    }

    #[Test]
    public function 正しい認証URLへアクセスした時_メール認証が完了してマイページへ遷移する(): void
    {
        // Arrange
        $user = User::factory()->unverified()->create();

        Event::fake();

        $verificationUrl = URL::temporarySignedRoute(
            'verification.verify',
            now()->addMinutes(60),
            [
                'id' => $user->id,
                'hash' => sha1($user->email),
            ]
        );

        // Act
        $response = $this
            ->actingAs($user)
            ->get($verificationUrl);

        // Assert
        Event::assertDispatched(Verified::class);
        $this->assertTrue($user->fresh()->hasVerifiedEmail());
        $response->assertRedirect('/mypage');
    }

    #[Test]
    public function 不正なハッシュの認証URLへアクセスした時_メール認証は完了しない(): void
    {
        // Arrange
        $user = User::factory()->unverified()->create();

        $verificationUrl = URL::temporarySignedRoute(
            'verification.verify',
            now()->addMinutes(60),
            [
                'id' => $user->id,
                'hash' => sha1('wrong-email'),
            ]
        );

        // Act
        $this
            ->actingAs($user)
            ->get($verificationUrl);

        // Assert
        $this->assertFalse($user->fresh()->hasVerifiedEmail());
    }
}
