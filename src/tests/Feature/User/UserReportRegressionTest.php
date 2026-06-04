<?php

namespace Tests\Feature\User;

use App\Models\Report;
use App\Models\User;
use App\Models\WorkPost;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Schema;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class UserReportRegressionTest extends TestCase
{
    use RefreshDatabase;

    private User $reporter;

    private User $reportedUser;

    private WorkPost $workPost;

    protected function setUp(): void
    {
        parent::setUp();

        $this->reporter = User::factory()->create([
            'email_verified_at' => now(),
        ]);

        $this->reportedUser = User::factory()->create([
            'email_verified_at' => now(),
        ]);

        $this->workPost = $this->募集を作成する($this->reportedUser);
    }

    #[Test]
    public function 通報作成画面で_正しい対象ユーザーと募集なら表示できる(): void
    {
        // Arrange

        // Act
        $response = $this
            ->actingAs($this->reporter)
            ->get(route('reports.create', [
                'reported_user_id' => $this->reportedUser->id,
                'work_post_id' => $this->workPost->id,
            ]));

        // Assert
        $response->assertOk();
        $response->assertViewIs('reports.create');
        $this->assertSame($this->reportedUser->id, $response->viewData('reportedUser')->id);
        $this->assertSame($this->workPost->id, $response->viewData('workPost')->id);
    }

    #[Test]
    #[DataProvider('不正な通報作成クエリ一覧')]
    public function 通報作成画面で_対象が不正な場合_404になる(array $query): void
    {
        // Arrange

        // Act
        $response = $this
            ->actingAs($this->reporter)
            ->get(route('reports.create', $query));

        // Assert
        $response->assertNotFound();
    }

    public static function 不正な通報作成クエリ一覧(): array
    {
        return [
            '存在しない通報対象ユーザー' => [
                [
                    'reported_user_id' => 999999,
                    'work_post_id' => 1,
                ],
            ],
            '存在しない募集' => [
                [
                    'reported_user_id' => 1,
                    'work_post_id' => 999999,
                ],
            ],
        ];
    }

    #[Test]
    public function 通報送信で_正しい入力なら未対応として保存され募集詳細へリダイレクトされる(): void
    {
        // Arrange
        $payload = $this->通報入力データ();

        // Act
        $response = $this
            ->actingAs($this->reporter)
            ->post(route('reports.store'), $payload);

        // Assert
        $response->assertRedirect(route('work-posts.show', $this->workPost));
        $response->assertSessionHas('success', '通報を送信しました。');

        $this->assertDatabaseHas('reports', [
            'reporter_id' => $this->reporter->id,
            'reported_user_id' => $this->reportedUser->id,
            'work_post_id' => $this->workPost->id,
            'status' => Report::STATUS_OPEN,
        ]);
    }

    #[Test]
    public function 通報送信で_必須項目が空ならバリデーションエラーになる(): void
    {
        // Arrange

        // Act
        $response = $this
            ->actingAs($this->reporter)
            ->from(route('reports.create', [
                'reported_user_id' => $this->reportedUser->id,
                'work_post_id' => $this->workPost->id,
            ]))
            ->post(route('reports.store'), []);

        // Assert
        $response->assertRedirect();
        $response->assertSessionHasErrors();
        $this->assertSame(0, Report::count());
    }

    #[Test]
    public function 未ログインで通報作成画面へアクセスした場合_ログイン画面へリダイレクトされる(): void
    {
        // Arrange

        // Act
        $response = $this->get(route('reports.create', [
            'reported_user_id' => $this->reportedUser->id,
            'work_post_id' => $this->workPost->id,
        ]));

        // Assert
        $response->assertRedirect(route('login'));
    }

    private function 通報入力データ(): array
    {
        $payload = [
            'reported_user_id' => $this->reportedUser->id,
            'work_post_id' => $this->workPost->id,
            'reason' => '不適切な募集です。',
        ];

        if (Schema::hasColumn('reports', 'body')) {
            $payload['body'] = '通報詳細本文です。';
        }

        if (Schema::hasColumn('reports', 'detail')) {
            $payload['detail'] = '通報詳細本文です。';
        }

        if (Schema::hasColumn('reports', 'description')) {
            $payload['description'] = '通報詳細本文です。';
        }

        return $payload;
    }

    private function 募集を作成する(User $user): WorkPost
    {
        $workPost = new WorkPost();
        $workPost->user_id = $user->id;
        $workPost->title = '通報対象募集';
        $workPost->body = '通報対象募集本文です。';
        $workPost->purpose = 'work';
        $workPost->location_type = 'online';
        $workPost->meeting_tool = 'Zoom';
        $workPost->time_zone = 'night';
        $workPost->max_participants = 3;
        $workPost->status = WorkPost::STATUS_OPEN;
        $workPost->save();

        return $workPost;
    }
}
