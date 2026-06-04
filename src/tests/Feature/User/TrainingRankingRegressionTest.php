<?php

namespace Tests\Feature\User;

use App\Models\User;
use App\Models\UserTrainingPointHistory;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class TrainingRankingRegressionTest extends TestCase
{
    use RefreshDatabase;

    private User $loginUser;

    protected function setUp(): void
    {
        parent::setUp();

        $this->loginUser = User::factory()->create([
            'name' => 'ログインユーザー',
            'email_verified_at' => now(),
        ]);
    }

    #[Test]
    public function ランキング画面へアクセスした時_正常に表示できる(): void
    {
        // Arrange

        // Act
        $response = $this
            ->actingAs($this->loginUser)
            ->get(route('trainings.ranking'));

        // Assert
        $response->assertOk();
        $response->assertViewIs('trainings.ranking');
    }

    #[Test]
    public function ランキング画面で_月間ポイントが多い順に月間ランキングが表示される(): void
    {
        // Arrange
        $firstUser = User::factory()->create(['email_verified_at' => now()]);
        $secondUser = User::factory()->create(['email_verified_at' => now()]);
        $thirdUser = User::factory()->create(['email_verified_at' => now()]);

        $this->ポイント履歴を作成する($secondUser, 30, now()->startOfMonth()->addDay());
        $this->ポイント履歴を作成する($firstUser, 50, now()->startOfMonth()->addDays(2));
        $this->ポイント履歴を作成する($thirdUser, 10, now()->startOfMonth()->addDays(3));

        // Act
        $response = $this
            ->actingAs($this->loginUser)
            ->get(route('trainings.ranking'));

        // Assert
        $response->assertOk();

        $monthlyRankings = $response->viewData('monthlyRankings');

        $this->assertSame(
            [$firstUser->id, $secondUser->id, $thirdUser->id],
            $monthlyRankings->pluck('user_id')->values()->all()
        );

        $this->assertSame(50, (int) $monthlyRankings[0]->total_points);
        $this->assertSame(30, (int) $monthlyRankings[1]->total_points);
        $this->assertSame(10, (int) $monthlyRankings[2]->total_points);
    }

    #[Test]
    public function ランキング画面で_前月のポイントは月間ランキングに含まれない(): void
    {
        // Arrange
        $currentMonthUser = User::factory()->create(['email_verified_at' => now()]);
        $lastMonthUser = User::factory()->create(['email_verified_at' => now()]);

        $this->ポイント履歴を作成する($currentMonthUser, 10, now()->startOfMonth()->addDay());
        $this->ポイント履歴を作成する($lastMonthUser, 100, now()->subMonth()->startOfMonth()->addDay());

        // Act
        $response = $this
            ->actingAs($this->loginUser)
            ->get(route('trainings.ranking'));

        // Assert
        $response->assertOk();

        $monthlyUserIds = $response->viewData('monthlyRankings')
            ->pluck('user_id')
            ->values()
            ->all();

        $this->assertContains($currentMonthUser->id, $monthlyUserIds);
        $this->assertNotContains($lastMonthUser->id, $monthlyUserIds);
    }

    #[Test]
    public function ランキング画面で_総ポイントが多い順に総合ランキングが表示される(): void
    {
        // Arrange
        $firstUser = User::factory()->create(['email_verified_at' => now()]);
        $secondUser = User::factory()->create(['email_verified_at' => now()]);
        $thirdUser = User::factory()->create(['email_verified_at' => now()]);

        $this->ポイント履歴を作成する($thirdUser, 10, now()->subMonth());
        $this->ポイント履歴を作成する($secondUser, 30, now()->subMonth());
        $this->ポイント履歴を作成する($firstUser, 50, now()->startOfMonth()->addDay());

        // Act
        $response = $this
            ->actingAs($this->loginUser)
            ->get(route('trainings.ranking'));

        // Assert
        $response->assertOk();

        $totalRankings = $response->viewData('totalRankings');

        $this->assertSame(
            [$firstUser->id, $secondUser->id, $thirdUser->id],
            $totalRankings->pluck('user_id')->values()->all()
        );

        $this->assertSame(50, (int) $totalRankings[0]->total_points);
        $this->assertSame(30, (int) $totalRankings[1]->total_points);
        $this->assertSame(10, (int) $totalRankings[2]->total_points);
    }

    #[Test]
    public function ランキング画面で_同一ユーザーの複数ポイント履歴は合算される(): void
    {
        // Arrange
        $user = User::factory()->create(['email_verified_at' => now()]);

        $this->ポイント履歴を作成する($user, 10, now()->startOfMonth()->addDay());
        $this->ポイント履歴を作成する($user, 8, now()->startOfMonth()->addDays(2));
        $this->ポイント履歴を作成する($user, 6, now()->startOfMonth()->addDays(3));

        // Act
        $response = $this
            ->actingAs($this->loginUser)
            ->get(route('trainings.ranking'));

        // Assert
        $response->assertOk();

        $monthlyRanking = $response->viewData('monthlyRankings')
            ->firstWhere('user_id', $user->id);

        $totalRanking = $response->viewData('totalRankings')
            ->firstWhere('user_id', $user->id);

        $this->assertSame(24, (int) $monthlyRanking->total_points);
        $this->assertSame(3, (int) $monthlyRanking->training_count);

        $this->assertSame(24, (int) $totalRanking->total_points);
        $this->assertSame(3, (int) $totalRanking->training_count);
    }

    #[Test]
    public function ランキング画面で_ランキング表示件数は20件までになる(): void
    {
        // Arrange
        foreach (range(1, 25) as $index) {
            $user = User::factory()->create([
                'email_verified_at' => now(),
            ]);

            $this->ポイント履歴を作成する(
                user: $user,
                points: $index,
                earnedOn: now()->startOfMonth()->addDay()
            );
        }

        // Act
        $response = $this
            ->actingAs($this->loginUser)
            ->get(route('trainings.ranking'));

        // Assert
        $response->assertOk();

        $this->assertCount(20, $response->viewData('monthlyRankings'));
        $this->assertCount(20, $response->viewData('totalRankings'));
    }

    #[Test]
    public function ランキング画面で_ポイント履歴がない場合_空のランキングが表示される(): void
    {
        // Arrange

        // Act
        $response = $this
            ->actingAs($this->loginUser)
            ->get(route('trainings.ranking'));

        // Assert
        $response->assertOk();
        $this->assertCount(0, $response->viewData('monthlyRankings'));
        $this->assertCount(0, $response->viewData('totalRankings'));
    }

    #[Test]
    public function 未ログインでランキング画面へアクセスした場合_ログイン画面へリダイレクトされる(): void
    {
        // Arrange

        // Act
        $response = $this->get(route('trainings.ranking'));

        // Assert
        $response->assertRedirect(route('login'));
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
}
