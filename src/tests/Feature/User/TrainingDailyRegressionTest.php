<?php

namespace Tests\Feature\User;

use App\Models\User;
use App\Models\UserChallengeTraining;
use App\Models\UserDiaryTraining;
use App\Models\UserTrainingPointHistory;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Http;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class TrainingDailyRegressionTest extends TestCase
{
    use RefreshDatabase;

    private User $user;

    protected function setUp(): void
    {
        parent::setUp();

        Http::preventStrayRequests();

        $this->AIプロバイダーを未設定にする();

        $this->user = User::factory()->create([
            'email_verified_at' => now(),
        ]);
    }

    #[Test]
    public function トレーニング一覧へアクセスした時_正常に表示できる(): void
    {
        // Arrange

        // Act
        $response = $this
            ->actingAs($this->user)
            ->get(route('trainings.index'));

        // Assert
        $response->assertOk();
    }

    #[Test]
    public function 日記トレーニング作成画面へアクセスした時_未実施なら正常に表示できる(): void
    {
        // Arrange

        // Act
        $response = $this
            ->actingAs($this->user)
            ->get(route('trainings.diary.create'));

        // Assert
        $response->assertOk();
    }

    #[Test]
    public function 日記トレーニング保存で_正しい入力なら採点結果とポイント履歴が保存される(): void
    {
        // Arrange
        $trainingDate = today()->toDateString();

        // Act
        $response = $this
            ->actingAs($this->user)
            ->post(route('trainings.diary.store'), [
                'training_date' => $trainingDate,
                'diary_body' => '今日はLaravelのテストコードを書きました。最初は難しかったですが、AAAを意識すると整理しやすくなりました。',
            ]);

        // Assert
        $training = UserDiaryTraining::query()
            ->where('user_id', $this->user->id)
            ->whereDate('training_date', $trainingDate)
            ->first();

        $this->assertNotNull($training);

        $response->assertRedirect(route('trainings.show', [
            'type' => UserDiaryTraining::TYPE,
            'id' => $training->id,
        ]));

        $this->assertSame('今日はLaravelのテストコードを書きました。最初は難しかったですが、AAAを意識すると整理しやすくなりました。', $training->diary_body);
        $this->assertNotNull($training->total_score);
        $this->assertNotNull($training->good_point);
        $this->assertNotNull($training->improvement_point);
        $this->assertNotNull($training->next_task);
        $this->assertGreaterThanOrEqual(1, (int) $training->earned_points);
        $this->assertLessThanOrEqual(10, (int) $training->earned_points);

        $this->assertDatabaseHas('user_training_point_histories', [
            'user_id' => $this->user->id,
            'training_type' => UserDiaryTraining::TYPE,
            'training_id' => $training->id,
            'point_type' => 'training',
            'points' => $training->earned_points,
        ]);

        $this->assertTrue(
            UserTrainingPointHistory::query()
                ->where('user_id', $this->user->id)
                ->where('training_type', UserDiaryTraining::TYPE)
                ->where('training_id', $training->id)
                ->whereDate('earned_on', $trainingDate)
                ->exists()
        );
    }

    #[Test]
    public function 日記トレーニング保存で_同じ日付が実施済みなら保存されない(): void
    {
        // Arrange
        $trainingDate = today()->toDateString();

        UserDiaryTraining::create([
            'user_id' => $this->user->id,
            'training_date' => $trainingDate,
            'diary_body' => 'すでに保存済みの日記です。',
            'total_score' => 80,
            'readability_score' => 20,
            'specificity_score' => 20,
            'structure_score' => 20,
            'expression_score' => 20,
            'good_point' => '良い点',
            'improvement_point' => '改善点',
            'next_task' => '次回の課題',
            'earned_points' => 8,
            'ai_provider' => 'local',
            'ai_model' => 'laravel-rule-based',
            'ai_status' => 'success',
            'is_fallback' => true,
            'ai_attempts' => 1,
        ]);

        // Act
        $response = $this
            ->actingAs($this->user)
            ->from(route('trainings.diary.create'))
            ->post(route('trainings.diary.store'), [
                'training_date' => $trainingDate,
                'diary_body' => '二回目の日記です。',
            ]);

        // Assert
        $response->assertRedirect(route('trainings.diary.create'));

        $this->assertSame(
            1,
            UserDiaryTraining::where('user_id', $this->user->id)
                ->whereDate('training_date', $trainingDate)
                ->count()
        );
    }

    #[Test]
    public function 日記トレーニング保存で_本文が空ならバリデーションエラーになる(): void
    {
        // Arrange

        // Act
        $response = $this
            ->actingAs($this->user)
            ->from(route('trainings.diary.create'))
            ->post(route('trainings.diary.store'), [
                'training_date' => today()->toDateString(),
                'diary_body' => '',
            ]);

        // Assert
        $response->assertRedirect(route('trainings.diary.create'));
        $response->assertSessionHasErrors(['diary_body']);

        $this->assertSame(0, UserDiaryTraining::where('user_id', $this->user->id)->count());
    }

    #[Test]
    public function 今日のチャレンジ作成画面へアクセスした時_未実施なら正常に表示できる(): void
    {
        // Arrange

        // Act
        $response = $this
            ->actingAs($this->user)
            ->get(route('trainings.challenge.create'));

        // Assert
        $response->assertOk();
    }

    #[Test]
    public function 今日のチャレンジ保存で_正しい入力なら採点結果とポイント履歴が保存される(): void
    {
        // Arrange
        $trainingDate = today()->toDateString();

        // Act
        $response = $this
            ->actingAs($this->user)
            ->post(route('trainings.challenge.store'), [
                'training_date' => $trainingDate,
                'challenged_thing' => 'Featureテストを作成した',
                'completed_thing' => 'ログイン後のリダイレクト確認ができた',
                'difficult_thing' => 'テストDBと本番DBの違いを理解するのが難しかった',
                'next_improvement' => '次回はテスト目的ごとにファイルを分ける',
            ]);

        // Assert
        $training = UserChallengeTraining::query()
            ->where('user_id', $this->user->id)
            ->whereDate('training_date', $trainingDate)
            ->first();

        $this->assertNotNull($training);

        $response->assertRedirect(route('trainings.show', [
            'type' => UserChallengeTraining::TYPE,
            'id' => $training->id,
        ]));

        $this->assertSame('Featureテストを作成した', $training->challenged_thing);
        $this->assertSame('ログイン後のリダイレクト確認ができた', $training->completed_thing);
        $this->assertSame('テストDBと本番DBの違いを理解するのが難しかった', $training->difficult_thing);
        $this->assertSame('次回はテスト目的ごとにファイルを分ける', $training->next_improvement);
        $this->assertNotNull($training->total_score);
        $this->assertGreaterThanOrEqual(1, (int) $training->earned_points);
        $this->assertLessThanOrEqual(10, (int) $training->earned_points);

        $this->assertDatabaseHas('user_training_point_histories', [
            'user_id' => $this->user->id,
            'training_type' => UserChallengeTraining::TYPE,
            'training_id' => $training->id,
            'point_type' => 'training',
            'points' => $training->earned_points,
        ]);

        $this->assertTrue(
            UserTrainingPointHistory::query()
                ->where('user_id', $this->user->id)
                ->where('training_type', UserChallengeTraining::TYPE)
                ->where('training_id', $training->id)
                ->whereDate('earned_on', $trainingDate)
                ->exists()
        );
    }

    #[Test]
    public function 今日のチャレンジ保存で_必須項目が空ならバリデーションエラーになる(): void
    {
        // Arrange

        // Act
        $response = $this
            ->actingAs($this->user)
            ->from(route('trainings.challenge.create'))
            ->post(route('trainings.challenge.store'), [
                'training_date' => today()->toDateString(),
                'challenged_thing' => '',
                'completed_thing' => '',
                'difficult_thing' => '',
                'next_improvement' => '',
            ]);

        // Assert
        $response->assertRedirect(route('trainings.challenge.create'));
        $response->assertSessionHasErrors([
            'challenged_thing',
            'completed_thing',
            'difficult_thing',
            'next_improvement',
        ]);

        $this->assertSame(0, UserChallengeTraining::where('user_id', $this->user->id)->count());
    }

    private function AIプロバイダーを未設定にする(): void
    {
        Config::set('services.google_ai.api_key', null);
        Config::set('services.google_ai.model', null);

        Config::set('services.openrouter.api_key', null);
        Config::set('services.openrouter.model', null);

        Config::set('services.groq.api_key', null);
        Config::set('services.groq.model', null);

        Config::set('services.ai.max_output_tokens', 500);
    }
}
