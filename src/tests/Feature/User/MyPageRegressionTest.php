<?php

namespace Tests\Feature\User;

use App\Models\Application;
use App\Models\Message;
use App\Models\User;
use App\Models\WorkPost;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class MyPageRegressionTest extends TestCase
{
    use RefreshDatabase;

    private User $user;

    private User $otherUser;

    protected function setUp(): void
    {
        parent::setUp();

        $this->user = User::factory()->create([
            'email_verified_at' => now(),
        ]);

        $this->otherUser = User::factory()->create([
            'email_verified_at' => now(),
        ]);
    }

    #[Test]
    public function マイページで_自分のプロフィールと募集と申請とメッセージが表示用データに渡される(): void
    {
        // Arrange
        $this->user->profile()->create([
            'display_name' => 'マイページユーザー',
            'job_type' => 'Laravelエンジニア',
        ]);

        $ownWorkPost = $this->募集を作成する($this->user);
        $pendingWorkPost = $this->募集を作成する($this->otherUser);
        $approvedWorkPost = $this->募集を作成する($this->otherUser);

        $pendingApplication = $this->申請を作成する($pendingWorkPost, $this->user, Application::STATUS_PENDING);
        $approvedApplication = $this->申請を作成する($approvedWorkPost, $this->user, Application::STATUS_APPROVED);

        $message = Message::create([
            'sender_id' => $this->otherUser->id,
            'receiver_id' => $this->user->id,
            'body' => 'マイページ表示用メッセージ',
        ]);

        // Act
        $response = $this
            ->actingAs($this->user)
            ->get(route('mypage'));

        // Assert
        $response->assertOk();
        $response->assertViewIs('mypage');

        $this->assertSame($this->user->id, $response->viewData('user')->id);
        $this->assertTrue($response->viewData('workPosts')->pluck('id')->contains($ownWorkPost->id));
        $this->assertTrue($response->viewData('applications')->pluck('id')->contains($pendingApplication->id));
        $this->assertTrue($response->viewData('approvedApplications')->pluck('id')->contains($approvedApplication->id));
        $this->assertTrue($response->viewData('messages')->pluck('id')->contains($message->id));
    }

    #[Test]
    public function マイページで_最新メッセージは10件まで表示される(): void
    {
        // Arrange
        foreach (range(1, 12) as $index) {
            Message::create([
                'sender_id' => $this->otherUser->id,
                'receiver_id' => $this->user->id,
                'body' => "メッセージ{$index}",
                'created_at' => now()->addMinutes($index),
                'updated_at' => now()->addMinutes($index),
            ]);
        }

        // Act
        $response = $this
            ->actingAs($this->user)
            ->get(route('mypage'));

        // Assert
        $response->assertOk();
        $this->assertCount(10, $response->viewData('messages'));
    }

    #[Test]
    public function 未ログインでマイページへアクセスした場合_ログイン画面へリダイレクトされる(): void
    {
        // Arrange

        // Act
        $response = $this->get(route('mypage'));

        // Assert
        $response->assertRedirect(route('login'));
    }

    private function 募集を作成する(User $user): WorkPost
    {
        $workPost = new WorkPost();
        $workPost->user_id = $user->id;
        $workPost->title = 'マイページ用募集' . uniqid();
        $workPost->body = 'マイページ用募集本文です。';
        $workPost->purpose = 'work';
        $workPost->location_type = 'online';
        $workPost->meeting_tool = 'Zoom';
        $workPost->time_zone = 'night';
        $workPost->max_participants = 3;
        $workPost->status = WorkPost::STATUS_OPEN;
        $workPost->save();

        return $workPost;
    }

    private function 申請を作成する(WorkPost $workPost, User $user, int $status): Application
    {
        $application = new Application();
        $application->work_post_id = $workPost->id;
        $application->user_id = $user->id;
        $application->message = 'マイページ用申請メッセージ';
        $application->status = $status;
        $application->save();

        return $application;
    }
}
