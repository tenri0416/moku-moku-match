<?php

namespace Tests\Feature\User;

use App\Models\User;
use App\Models\WorkPost;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class WorkPostRegressionTest extends TestCase
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

        $this->プロフィールを作成する($this->user);
        $this->プロフィールを作成する($this->otherUser);
    }

    #[Test]
    public function 募集詳細で_公開中の募集を表示できる(): void
    {
        // Arrange
        $workPost = $this->募集を作成する($this->otherUser, WorkPost::STATUS_OPEN);

        // Act
        $response = $this->get($this->募集詳細URL($workPost));

        // Assert
        $response->assertOk();
        $response->assertViewIs('work-posts.show');
        $this->assertSame($workPost->id, $response->viewData('workPost')->id);
    }

    #[Test]
    public function 募集作成画面で_プロフィール登録済みのメール認証済みユーザーなら表示できる(): void
    {
        // Arrange

        // Act
        $response = $this
            ->actingAs($this->user)
            ->get(route('work-posts.create'));

        // Assert
        $response->assertOk();
    }

    #[Test]
    public function 募集作成画面で_プロフィール未登録ならプロフィール編集画面へリダイレクトされる(): void
    {
        // Arrange
        $noProfileUser = User::factory()->create([
            'email_verified_at' => now(),
        ]);

        // Act
        $response = $this
            ->actingAs($noProfileUser)
            ->get(route('work-posts.create'));

        // Assert
        $response->assertRedirect(route('profile.edit'));
    }

    #[Test]
    public function 募集作成で_正しい入力なら募集が保存される(): void
    {
        // Arrange
        $payload = $this->募集入力データ();

        // Act
        $response = $this
            ->actingAs($this->user)
            ->post(route('work-posts.store'), $payload);

        // Assert
        $workPost = WorkPost::query()
            ->where('user_id', $this->user->id)
            ->where('title', $payload['title'])
            ->first();

        $this->assertNotNull($workPost);

        $response->assertRedirect($this->募集詳細URL($workPost));

        $this->assertDatabaseHas('work_posts', [
            'id' => $workPost->id,
            'user_id' => $this->user->id,
            'title' => $payload['title'],
            'status' => WorkPost::STATUS_OPEN,
        ]);
    }

    #[Test]
    #[DataProvider('募集作成の不正入力一覧')]
    public function 募集作成で_入力が不正な場合_バリデーションエラーになる(array $override, array $errorKeys): void
    {
        // Arrange
        $payload = [
            ...$this->募集入力データ(),
            ...$override,
        ];

        // Act
        $response = $this
            ->actingAs($this->user)
            ->from(route('work-posts.create'))
            ->post(route('work-posts.store'), $payload);

        // Assert
        $response->assertRedirect(route('work-posts.create'));
        $response->assertSessionHasErrors($errorKeys);
    }

    public static function 募集作成の不正入力一覧(): array
    {
        return [
            'タイトル空' => [
                ['title' => ''],
                ['title'],
            ],
            '本文空' => [
                ['body' => ''],
                ['body'],
            ],
            '目的空' => [
                ['purpose' => ''],
                ['purpose'],
            ],
            '開催形式空' => [
                ['location_type' => ''],
                ['location_type'],
            ],
        ];
    }

    #[Test]
    public function 募集編集画面で_自分の募集なら表示できる(): void
    {
        // Arrange
        $workPost = $this->募集を作成する($this->user, WorkPost::STATUS_OPEN);

        // Act
        $response = $this
            ->actingAs($this->user)
            ->get(route('work-posts.edit', [
                'workPost' => $workPost->id,
            ]));

        // Assert
        $response->assertOk();
    }

    #[Test]
    public function 募集更新で_自分の募集なら更新できる(): void
    {
        // Arrange
        $workPost = $this->募集を作成する($this->user, WorkPost::STATUS_OPEN);

        $payload = [
            ...$this->募集入力データ(),
            'title' => '更新後の募集タイトル',
            'body' => '更新後の募集本文です。',
        ];

        // Act
        $response = $this
            ->actingAs($this->user)
            ->put(route('work-posts.update', [
                'workPost' => $workPost->id,
            ]), $payload);

        // Assert
        $response->assertRedirect($this->募集詳細URL($workPost));

        $this->assertDatabaseHas('work_posts', [
            'id' => $workPost->id,
            'title' => '更新後の募集タイトル',
            'body' => '更新後の募集本文です。',
        ]);
    }

    #[Test]
    public function 募集編集画面で_他人の募集なら403またはリダイレクトになる(): void
    {
        // Arrange
        $workPost = $this->募集を作成する($this->otherUser, WorkPost::STATUS_OPEN);

        // Act
        $response = $this
            ->actingAs($this->user)
            ->get(route('work-posts.edit', [
                'workPost' => $workPost->id,
            ]));

        // Assert
        $this->assertTrue(
            in_array($response->getStatusCode(), [302, 403], true),
            '他人の募集編集画面にはアクセスできないこと。'
        );
    }

    #[Test]
    public function 募集締切で_自分の募集なら募集終了にできる(): void
    {
        // Arrange
        $workPost = $this->募集を作成する($this->user, WorkPost::STATUS_OPEN);

        // Act
        $response = $this
            ->actingAs($this->user)
            ->patch(route('work-posts.close', [
                'workPost' => $workPost->id,
            ]));

        // Assert
        $response->assertRedirect();
        $this->assertSame(WorkPost::STATUS_CLOSED, $workPost->fresh()->status);
    }

    #[Test]
    public function 未ログインで募集作成画面へアクセスした場合_ログイン画面へリダイレクトされる(): void
    {
        // Arrange

        // Act
        $response = $this->get(route('work-posts.create'));

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

    private function 募集入力データ(): array
    {
        return [
            'title' => 'テスト募集タイトル',
            'body' => 'テスト募集本文です。一緒に黙々作業しましょう。',
            'purpose' => 'work',
            'location_type' => 'online',
            'meeting_tool' => 'Zoom',
            'prefecture_id' => null,
            'start_at' => now()->addDay()->format('Y-m-d H:i:s'),
            'end_at' => now()->addDay()->addHour()->format('Y-m-d H:i:s'),
            'time_zone' => 'night',
            'max_participants' => 3,
            'status' => WorkPost::STATUS_OPEN,
        ];
    }

    private function 募集を作成する(User $user, int $status): WorkPost
    {
        $workPost = new WorkPost();
        $workPost->user_id = $user->id;
        $workPost->title = '募集タイトル' . uniqid();
        $workPost->body = '募集本文です。';
        $workPost->purpose = 'work';
        $workPost->location_type = 'online';
        $workPost->meeting_tool = 'Zoom';
        $workPost->prefecture_id = null;
        $workPost->start_at = now()->addDay();
        $workPost->end_at = now()->addDay()->addHour();
        $workPost->time_zone = 'night';
        $workPost->max_participants = 3;
        $workPost->status = $status;
        $workPost->save();

        return $workPost;
    }

    private function 募集詳細URL(WorkPost $workPost): string
    {
        return url('/work-posts/' . $workPost->id);
    }
}
