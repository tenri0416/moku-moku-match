<?php

namespace Tests\Feature\User;

use App\Models\Message;
use App\Models\Prefecture;
use App\Models\User;
use App\Models\UserTrainingPointHistory;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Route;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class UserProfileRegressionTest extends TestCase
{
    use RefreshDatabase;

    private User $loginUser;

    private User $targetUser;

    protected function setUp(): void
    {
        parent::setUp();

        $this->loginUser = User::factory()->create([
            'name' => 'ログインユーザー',
            'email_verified_at' => now(),
        ]);

        $this->targetUser = User::factory()->create([
            'name' => '対象ユーザー',
            'email_verified_at' => now(),
        ]);
    }

    #[Test]
    public function プロフィール公開画面で_対象ユーザーのプロフィールとポイント情報が表示用データに渡される(): void
    {
        // Arrange
        $prefecture = $this->都道府県を作成する();

        $this->targetUser->profile()->create([
            'display_name' => '公開プロフィール太郎',
            'job_type' => 'Laravelエンジニア',
            'prefecture_id' => $prefecture->id,
            'skills' => 'PHP, Laravel, Docker',
            'bio' => 'フルリモートで開発しています。',
            'purpose' => '黙々作業仲間を探したい',
            'work_style' => '平日夜に作業したい',
        ]);

        $this->ポイント履歴を作成する(
            user: $this->targetUser,
            points: 10,
            earnedOn: now()->startOfMonth()->addDay()
        );

        $this->ポイント履歴を作成する(
            user: $this->targetUser,
            points: 8,
            earnedOn: now()->startOfMonth()->addDays(2)
        );

        $this->ポイント履歴を作成する(
            user: $this->targetUser,
            points: 6,
            earnedOn: now()->subMonth()
        );

        // Act
        $response = $this
            ->actingAs($this->loginUser)
            ->get($this->プロフィール公開URL($this->targetUser));

        // Assert
        $response->assertOk();
        $response->assertViewIs('users.profiles.show');

        $this->assertSame($this->targetUser->id, $response->viewData('user')->id);
        $this->assertSame('公開プロフィール太郎', $response->viewData('profile')->display_name);
        $this->assertSame(24, (int) $response->viewData('totalPoints'));
        $this->assertSame(18, (int) $response->viewData('monthlyPoints'));
        $this->assertSame(3, (int) $response->viewData('trainingCount'));
        $this->assertFalse($response->viewData('isMine'));
    }

    #[Test]
    public function プロフィール公開画面で_自分自身を表示した場合_isMineがtrueになる(): void
    {
        // Arrange
        $this->loginUser->profile()->create([
            'display_name' => '自分のプロフィール',
            'job_type' => 'バックエンドエンジニア',
            'skills' => 'Laravel',
            'bio' => '自分の紹介文です。',
            'purpose' => '学習仲間を探したい',
            'work_style' => '朝活したい',
        ]);

        // Act
        $response = $this
            ->actingAs($this->loginUser)
            ->get($this->プロフィール公開URL($this->loginUser));

        // Assert
        $response->assertOk();
        $this->assertTrue($response->viewData('isMine'));
        $this->assertSame($this->loginUser->id, $response->viewData('user')->id);
    }

    #[Test]
    public function プロフィール公開画面で_プロフィール未作成のユーザーでも表示できる(): void
    {
        // Arrange

        // Act
        $response = $this
            ->actingAs($this->loginUser)
            ->get($this->プロフィール公開URL($this->targetUser));

        // Assert
        $response->assertOk();
        $this->assertSame($this->targetUser->id, $response->viewData('user')->id);
        $this->assertNull($response->viewData('profile'));
        $this->assertSame(0, (int) $response->viewData('totalPoints'));
        $this->assertSame(0, (int) $response->viewData('monthlyPoints'));
        $this->assertSame(0, (int) $response->viewData('trainingCount'));
        $this->assertFalse($response->viewData('isMine'));
    }

    #[Test]
    public function プロフィール公開画面で_他ユーザーへ直接メッセージ送信できる導線の送信先として利用できる(): void
    {
        // Arrange

        // Act
        $response = $this
            ->actingAs($this->loginUser)
            ->post(route('messages.users.store', $this->targetUser), [
                'body' => 'プロフィールを見てメッセージしました。',
            ]);

        // Assert
        $response->assertRedirect(route('messages.users.show', $this->targetUser));

        $this->assertDatabaseHas('messages', [
            'sender_id' => $this->loginUser->id,
            'receiver_id' => $this->targetUser->id,
            'body' => 'プロフィールを見てメッセージしました。',
        ]);
    }

    #[Test]
    public function プロフィール公開画面で_存在しないユーザーを指定した場合_404になる(): void
    {
        // Arrange

        // Act
        $response = $this
            ->actingAs($this->loginUser)
            ->get($this->プロフィール公開URL(999999));

        // Assert
        $response->assertNotFound();
    }

    #[Test]
    public function 未ログインでプロフィール公開画面へアクセスした場合_表示できる(): void
    {
        // Arrange
        $this->targetUser->profile()->create([
            'display_name' => 'ゲスト閲覧可能ユーザー',
            'job_type' => 'Webエンジニア',
            'skills' => 'PHP',
            'bio' => 'ゲストでも閲覧できるプロフィールです。',
            'purpose' => '作業仲間を探したい',
            'work_style' => '夜に作業したい',
        ]);

        // Act
        $response = $this->get($this->プロフィール公開URL($this->targetUser));

        // Assert
        $response->assertOk();
        $this->assertSame($this->targetUser->id, $response->viewData('user')->id);
        $this->assertFalse($response->viewData('isMine'));
    }

    private function ポイント履歴を作成する(User $user, int $points, mixed $earnedOn): UserTrainingPointHistory
    {
        return UserTrainingPointHistory::create([
            'user_id' => $user->id,
            'training_type' => 'summary',
            'training_id' => random_int(1, 999999),
            'point_type' => 'training',
            'points' => $points,
            'earned_on' => $earnedOn,
            'note' => 'テスト用ポイント',
        ]);
    }

    private function 都道府県を作成する(): Prefecture
    {
        $prefecture = new Prefecture();
        $prefecture->slug = 'tokyo';
        $prefecture->name = '東京都';
        $prefecture->save();

        return $prefecture;
    }

    private function プロフィール公開URL(User|int $user): string
    {
        $id = $user instanceof User ? $user->id : $user;

        if (Route::has('users.profiles.show')) {
            return route('users.profiles.show', $id);
        }

        if (Route::has('users.profile.show')) {
            return route('users.profile.show', $id);
        }

        if (Route::has('users.show')) {
            return route('users.show', $id);
        }

        if (Route::has('profiles.show')) {
            return route('profiles.show', $id);
        }

        return "/users/{$id}/profile";
    }
}
