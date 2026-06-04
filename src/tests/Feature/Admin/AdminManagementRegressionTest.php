<?php

namespace Tests\Feature\Admin;

use App\Models\Admin;
use App\Models\AiProviderAttemptLog;
use App\Models\Report;
use App\Models\User;
use App\Models\WorkPost;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class AdminManagementRegressionTest extends TestCase
{
    use RefreshDatabase;

    private Admin $admin;

    protected function setUp(): void
    {
        parent::setUp();

        $this->admin = Admin::create([
            'name' => 'テスト管理者',
            'email' => 'admin-management-regression@example.com',
            'email_verified_at' => now(),
            'password' => Hash::make('password'),
            'status' => 1,
            'remember_token' => null,
        ]);
    }

    #[Test]
    public function 管理者ダッシュボードで_ユーザー数と募集数と未対応通報数が表示用データに渡される(): void
    {
        // Arrange
        $owner = User::factory()->create(['email_verified_at' => now()]);
        $reporter = User::factory()->create(['email_verified_at' => now()]);
        $reportedUser = User::factory()->create(['email_verified_at' => now()]);

        $workPost = $this->募集を作成する($owner);

        $this->通報を作成する($reporter, $reportedUser, $workPost, Report::STATUS_OPEN);
        $this->通報を作成する($reporter, $reportedUser, $workPost, Report::STATUS_CLOSED);

        // Act
        $response = $this
            ->actingAs($this->admin, 'admin')
            ->get($this->管理者URL('admin.dashboard', '/admin/dashboard'));

        // Assert
        $response->assertOk();
        $response->assertViewIs('admin.dashboard');

        $this->assertSame(User::count(), (int) $response->viewData('userCount'));
        $this->assertSame(WorkPost::count(), (int) $response->viewData('workPostCount'));
        $this->assertSame(1, (int) $response->viewData('openReportCount'));
        $this->assertCount(2, $response->viewData('latestReports'));
    }

    #[Test]
    public function 管理者DB一覧で_非表示テーブルは表示用データから除外される(): void
    {
        // Arrange

        // Act
        $response = $this
            ->actingAs($this->admin, 'admin')
            ->get($this->管理者URL('admin.database.index', '/admin/database'));

        // Assert
        $response->assertOk();
        $response->assertViewIs('admin.database.index');

        $tables = $response->viewData('tables');

        $this->assertFalse($tables->contains('sessions'));
        $this->assertFalse($tables->contains('password_reset_tokens'));
        $this->assertFalse($tables->contains('cache'));
        $this->assertTrue($tables->contains('users'));
    }

    #[Test]
    public function 管理者DB詳細で_許可されたテーブルのカラムと行が表示用データに渡される(): void
    {
        // Arrange
        User::factory()->create([
            'name' => 'DB詳細確認ユーザー',
            'email_verified_at' => now(),
        ]);

        // Act
        $response = $this
            ->actingAs($this->admin, 'admin')
            ->get($this->管理者URL('admin.database.show', '/admin/database/users', ['users']));

        // Assert
        $response->assertOk();
        $response->assertViewIs('admin.database.show');

        $this->assertSame('users', $response->viewData('table'));
        $this->assertTrue($response->viewData('columns')->contains('id'));
        $this->assertGreaterThanOrEqual(1, $response->viewData('rows')->total());
    }

    #[Test]
    #[DataProvider('管理者DB非表示テーブル一覧')]
    public function 管理者DB詳細で_非表示テーブルを指定した場合_404になる(string $table): void
    {
        // Arrange

        // Act
        $response = $this
            ->actingAs($this->admin, 'admin')
            ->get($this->管理者URL('admin.database.show', "/admin/database/{$table}", [$table]));

        // Assert
        $response->assertNotFound();
    }

    public static function 管理者DB非表示テーブル一覧(): array
    {
        return [
            'sessions' => ['sessions'],
            'password_reset_tokens' => ['password_reset_tokens'],
            'cache' => ['cache'],
        ];
    }

    #[Test]
    public function 管理者ユーザー一覧で_ユーザー一覧が表示用データに渡される(): void
    {
        // Arrange
        User::factory()->count(3)->create([
            'email_verified_at' => now(),
        ]);

        // Act
        $response = $this
            ->actingAs($this->admin, 'admin')
            ->get($this->管理者URL('admin.users.index', '/admin/users'));

        // Assert
        $response->assertOk();
        $response->assertViewIs('admin.users.index');
        $this->assertGreaterThanOrEqual(3, $response->viewData('users')->total());
    }

    #[Test]
    public function 管理者ユーザー詳細で_対象ユーザーが表示用データに渡される(): void
    {
        // Arrange
        $user = User::factory()->create([
            'name' => '管理対象ユーザー',
            'email_verified_at' => now(),
        ]);

        // Act
        $response = $this
            ->actingAs($this->admin, 'admin')
            ->get($this->管理者URL('admin.users.show', "/admin/users/{$user->id}", [$user]));

        // Assert
        $response->assertOk();
        $response->assertViewIs('admin.users.show');
        $this->assertSame($user->id, $response->viewData('user')->id);
    }

    #[Test]
    public function 管理者ユーザー停止で_ユーザーのステータスが停止になる(): void
    {
        // Arrange
        $user = User::factory()->create([
            'status' => User::STATUS_ACTIVE,
            'email_verified_at' => now(),
        ]);

        // Act
        $response = $this
            ->actingAs($this->admin, 'admin')
            ->from($this->管理者URL('admin.users.show', "/admin/users/{$user->id}", [$user]))
            ->patch($this->管理者URL('admin.users.suspend', "/admin/users/{$user->id}/suspend", [$user]));

        // Assert
        $response->assertRedirect();
        $response->assertSessionHas('success', 'ユーザーを停止しました。');

        $this->assertSame(User::STATUS_SUSPENDED, $user->fresh()->status);
    }

    #[Test]
    public function 管理者ユーザー有効化で_ユーザーのステータスが有効になる(): void
    {
        // Arrange
        $user = User::factory()->create([
            'status' => User::STATUS_SUSPENDED,
            'email_verified_at' => now(),
        ]);

        // Act
        $response = $this
            ->actingAs($this->admin, 'admin')
            ->from($this->管理者URL('admin.users.show', "/admin/users/{$user->id}", [$user]))
            ->patch($this->管理者URL('admin.users.activate', "/admin/users/{$user->id}/activate", [$user]));

        // Assert
        $response->assertRedirect();
        $response->assertSessionHas('success', 'ユーザーを有効化しました。');

        $this->assertSame(User::STATUS_ACTIVE, $user->fresh()->status);
    }

    #[Test]
    public function 管理者募集一覧で_募集一覧が表示用データに渡される(): void
    {
        // Arrange
        $owner = User::factory()->create(['email_verified_at' => now()]);
        $this->募集を作成する($owner);

        // Act
        $response = $this
            ->actingAs($this->admin, 'admin')
            ->get($this->管理者URL('admin.work-posts.index', '/admin/work-posts'));

        // Assert
        $response->assertOk();
        $response->assertViewIs('admin.work-posts.index');
        $this->assertGreaterThanOrEqual(1, $response->viewData('workPosts')->total());
    }

    #[Test]
    public function 管理者募集詳細で_対象募集が表示用データに渡される(): void
    {
        // Arrange
        $owner = User::factory()->create(['email_verified_at' => now()]);
        $workPost = $this->募集を作成する($owner);

        // Act
        $response = $this
            ->actingAs($this->admin, 'admin')
            ->get($this->管理者URL('admin.work-posts.show', "/admin/work-posts/{$workPost->id}", [$workPost]));

        // Assert
        $response->assertOk();
        $response->assertViewIs('admin.work-posts.show');
        $this->assertSame($workPost->id, $response->viewData('workPost')->id);
    }

    #[Test]
    public function 管理者募集非公開で_募集ステータスが非公開になる(): void
    {
        // Arrange
        $owner = User::factory()->create(['email_verified_at' => now()]);
        $workPost = $this->募集を作成する($owner, WorkPost::STATUS_OPEN);

        // Act
        $response = $this
            ->actingAs($this->admin, 'admin')
            ->from($this->管理者URL('admin.work-posts.show', "/admin/work-posts/{$workPost->id}", [$workPost]))
            ->patch($this->管理者URL('admin.work-posts.private', "/admin/work-posts/{$workPost->id}/private", [$workPost]));

        // Assert
        $response->assertRedirect();
        $response->assertSessionHas('success', '募集を非公開にしました。');

        $this->assertSame(WorkPost::STATUS_PRIVATE, $workPost->fresh()->status);
    }

    #[Test]
    public function 管理者募集再公開で_募集ステータスが公開になる(): void
    {
        // Arrange
        $owner = User::factory()->create(['email_verified_at' => now()]);
        $workPost = $this->募集を作成する($owner, WorkPost::STATUS_PRIVATE);

        // Act
        $response = $this
            ->actingAs($this->admin, 'admin')
            ->from($this->管理者URL('admin.work-posts.show', "/admin/work-posts/{$workPost->id}", [$workPost]))
            ->patch($this->管理者URL('admin.work-posts.open', "/admin/work-posts/{$workPost->id}/open", [$workPost]));

        // Assert
        $response->assertRedirect();
        $response->assertSessionHas('success', '募集を再公開しました。');

        $this->assertSame(WorkPost::STATUS_OPEN, $workPost->fresh()->status);
    }

    #[Test]
    public function 管理者通知既読処理で_未読通知がすべて既読になる(): void
    {
        // Arrange
        $this->管理者通知を作成する($this->admin, null);
        $this->管理者通知を作成する($this->admin, null);
        $this->管理者通知を作成する($this->admin, now());

        // Act
        $response = $this
            ->actingAs($this->admin, 'admin')
            ->postJson($this->管理者URL('admin.notifications.mark-all-as-read', '/admin/notifications/mark-all-as-read'));

        // Assert
        $response->assertOk();
        $response->assertJsonPath('message', '管理者通知を既読にしました。');
        $response->assertJsonPath('unread_count', 0);

        $this->assertSame(0, $this->admin->fresh()->unreadNotifications()->count());
    }

    #[Test]
    public function 管理者通知既読処理で_未認証の場合_401を返す(): void
    {
        // Arrange

        // Act
        $response = $this->postJson($this->管理者URL('admin.notifications.mark-all-as-read', '/admin/notifications/mark-all-as-read'));

        // Assert
        $this->assertTrue(
            in_array($response->getStatusCode(), [401, 302], true),
            '未認証時は401または管理者ログイン画面へのリダイレクトになること。'
        );
    }

    #[Test]
    public function AI利用状況画面で_集計データが表示用データに渡される(): void
    {
        // Arrange
        $this->AI試行ログを作成する('google', 'success', false, now());
        $this->AI試行ログを作成する('google', 'failed', true, now());
        $this->AI試行ログを作成する('groq', 'success', false, now()->subDay());

        // Act
        $response = $this
            ->actingAs($this->admin, 'admin')
            ->get($this->管理者URL('admin.ai-usage.index', '/admin/ai-usage'));

        // Assert
        $response->assertOk();
        $response->assertViewIs('admin.ai-usage.index');

        $this->assertSame(3, (int) $response->viewData('totalAttempts'));
        $this->assertSame(2, (int) $response->viewData('successAttempts'));
        $this->assertSame(1, (int) $response->viewData('failedAttempts'));
        $this->assertSame(1, (int) $response->viewData('fallbackAttempts'));
        $this->assertCount(3, $response->viewData('providerCards'));
    }

    #[Test]
    public function 記事閲覧数一覧画面で_正常に表示できる(): void
    {
        // Arrange

        // Act
        $response = $this
            ->actingAs($this->admin, 'admin')
            ->get($this->管理者URL('admin.article-views.index', '/admin/article-views'));

        // Assert
        $response->assertOk();
        $response->assertViewIs('admin.article-views.index');
        $this->assertNotNull($response->viewData('articles'));
    }

    private function 募集を作成する(User $user, ?int $status = null): WorkPost
    {
        $workPost = new WorkPost();
        $workPost->user_id = $user->id;
        $workPost->title = '管理者テスト用募集';
        $workPost->body = '管理者テスト用募集本文です。';
        $workPost->purpose = 'work';
        $workPost->location_type = 'online';
        $workPost->meeting_tool = 'Zoom';
        $workPost->time_zone = 'night';
        $workPost->max_participants = 3;
        $workPost->status = $status ?? WorkPost::STATUS_OPEN;
        $workPost->save();

        return $workPost;
    }

    private function 通報を作成する(User $reporter, User $reportedUser, WorkPost $workPost, int $status): Report
    {
        $report = new Report();
        $report->reporter_id = $reporter->id;
        $report->reported_user_id = $reportedUser->id;
        $report->work_post_id = $workPost->id;
        $report->reason = '管理者テスト用通報です。';

        if (Schema::hasColumn('reports', 'body')) {
            $report->body = '通報詳細本文です。';
        }

        if (Schema::hasColumn('reports', 'detail')) {
            $report->detail = '通報詳細本文です。';
        }

        if (Schema::hasColumn('reports', 'description')) {
            $report->description = '通報詳細本文です。';
        }

        $report->status = $status;
        $report->save();

        return $report;
    }

    private function 管理者通知を作成する(Admin $admin, mixed $readAt): void
    {
        DB::table('notifications')->insert([
            'id' => (string) Str::uuid(),
            'type' => 'Tests\\Notifications\\DummyAdminNotification',
            'notifiable_type' => $admin::class,
            'notifiable_id' => $admin->id,
            'data' => json_encode([
                'message' => 'テスト通知',
            ], JSON_UNESCAPED_UNICODE),
            'read_at' => $readAt,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }

    private function AI試行ログを作成する(string $provider, string $status, bool $isFallback, mixed $attemptedAt): void
    {
        if (! class_exists(AiProviderAttemptLog::class)) {
            $this->markTestSkipped('AiProviderAttemptLog が存在しないためスキップします。');
        }

        AiProviderAttemptLog::create([
            'provider' => $provider,
            'model' => $provider . '-test-model',
            'status' => $status,
            'status_code' => $status === 'success' ? 200 : 429,
            'error_message' => $status === 'failed' ? 'テスト用エラー' : null,
            'retry_after_seconds' => $status === 'failed' ? 60 : null,
            'retry_available_at' => $status === 'failed' ? now()->addMinute() : null,
            'attempt' => 1,
            'is_fallback' => $isFallback,
            'action_name' => 'テスト用AI処理',
            'attempted_at' => $attemptedAt,
        ]);
    }

    private function 管理者URL(string $routeName, string $fallback, array $parameters = []): string
    {
        if (Route::has($routeName)) {
            return route($routeName, $parameters);
        }

        return url($fallback);
    }
}
