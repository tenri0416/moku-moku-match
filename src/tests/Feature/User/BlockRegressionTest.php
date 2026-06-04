<?php

namespace Tests\Feature\User;

use App\Models\Block;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class BlockRegressionTest extends TestCase
{
    use RefreshDatabase;

    private User $user;

    private User $targetUser;

    protected function setUp(): void
    {
        parent::setUp();

        $this->user = User::factory()->create([
            'email_verified_at' => now(),
        ]);

        $this->targetUser = User::factory()->create([
            'email_verified_at' => now(),
        ]);
    }

    #[Test]
    public function ブロック作成で_他ユーザーをブロックできる(): void
    {
        // Arrange

        // Act
        $response = $this
            ->actingAs($this->user)
            ->post(route('blocks.store', $this->targetUser));

        // Assert
        $response->assertRedirect();
        $response->assertSessionHas('success', 'ユーザーをブロックしました。');

        $this->assertDatabaseHas('blocks', [
            'blocker_id' => $this->user->id,
            'blocked_user_id' => $this->targetUser->id,
        ]);
    }

    #[Test]
    public function ブロック作成で_同じユーザーを重複ブロックしても1件だけ保存される(): void
    {
        // Arrange
        Block::create([
            'blocker_id' => $this->user->id,
            'blocked_user_id' => $this->targetUser->id,
        ]);

        // Act
        $response = $this
            ->actingAs($this->user)
            ->post(route('blocks.store', $this->targetUser));

        // Assert
        $response->assertRedirect();

        $this->assertSame(
            1,
            Block::where('blocker_id', $this->user->id)
                ->where('blocked_user_id', $this->targetUser->id)
                ->count()
        );
    }

    #[Test]
    public function ブロック作成で_自分自身を指定した場合_403になる(): void
    {
        // Arrange

        // Act
        $response = $this
            ->actingAs($this->user)
            ->post(route('blocks.store', $this->user));

        // Assert
        $response->assertForbidden();

        $this->assertDatabaseMissing('blocks', [
            'blocker_id' => $this->user->id,
            'blocked_user_id' => $this->user->id,
        ]);
    }

    #[Test]
    public function ブロック解除で_ブロック済みユーザーを解除できる(): void
    {
        // Arrange
        Block::create([
            'blocker_id' => $this->user->id,
            'blocked_user_id' => $this->targetUser->id,
        ]);

        // Act
        $response = $this
            ->actingAs($this->user)
            ->delete(route('blocks.destroy', $this->targetUser));

        // Assert
        $response->assertRedirect();
        $response->assertSessionHas('success', 'ブロックを解除しました。');

        $this->assertDatabaseMissing('blocks', [
            'blocker_id' => $this->user->id,
            'blocked_user_id' => $this->targetUser->id,
        ]);
    }

    #[Test]
    public function 未ログインでブロック作成した場合_ログイン画面へリダイレクトされる(): void
    {
        // Arrange

        // Act
        $response = $this->post(route('blocks.store', $this->targetUser));

        // Assert
        $response->assertRedirect(route('login'));
    }
}
