<?php

namespace Tests\Feature\User;

use App\Models\User;
use App\Models\WorkPost;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class WorkPostPermissionRegressionTest extends TestCase
{
    use RefreshDatabase;

    private User $owner;

    private User $otherUser;

    protected function setUp(): void
    {
        parent::setUp();

        $this->owner = User::factory()->create([
            'email_verified_at' => now(),
        ]);

        $this->otherUser = User::factory()->create([
            'email_verified_at' => now(),
        ]);

        $this->プロフィールを作成する($this->owner);
        $this->プロフィールを作成する($this->otherUser);
    }

    #[Test]
    public function 募集詳細で_非公開募集なら404になる(): void
    {
        // Arrange
        $workPost = $this->募集を作成する($this->owner, WorkPost::STATUS_PRIVATE);

        // Act
        $response = $this->get(route('work-posts.show', [
            'workPost' => $workPost->id,
        ]));

        // Assert
        $response->assertNotFound();
    }

    #[Test]
    public function 募集更新で_他人の募集なら更新できない(): void
    {
        // Arrange
        $workPost = $this->募集を作成する($this->owner, WorkPost::STATUS_OPEN);

        // Act
        $response = $this
            ->actingAs($this->otherUser)
            ->put(route('work-posts.update', [
                'workPost' => $workPost->id,
            ]), [
                ...$this->募集入力データ(),
                'title' => '他人が更新したタイトル',
            ]);

        // Assert
        $this->assertTrue(
            in_array($response->getStatusCode(), [302, 403], true),
            '他人の募集は更新できないこと。'
        );

        $this->assertDatabaseMissing('work_posts', [
            'id' => $workPost->id,
            'title' => '他人が更新したタイトル',
        ]);
    }

    #[Test]
    public function 募集締切で_他人の募集なら締切できない(): void
    {
        // Arrange
        $workPost = $this->募集を作成する($this->owner, WorkPost::STATUS_OPEN);

        // Act
        $response = $this
            ->actingAs($this->otherUser)
            ->patch(route('work-posts.close', [
                'workPost' => $workPost->id,
            ]));

        // Assert
        $this->assertTrue(
            in_array($response->getStatusCode(), [302, 403], true),
            '他人の募集は締切できないこと。'
        );

        $this->assertSame(WorkPost::STATUS_OPEN, $workPost->fresh()->status);
    }

    #[Test]
    public function 未ログインで募集編集画面へアクセスした場合_ログイン画面へリダイレクトされる(): void
    {
        // Arrange
        $workPost = $this->募集を作成する($this->owner, WorkPost::STATUS_OPEN);

        // Act
        $response = $this->get(route('work-posts.edit', [
            'workPost' => $workPost->id,
        ]));

        // Assert
        $response->assertRedirect(route('login'));
    }

    #[Test]
    public function 未ログインで募集更新した場合_ログイン画面へリダイレクトされる(): void
    {
        // Arrange
        $workPost = $this->募集を作成する($this->owner, WorkPost::STATUS_OPEN);

        // Act
        $response = $this->put(route('work-posts.update', [
            'workPost' => $workPost->id,
        ]), $this->募集入力データ());

        // Assert
        $response->assertRedirect(route('login'));
    }

    #[Test]
    #[DataProvider('募集更新の不正入力一覧')]
    public function 募集更新で_入力が不正な場合_バリデーションエラーになる(array $override, array $errorKeys): void
    {
        // Arrange
        $workPost = $this->募集を作成する($this->owner, WorkPost::STATUS_OPEN);

        $payload = [
            ...$this->募集入力データ(),
            ...$override,
        ];

        // Act
        $response = $this
            ->actingAs($this->owner)
            ->from(route('work-posts.edit', ['workPost' => $workPost->id]))
            ->put(route('work-posts.update', ['workPost' => $workPost->id]), $payload);

        // Assert
        $response->assertRedirect(route('work-posts.edit', ['workPost' => $workPost->id]));
        $response->assertSessionHasErrors($errorKeys);
    }

    public static function 募集更新の不正入力一覧(): array
    {
        return [
            '終了日時が開始日時より前' => [
                [
                    'start_at' => now()->addDay()->format('Y-m-d H:i:s'),
                    'end_at' => now()->subDay()->format('Y-m-d H:i:s'),
                ],
                ['end_at'],
            ],
            '募集人数が0' => [
                [
                    'max_participants' => 0,
                ],
                ['max_participants'],
            ],
            '開催形式が不正' => [
                [
                    'location_type' => 'invalid-location',
                ],
                ['location_type'],
            ],
            '時間帯が不正' => [
                [
                    'time_zone' => 'invalid-time-zone',
                ],
                ['time_zone'],
            ],
        ];
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
            'title' => '権限テスト用募集',
            'body' => '権限テスト用募集本文です。',
            'purpose' => '黙々作業',
            'location_type' => WorkPost::LOCATION_ONLINE,
            'meeting_tool' => 'Zoom',
            'prefecture_id' => null,
            'start_at' => now()->addDay()->format('Y-m-d H:i:s'),
            'end_at' => now()->addDay()->addHour()->format('Y-m-d H:i:s'),
            'time_zone' => WorkPost::TIME_ZONE_NIGHT,
            'max_participants' => 3,
        ];
    }

    private function 募集を作成する(User $user, int $status): WorkPost
    {
        $workPost = new WorkPost();
        $workPost->user_id = $user->id;
        $workPost->title = '権限テスト用募集' . uniqid();
        $workPost->body = '権限テスト用募集本文です。';
        $workPost->purpose = '黙々作業';
        $workPost->location_type = WorkPost::LOCATION_ONLINE;
        $workPost->meeting_tool = 'Zoom';
        $workPost->time_zone = WorkPost::TIME_ZONE_NIGHT;
        $workPost->max_participants = 3;
        $workPost->status = $status;
        $workPost->save();

        return $workPost;
    }
}
