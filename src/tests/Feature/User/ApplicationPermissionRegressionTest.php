<?php

namespace Tests\Feature\User;

use App\Models\Application;
use App\Models\User;
use App\Models\WorkPost;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class ApplicationPermissionRegressionTest extends TestCase
{
    use RefreshDatabase;

    private User $owner;

    private User $applicant;

    private User $otherUser;

    protected function setUp(): void
    {
        parent::setUp();

        $this->owner = User::factory()->create([
            'email_verified_at' => now(),
        ]);

        $this->applicant = User::factory()->create([
            'email_verified_at' => now(),
        ]);

        $this->otherUser = User::factory()->create([
            'email_verified_at' => now(),
        ]);

        $this->プロフィールを作成する($this->owner);
        $this->プロフィールを作成する($this->applicant);
        $this->プロフィールを作成する($this->otherUser);
    }

    #[Test]
    public function 参加申請で_非公開募集には申請できない(): void
    {
        // Arrange
        $workPost = $this->募集を作成する($this->owner, WorkPost::STATUS_PRIVATE);

        // Act
        $response = $this
            ->actingAs($this->applicant)
            ->post(route('applications.store', $workPost), [
                'message' => '非公開募集に申請します。',
            ]);

        // Assert
        $this->assertTrue(
            in_array($response->getStatusCode(), [302, 403, 404], true),
            '非公開募集には申請できないこと。'
        );

        $this->assertSame(0, Application::count());
    }

    #[Test]
    public function 参加申請で_終了済み募集には申請できない(): void
    {
        // Arrange
        $workPost = $this->募集を作成する($this->owner, WorkPost::STATUS_CLOSED);

        // Act
        $response = $this
            ->actingAs($this->applicant)
            ->post(route('applications.store', $workPost), [
                'message' => '終了済み募集に申請します。',
            ]);

        // Assert
        $response->assertRedirect(route('work-posts.show', $workPost));
        $this->assertDatabaseMissing('applications', [
            'work_post_id' => $workPost->id,
            'user_id' => $this->applicant->id,
        ]);
    }

    #[Test]
    public function 参加申請一覧で_募集オーナー以外は閲覧できない(): void
    {
        // Arrange
        $workPost = $this->募集を作成する($this->owner, WorkPost::STATUS_OPEN);

        // Act
        $response = $this
            ->actingAs($this->otherUser)
            ->get(route('applications.index', $workPost));

        // Assert
        $this->assertTrue(
            in_array($response->getStatusCode(), [302, 403], true),
            '募集オーナー以外は参加申請一覧を閲覧できないこと。'
        );
    }

    #[Test]
    public function 参加申請承認で_募集オーナー以外は承認できない(): void
    {
        // Arrange
        $workPost = $this->募集を作成する($this->owner, WorkPost::STATUS_OPEN);
        $application = $this->申請を作成する($workPost, $this->applicant, Application::STATUS_PENDING);

        // Act
        $response = $this
            ->actingAs($this->otherUser)
            ->patch(route('applications.approve', $application));

        // Assert
        $this->assertTrue(
            in_array($response->getStatusCode(), [302, 403], true),
            '募集オーナー以外は申請を承認できないこと。'
        );

        $this->assertSame(Application::STATUS_PENDING, $application->fresh()->status);
    }

    #[Test]
    public function 参加申請却下で_募集オーナー以外は却下できない(): void
    {
        // Arrange
        $workPost = $this->募集を作成する($this->owner, WorkPost::STATUS_OPEN);
        $application = $this->申請を作成する($workPost, $this->applicant, Application::STATUS_PENDING);

        // Act
        $response = $this
            ->actingAs($this->otherUser)
            ->patch(route('applications.reject', $application));

        // Assert
        $this->assertTrue(
            in_array($response->getStatusCode(), [302, 403], true),
            '募集オーナー以外は申請を却下できないこと。'
        );

        $this->assertSame(Application::STATUS_PENDING, $application->fresh()->status);
    }

    #[Test]
    public function 参加申請承認で_存在しない申請なら404になる(): void
    {
        // Arrange

        // Act
        $response = $this
            ->actingAs($this->owner)
            ->patch('/applications/999999/approve');

        // Assert
        $response->assertNotFound();
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
        $workPost->title = '申請権限テスト用募集' . uniqid();
        $workPost->body = '申請権限テスト用募集本文です。';
        $workPost->purpose = '黙々作業';
        $workPost->location_type = WorkPost::LOCATION_ONLINE;
        $workPost->meeting_tool = 'Zoom';
        $workPost->time_zone = WorkPost::TIME_ZONE_NIGHT;
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
        $application->message = '申請権限テスト用メッセージです。';
        $application->status = $status;
        $application->save();

        return $application;
    }
}
