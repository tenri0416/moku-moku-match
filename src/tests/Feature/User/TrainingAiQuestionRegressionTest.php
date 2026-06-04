<?php

namespace Tests\Feature\User;

use App\Models\User;
use App\Models\UserAbstractionTraining;
use App\Models\UserConcretizationTraining;
use App\Models\UserSummaryTraining;
use App\Models\UserTrainingPointHistory;
use App\Models\UserVerbalizationTraining;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Http;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class TrainingAiQuestionRegressionTest extends TestCase
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
    #[DataProvider('AI出題型トレーニング一覧')]
    public function AI出題型トレーニング作成で_AIが利用できない場合_Laravel固定問題と模範解答が保存される(
        string $type,
        string $modelClass,
        string $createRouteName,
    ): void {
        // Arrange

        // Act
        $response = $this
            ->actingAs($this->user)
            ->get(route($createRouteName));

        // Assert
        $response->assertOk();

        $training = $modelClass::query()
            ->where('user_id', $this->user->id)
            ->whereDate('training_date', today())
            ->first();

        $this->assertNotNull($training);
        $this->assertSame($type, $training::TYPE);
        $this->assertNotEmpty($training->question_title);
        $this->assertNotEmpty($training->question_body);
        $this->assertNotEmpty($training->model_answer);
        $this->assertNotEmpty($training->answer_point);
        $this->assertSame('local', $training->ai_provider);
        $this->assertSame('laravel-rule-based', $training->ai_model);
        $this->assertSame('success', $training->ai_status);
        $this->assertTrue((bool) $training->is_fallback);
        $this->assertGreaterThanOrEqual(1, (int) $training->ai_attempts);
    }

    #[Test]
    #[DataProvider('AI出題型トレーニング一覧')]
    public function AI出題型トレーニング作成で_同じ日に未回答の問題がある場合_新しい問題は作成されない(
        string $type,
        string $modelClass,
        string $createRouteName,
    ): void {
        // Arrange
        $modelClass::create([
            'user_id' => $this->user->id,
            'training_date' => today(),
            'question_title' => '既存の問題タイトル',
            'question_body' => '既存の問題本文',
            'model_answer' => '既存の模範解答',
            'answer_point' => '既存の回答ポイント',
            'ai_provider' => 'local',
            'ai_model' => 'laravel-rule-based',
            'ai_status' => 'success',
            'is_fallback' => true,
            'ai_attempts' => 1,
        ]);

        // Act
        $response = $this
            ->actingAs($this->user)
            ->get(route($createRouteName));

        // Assert
        $response->assertOk();

        $this->assertSame(
            1,
            $modelClass::where('user_id', $this->user->id)
                ->whereDate('training_date', today())
                ->count()
        );
    }

    #[Test]
    #[DataProvider('AI出題型トレーニング一覧')]
    public function AI出題型トレーニング作成で_同じ日に回答済みの場合_詳細画面へリダイレクトされる(
        string $type,
        string $modelClass,
        string $createRouteName,
    ): void {
        // Arrange
        $training = $modelClass::create([
            'user_id' => $this->user->id,
            'training_date' => today(),
            'question_title' => '回答済み問題タイトル',
            'question_body' => '回答済み問題本文',
            'model_answer' => '回答済み模範解答',
            'answer_point' => '回答済み回答ポイント',
            'answer_body' => 'ユーザーの回答',
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
            ->get(route($createRouteName));

        // Assert
        $response->assertRedirect(route('trainings.show', [
            'type' => $type,
            'id' => $training->id,
        ]));

        $this->assertSame(
            1,
            $modelClass::where('user_id', $this->user->id)
                ->whereDate('training_date', today())
                ->count()
        );
    }

    #[Test]
    public function 要約力トレーニング回答保存で_正常な回答なら採点結果とポイント履歴が保存される(): void
    {
        // Arrange
        $trainingDate = today()->toDateString();

        $training = UserSummaryTraining::create([
            'user_id' => $this->user->id,
            'training_date' => $trainingDate,
            'question_title' => '150文字以内で要約してください',
            'question_body' => '在宅作業は自由度が高い一方で、集中が切れやすい課題があります。',
            'model_answer' => '在宅作業は自由に進められる反面、集中が切れやすいため、時間管理が重要である。',
            'answer_point' => '原因と対策を簡潔にまとめる',
            'ai_provider' => 'local',
            'ai_model' => 'laravel-rule-based',
            'ai_status' => 'success',
            'is_fallback' => true,
            'ai_attempts' => 1,
        ]);

        $answerBody = '在宅作業は自分のペースで進められる一方で、集中が切れやすいため、時間を決めて作業することが大切です。';

        // Act
        $response = $this
            ->actingAs($this->user)
            ->post(route('trainings.summary.store', $training), [
                'answer_body' => $answerBody,
            ]);

        // Assert
        $response->assertRedirect(route('trainings.show', [
            'type' => UserSummaryTraining::TYPE,
            'id' => $training->id,
        ]));

        $training->refresh();

        $this->assertSame($answerBody, $training->answer_body);
        $this->assertNotNull($training->total_score);
        $this->assertNotNull($training->good_point);
        $this->assertNotNull($training->improvement_point);
        $this->assertNotNull($training->next_task);
        $this->assertGreaterThanOrEqual(1, (int) $training->earned_points);
        $this->assertLessThanOrEqual(10, (int) $training->earned_points);

        $this->assertDatabaseHas('user_training_point_histories', [
            'user_id' => $this->user->id,
            'training_type' => UserSummaryTraining::TYPE,
            'training_id' => $training->id,
            'point_type' => 'training',
            'points' => $training->earned_points,
        ]);

        $this->assertTrue(
            UserTrainingPointHistory::query()
                ->where('user_id', $this->user->id)
                ->where('training_type', UserSummaryTraining::TYPE)
                ->where('training_id', $training->id)
                ->whereDate('earned_on', $trainingDate)
                ->exists()
        );
    }

    #[Test]
    public function 要約力トレーニング回答保存で_回答が空ならバリデーションエラーになる(): void
    {
        // Arrange
        $training = $this->要約力トレーニング問題を作成する();

        // Act
        $response = $this
            ->actingAs($this->user)
            ->from(route('trainings.summary.create'))
            ->post(route('trainings.summary.store', $training), [
                'answer_body' => '',
            ]);

        // Assert
        $response->assertRedirect(route('trainings.summary.create'));
        $response->assertSessionHasErrors(['answer_body']);

        $training->refresh();

        $this->assertNull($training->answer_body);
    }

    #[Test]
    public function 要約力トレーニング回答保存で_回答が5001文字ならバリデーションエラーになる(): void
    {
        // Arrange
        $training = $this->要約力トレーニング問題を作成する();

        // Act
        $response = $this
            ->actingAs($this->user)
            ->from(route('trainings.summary.create'))
            ->post(route('trainings.summary.store', $training), [
                'answer_body' => str_repeat('あ', 5001),
            ]);

        // Assert
        $response->assertRedirect(route('trainings.summary.create'));
        $response->assertSessionHasErrors(['answer_body']);

        $training->refresh();

        $this->assertNull($training->answer_body);
    }

    #[Test]
    public function 要約力トレーニング回答保存で_回答が5000文字なら保存できる(): void
    {
        // Arrange
        $training = $this->要約力トレーニング問題を作成する();

        $answerBody = str_repeat('あ', 5000);

        // Act
        $response = $this
            ->actingAs($this->user)
            ->post(route('trainings.summary.store', $training), [
                'answer_body' => $answerBody,
            ]);

        // Assert
        $response->assertRedirect(route('trainings.show', [
            'type' => UserSummaryTraining::TYPE,
            'id' => $training->id,
        ]));

        $training->refresh();

        $this->assertSame($answerBody, $training->answer_body);
    }

    #[Test]
    public function 要約力トレーニング回答保存で_回答済みの場合_回答内容とポイント履歴は増えない(): void
    {
        // Arrange
        $training = UserSummaryTraining::create([
            'user_id' => $this->user->id,
            'training_date' => today(),
            'question_title' => '回答済み問題タイトル',
            'question_body' => '回答済み問題本文',
            'model_answer' => '回答済み模範解答',
            'answer_point' => '回答済み回答ポイント',
            'answer_body' => '最初の回答',
            'total_score' => 70,
            'readability_score' => 18,
            'specificity_score' => 18,
            'structure_score' => 17,
            'expression_score' => 17,
            'good_point' => '最初の良い点',
            'improvement_point' => '最初の改善点',
            'next_task' => '最初の課題',
            'earned_points' => 7,
            'ai_provider' => 'local',
            'ai_model' => 'laravel-rule-based',
            'ai_status' => 'success',
            'is_fallback' => true,
            'ai_attempts' => 1,
        ]);

        // Act
        $response = $this
            ->actingAs($this->user)
            ->post(route('trainings.summary.store', $training), [
                'answer_body' => '二回目の回答',
            ]);

        // Assert
        $response->assertRedirect(route('trainings.show', [
            'type' => UserSummaryTraining::TYPE,
            'id' => $training->id,
        ]));

        $training->refresh();

        $this->assertSame('最初の回答', $training->answer_body);
        $this->assertSame(70, (int) $training->total_score);
        $this->assertSame(7, (int) $training->earned_points);

        $this->assertSame(
            0,
            UserTrainingPointHistory::where('user_id', $this->user->id)
                ->where('training_type', UserSummaryTraining::TYPE)
                ->where('training_id', $training->id)
                ->count()
        );
    }

    public static function AI出題型トレーニング一覧(): array
    {
        return [
            '要約力' => [
                UserSummaryTraining::TYPE,
                UserSummaryTraining::class,
                'trainings.summary.create',
            ],
            '言語化力' => [
                UserVerbalizationTraining::TYPE,
                UserVerbalizationTraining::class,
                'trainings.verbalization.create',
            ],
            '抽象化力' => [
                UserAbstractionTraining::TYPE,
                UserAbstractionTraining::class,
                'trainings.abstraction.create',
            ],
            '具体化力' => [
                UserConcretizationTraining::TYPE,
                UserConcretizationTraining::class,
                'trainings.concretization.create',
            ],
        ];
    }

    private function 要約力トレーニング問題を作成する(): UserSummaryTraining
    {
        return UserSummaryTraining::create([
            'user_id' => $this->user->id,
            'training_date' => today(),
            'question_title' => '150文字以内で要約してください',
            'question_body' => '要約対象本文',
            'model_answer' => '模範解答',
            'answer_point' => '回答ポイント',
            'ai_provider' => 'local',
            'ai_model' => 'laravel-rule-based',
            'ai_status' => 'success',
            'is_fallback' => true,
            'ai_attempts' => 1,
        ]);
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
