<?php

namespace Tests\Feature\User;

use App\Models\Application;
use App\Models\User;
use App\Models\WorkPost;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class ApplicationRegressionTest extends TestCase
{
    use RefreshDatabase;

    private User $owner;

    private User $applicant;

    protected function setUp(): void
    {
        parent::setUp();

        $this->owner = User::factory()->create([
            'email_verified_at' => now(),
        ]);

        $this->applicant = User::factory()->create([
            'email_verified_at' => now(),
        ]);

        $this->プロフィールを作成する($this->owner);
        $this->プロフィールを作成する($this->applicant);
    }

    #[Test]
    public function 参加申請作成画面で_申請可能な募集なら表示できる(): void
    {
        // Arrange
        $workPost = $this->募集を作成する($this->owner, WorkPost::STATUS_OPEN);

        // Act
        $response = $this
            ->actingAs($this->applicant)
            ->get(route('applications.create', $workPost));

        // Assert
        $response->assertOk();
        $response->assertViewIs('applications.create');
    }

    #[Test]
    public function 参加申請で_申請可能な募集なら申請を保存できる(): void
    {
        // Arrange
        $workPost = $this->募集を作成する($this->owner, WorkPost::STATUS_OPEN);

        // Act
        $response = $this
            ->actingAs($this->applicant)
            ->post(route('applications.store', $workPost), [
                'message' => '参加希望です。よろしくお願いします。',
            ]);

        // Assert
        $response->assertRedirect(route('work-posts.show', $workPost));
        $response->assertSessionHas('success', '参加申請を送信しました。');

        $this->assertDatabaseHas('applications', [
            'work_post_id' => $workPost->id,
            'user_id' => $this->applicant->id,
            'status' => Application::STATUS_PENDING,
        ]);
    }

    #[Test]
    public function 参加申請で_プロフィール未登録の場合_申請できない(): void
    {
        // Arrange
        $noProfileUser = User::factory()->create([
            'email_verified_at' => now(),
        ]);

        $workPost = $this->募集を作成する($this->owner, WorkPost::STATUS_OPEN);

        // Act
        $response = $this
            ->actingAs($noProfileUser)
            ->post(route('applications.store', $workPost), [
                'message' => 'プロフィール未登録で申請します。',
            ]);

        // Assert
        $response->assertRedirect(route('work-posts.show', $workPost));
        $response->assertSessionHas('error', '参加申請をするには、先にプロフィール登録が必要です。');

        $this->assertDatabaseMissing('applications', [
            'work_post_id' => $workPost->id,
            'user_id' => $noProfileUser->id,
        ]);
    }

    #[Test]
    public function 参加申請で_自分の募集には申請できない(): void
    {
        // Arrange
        $workPost = $this->募集を作成する($this->owner, WorkPost::STATUS_OPEN);

        // Act
        $response = $this
            ->actingAs($this->owner)
            ->post(route('applications.store', $workPost), [
                'message' => '自分の募集に申請します。',
            ]);

        // Assert
        $response->assertRedirect(route('work-posts.show', $workPost));
        $response->assertSessionHas('error', '自分が作成した募集には参加申請できません。');

        $this->assertDatabaseMissing('applications', [
            'work_post_id' => $workPost->id,
            'user_id' => $this->owner->id,
        ]);
    }

    #[Test]
    public function 参加申請で_同じ募集へ重複申請できない(): void
    {
        // Arrange
        $workPost = $this->募集を作成する($this->owner, WorkPost::STATUS_OPEN);

        $this->申請を作成する($workPost, $this->applicant, Application::STATUS_PENDING);

        // Act
        $response = $this
            ->actingAs($this->applicant)
            ->post(route('applications.store', $workPost), [
                'message' => '2回目の申請です。',
            ]);

        // Assert
        $response->assertRedirect(route('work-posts.show', $workPost));
        $response->assertSessionHas('error', 'この募集にはすでに参加申請済みです。');

        $this->assertSame(
            1,
            Application::where('work_post_id', $workPost->id)
                ->where('user_id', $this->applicant->id)
                ->count()
        );
    }

    #[Test]
    public function 参加申請一覧で_募集オーナーなら申請一覧を確認できる(): void
    {
        // Arrange
        $workPost = $this->募集を作成する($this->owner, WorkPost::STATUS_OPEN);
        $application = $this->申請を作成する($workPost, $this->applicant, Application::STATUS_PENDING);

        // Act
        $response = $this
            ->actingAs($this->owner)
            ->get(route('applications.index', $workPost));

        // Assert
        $response->assertOk();
        $response->assertViewIs('applications.index');
        $this->assertTrue($response->viewData('applications')->pluck('id')->contains($application->id));
    }

    #[Test]
    public function 参加申請承認で_募集オーナーなら承認できる(): void
    {
        // Arrange
        $workPost = $this->募集を作成する($this->owner, WorkPost::STATUS_OPEN);
        $application = $this->申請を作成する($workPost, $this->applicant, Application::STATUS_PENDING);

        // Act
        $response = $this
            ->actingAs($this->owner)
            ->patch(route('applications.approve', $application));

        // Assert
        $response->assertRedirect();
        $this->assertSame(Application::STATUS_APPROVED, $application->fresh()->status);
    }

    #[Test]
    public function 参加申請却下で_募集オーナーなら却下できる(): void
    {
        // Arrange
        $workPost = $this->募集を作成する($this->owner, WorkPost::STATUS_OPEN);
        $application = $this->申請を作成する($workPost, $this->applicant, Application::STATUS_PENDING);

        // Act
        $response = $this
            ->actingAs($this->owner)
            ->patch(route('applications.reject', $application));

        // Assert
        $response->assertRedirect();
        $this->assertSame(Application::STATUS_REJECTED, $application->fresh()->status);
    }

    #[Test]
    public function 未ログインで参加申請作成へアクセスした場合_ログイン画面へリダイレクトされる(): void
    {
        // Arrange
        $workPost = $this->募集を作成する($this->owner, WorkPost::STATUS_OPEN);

        // Act
        $response = $this->get(route('applications.create', $workPost));

        // Assert
        $response->assertRedirect(route('login'));
    }

    private function プロフィールを作成する(User $user): void
    {
        $user->profile()->create([
            'display_name' => $user->name,
            'job_type' => 'Laravelエンジニア',
            'skills' => 'PHP, Laravel',
            'bio' => 'テスト用プロフィールです。',
            'purpose' => '作業仲間を探したい',
            'work_style' => '夜に作業したい',
        ]);
    }

    private function 募集を作成する(User $user, int $status): WorkPost
    {
        $workPost = new WorkPost();
        $workPost->user_id = $user->id;
        $workPost->title = '申請テスト用募集';
        $workPost->body = '申請テスト用募集本文です。';
        $workPost->purpose = 'work';
        $workPost->location_type = 'online';
        $workPost->meeting_tool = 'Zoom';
        $workPost->time_zone = 'night';
        $workPost->max_participants = 3;
        $workPost->status = $status;
        $workPost->save();

        return $workPost;
    }

    private function 申請を作成する(WorkPost $workPost, User $user, int $status): Application
    {
        $application = new Application();
        $application->work_post_id = $workPost->id;
        $application->user_id = $user->id;
        $application->message = 'テスト申請メッセージです。';
        $application->status = $status;
        $application->save();

        return $application;
    }
}
