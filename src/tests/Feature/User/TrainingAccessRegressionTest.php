<?php

namespace Tests\Feature\User;

use App\Models\User;
use App\Models\UserSummaryTraining;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\Attributes\DataProvider;


class TrainingAccessRegressionTest extends TestCase
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
    public function トレーニング詳細で_自分のトレーニングなら表示できる(): void
    {
        // Arrange
        $training = $this->要約力トレーニングを作成する($this->user);

        // Act
        $response = $this
            ->actingAs($this->user)
            ->get(route('trainings.show', [
                'type' => UserSummaryTraining::TYPE,
                'id' => $training->id,
            ]));

        // Assert
        $response->assertOk();
    }

    #[Test]
    public function トレーニング詳細で_他人のトレーニングなら403になる(): void
    {
        // Arrange
        $otherUser = User::factory()->create([
            'email_verified_at' => now(),
        ]);

        $training = $this->要約力トレーニングを作成する($otherUser);

        // Act
        $response = $this
            ->actingAs($this->user)
            ->get(route('trainings.show', [
                'type' => UserSummaryTraining::TYPE,
                'id' => $training->id,
            ]));

        // Assert
        $response->assertForbidden();
    }

    #[Test]
    public function トレーニング詳細で_存在しない種別を指定した場合_404になる(): void
    {
        // Arrange

        // Act
        $response = $this
            ->actingAs($this->user)
            ->get(route('trainings.show', [
                'type' => 'invalid-training-type',
                'id' => 1,
            ]));

        // Assert
        $response->assertNotFound();
    }

    #[Test]
    public function 要約力トレーニング回答保存で_他人のトレーニングなら403になる(): void
    {
        // Arrange
        $otherUser = User::factory()->create([
            'email_verified_at' => now(),
        ]);

        $training = $this->要約力トレーニングを作成する($otherUser);

        // Act
        $response = $this
            ->actingAs($this->user)
            ->post(route('trainings.summary.store', $training), [
                'answer_body' => '他人のトレーニングに回答しようとしています。',
            ]);

        // Assert
        $response->assertForbidden();

        $training->refresh();

        $this->assertNull($training->answer_body);
    }

    #[Test]
    public function 未ログインでトレーニング一覧へアクセスした場合_ログイン画面へリダイレクトされる(): void
    {
        // Arrange

        // Act
        $response = $this->get(route('trainings.index'));

        // Assert
        $response->assertRedirect(route('login'));
    }

    public function 未ログインで要約力トレーニング作成へアクセスした場合_ログイン画面へリダイレクトされる(): void
    {
        // Arrange

        // Act
        $response = $this->get(route('trainings.summary.create'));

        // Assert
        $response->assertRedirect(route('login'));
    }

    #[Test]
    private function 要約力トレーニングを作成する(User $user): UserSummaryTraining
    {
        return UserSummaryTraining::create([
            'user_id' => $user->id,
            'training_date' => today(),
            'question_title' => '150文字以内で要約してください',
            'question_body' => '要約対象本文',
            'model_answer' => '模範解答',
            'answer_point' => '回答ポイント',
            'answer_body' => null,
            'ai_provider' => 'local',
            'ai_model' => 'laravel-rule-based',
            'ai_status' => 'success',
            'is_fallback' => true,
            'ai_attempts' => 1,
        ]);
    }

    #[Test]
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
