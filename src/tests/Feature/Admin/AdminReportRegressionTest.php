<?php

namespace Tests\Feature\Admin;

use App\Models\Admin;
use App\Models\Report;
use App\Models\User;
use App\Models\WorkPost;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Route;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class AdminReportRegressionTest extends TestCase
{
    use RefreshDatabase;

    private Admin $admin;

    private User $reporter;

    private User $reportedUser;

    private WorkPost $workPost;

    protected function setUp(): void
    {
        parent::setUp();

        $this->admin = $this->管理者を作成する();

        $this->reporter = User::factory()->create([
            'name' => '通報者',
            'email_verified_at' => now(),
        ]);

        $this->reportedUser = User::factory()->create([
            'name' => '通報対象者',
            'email_verified_at' => now(),
        ]);

        $this->workPost = $this->募集を作成する($this->reportedUser);
    }

    #[Test]
    public function 管理者通報一覧で_通報一覧が新しい順で表示用データに渡される(): void
    {
        // Arrange
        $oldReport = $this->通報を作成する([
            'reason' => '古い通報',
            'status' => Report::STATUS_OPEN,
            'created_at' => now()->subDay(),
        ]);

        $newReport = $this->通報を作成する([
            'reason' => '新しい通報',
            'status' => Report::STATUS_OPEN,
            'created_at' => now(),
        ]);

        // Act
        $response = $this
            ->actingAs($this->admin, 'admin')
            ->get($this->管理者URL('admin.reports.index', '/admin/reports'));

        // Assert
        $response->assertOk();
        $response->assertViewIs('admin.reports.index');

        $reports = $response->viewData('reports');

        $this->assertSame($newReport->id, $reports->items()[0]->id);
        $this->assertSame($oldReport->id, $reports->items()[1]->id);
    }

    #[Test]
    public function 管理者通報詳細で_対象通報が表示用データに渡される(): void
    {
        // Arrange
        $report = $this->通報を作成する([
            'reason' => '詳細確認用通報',
            'status' => Report::STATUS_OPEN,
        ]);

        // Act
        $response = $this
            ->actingAs($this->admin, 'admin')
            ->get($this->管理者URL('admin.reports.show', "/admin/reports/{$report->id}", [$report]));

        // Assert
        $response->assertOk();
        $response->assertViewIs('admin.reports.show');
        $this->assertSame($report->id, $response->viewData('report')->id);
    }

    #[Test]
    public function 管理者通報詳細で_存在しない通報を指定した場合_404になる(): void
    {
        // Arrange

        // Act
        $response = $this
            ->actingAs($this->admin, 'admin')
            ->get($this->管理者URL('admin.reports.show', '/admin/reports/999999', [999999]));

        // Assert
        $response->assertNotFound();
    }

    #[Test]
    public function 管理者通報ステータス変更で_未対応から対応中に変更できる(): void
    {
        // Arrange
        $report = $this->通報を作成する([
            'status' => Report::STATUS_OPEN,
        ]);

        // Act
        $response = $this
            ->actingAs($this->admin, 'admin')
            ->from($this->管理者URL('admin.reports.show', "/admin/reports/{$report->id}", [$report]))
            ->patch($this->管理者URL('admin.reports.in-progress', "/admin/reports/{$report->id}/in-progress", [$report]));

        // Assert
        $response->assertRedirect();
        $response->assertSessionHas('success', '通報を対応中にしました。');

        $this->assertSame(Report::STATUS_IN_PROGRESS, $report->fresh()->status);
    }

    #[Test]
    public function 管理者通報ステータス変更で_対応済みに変更できる(): void
    {
        // Arrange
        $report = $this->通報を作成する([
            'status' => Report::STATUS_IN_PROGRESS,
        ]);

        // Act
        $response = $this
            ->actingAs($this->admin, 'admin')
            ->from($this->管理者URL('admin.reports.show', "/admin/reports/{$report->id}", [$report]))
            ->patch($this->管理者URL('admin.reports.close', "/admin/reports/{$report->id}/close", [$report]));

        // Assert
        $response->assertRedirect();
        $response->assertSessionHas('success', '通報を対応済みにしました。');

        $this->assertSame(Report::STATUS_CLOSED, $report->fresh()->status);
    }

    #[Test]
    public function 未ログインで管理者通報一覧へアクセスした場合_管理者ログイン画面へリダイレクトされる(): void
    {
        // Arrange

        // Act
        $response = $this->get($this->管理者URL('admin.reports.index', '/admin/reports'));

        // Assert
        $response->assertRedirect();
    }

    #[Test]
    public function 一般ユーザーで管理者通報一覧へアクセスした場合_管理者としてはアクセスできない(): void
    {
        // Arrange
        $user = User::factory()->create([
            'email_verified_at' => now(),
        ]);

        // Act
        $response = $this
            ->actingAs($user)
            ->get($this->管理者URL('admin.reports.index', '/admin/reports'));

        // Assert
        $this->assertTrue(
            in_array($response->getStatusCode(), [302, 403], true),
            '一般ユーザーは管理者通報画面へアクセスできないこと。'
        );
    }

    private function 管理者を作成する(): Admin
    {
        return Admin::create([
            'name' => 'テスト管理者',
            'email' => 'admin-report-regression@example.com',
            'email_verified_at' => now(),
            'password' => Hash::make('password'),
            'status' => 1,
            'remember_token' => null,
        ]);
    }

    private function 募集を作成する(User $user): WorkPost
    {
        $workPost = new WorkPost();
        $workPost->user_id = $user->id;
        $workPost->title = '通報対象の募集';
        $workPost->body = '通報対象の募集本文です。';
        $workPost->purpose = 'work';
        $workPost->location_type = 'online';
        $workPost->meeting_tool = 'Zoom';
        $workPost->time_zone = 'night';
        $workPost->max_participants = 3;
        $workPost->status = WorkPost::STATUS_OPEN;
        $workPost->save();

        return $workPost;
    }

    private function 通報を作成する(array $overrides = []): Report
    {
        $report = new Report();
        $report->reporter_id = $overrides['reporter_id'] ?? $this->reporter->id;
        $report->reported_user_id = $overrides['reported_user_id'] ?? $this->reportedUser->id;
        $report->work_post_id = $overrides['work_post_id'] ?? $this->workPost->id;
        $report->reason = $overrides['reason'] ?? '不適切な募集です。';

        if ($this->カラムが存在する('reports', 'body')) {
            $report->body = $overrides['body'] ?? '通報詳細本文です。';
        }

        if ($this->カラムが存在する('reports', 'detail')) {
            $report->detail = $overrides['detail'] ?? '通報詳細本文です。';
        }

        if ($this->カラムが存在する('reports', 'description')) {
            $report->description = $overrides['description'] ?? '通報詳細本文です。';
        }

        $report->status = $overrides['status'] ?? Report::STATUS_OPEN;
        $report->created_at = $overrides['created_at'] ?? now();
        $report->updated_at = $overrides['updated_at'] ?? $report->created_at;
        $report->save();

        return $report->fresh();
    }

    private function カラムが存在する(string $table, string $column): bool
    {
        return \Illuminate\Support\Facades\Schema::hasColumn($table, $column);
    }

    private function 管理者URL(string $routeName, string $fallback, array $parameters = []): string
    {
        if (Route::has($routeName)) {
            return route($routeName, $parameters);
        }

        return url($fallback);
    }
}
