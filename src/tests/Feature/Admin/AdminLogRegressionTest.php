<?php

namespace Tests\Feature\Admin;

use App\Models\Admin;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\Attributes\DataProvider;

class AdminLogRegressionTest extends TestCase
{
    use RefreshDatabase;

    private Admin $admin;

    /**
     * このテストで作成したログファイル名
     *
     * @var array<int, string>
     */
    private array $createdLogFiles = [];

    protected function setUp(): void
    {
        parent::setUp();

        $this->admin = Admin::create([
            'name' => 'テスト管理者',
            'email' => 'admin-log-regression@example.com',
            'email_verified_at' => now(),
            'password' => Hash::make('password'),
            'status' => 1,
            'remember_token' => null,
        ]);

        File::ensureDirectoryExists(storage_path('logs'));
    }

    protected function tearDown(): void
    {
        foreach ($this->createdLogFiles as $fileName) {
            $path = storage_path('logs/' . $fileName);

            if (File::exists($path)) {
                File::delete($path);
            }
        }

        parent::tearDown();
    }

    #[Test]
    public function 管理者ログ一覧で_ログ種別を指定しない場合_エラーログがデフォルト表示される(): void
    {
        // Arrange
        $errorLogName = 'error-2099-02-01.log';
        $laravelLogName = 'laravel-2099-02-01.log';

        $this->ログファイルを作成する($errorLogName, 'エラーログ本文');
        $this->ログファイルを作成する($laravelLogName, '通常ログ本文');

        // Act
        $response = $this
            ->actingAs($this->admin, 'admin')
            ->get(route('admin.logs.index'));

        // Assert
        $response->assertOk();

        $this->assertSame('error', $response->viewData('type'));

        $logFileNames = $response->viewData('logFiles')
            ->pluck('name')
            ->values()
            ->all();

        $this->assertContains($errorLogName, $logFileNames);
        $this->assertNotContains($laravelLogName, $logFileNames);
    }

    #[Test]
    public function 管理者ログ一覧で_通常ログを指定した場合_通常ログのみ表示される(): void
    {
        // Arrange
        $errorLogName = 'error-2099-02-02.log';
        $laravelLogName = 'laravel-2099-02-02.log';

        $this->ログファイルを作成する($errorLogName, 'エラーログ本文');
        $this->ログファイルを作成する($laravelLogName, '通常ログ本文');

        // Act
        $response = $this
            ->actingAs($this->admin, 'admin')
            ->get(route('admin.logs.index', [
                'type' => 'laravel',
            ]));

        // Assert
        $response->assertOk();

        $this->assertSame('laravel', $response->viewData('type'));

        $logFileNames = $response->viewData('logFiles')
            ->pluck('name')
            ->values()
            ->all();

        $this->assertContains($laravelLogName, $logFileNames);
        $this->assertNotContains($errorLogName, $logFileNames);
    }

    #[Test]
    public function 管理者ログ一覧で_エラーログと日付を指定した場合_対象日のエラーログのみ表示される(): void
    {
        // Arrange
        $targetLogName = 'error-2099-02-03.log';
        $otherDateLogName = 'error-2099-02-04.log';
        $sameDateLaravelLogName = 'laravel-2099-02-03.log';

        $this->ログファイルを作成する($targetLogName, '対象日のエラーログ');
        $this->ログファイルを作成する($otherDateLogName, '別日のエラーログ');
        $this->ログファイルを作成する($sameDateLaravelLogName, '同日の通常ログ');

        // Act
        $response = $this
            ->actingAs($this->admin, 'admin')
            ->get(route('admin.logs.index', [
                'type' => 'error',
                'date' => '2099-02-03',
            ]));

        // Assert
        $response->assertOk();

        $this->assertSame('error', $response->viewData('type'));
        $this->assertSame('2099-02-03', $response->viewData('date'));

        $logFileNames = $response->viewData('logFiles')
            ->pluck('name')
            ->values()
            ->all();

        $this->assertSame([$targetLogName], $logFileNames);
    }

    #[Test]
    public function 管理者ログ一覧で_通常ログと日付を指定した場合_対象日の通常ログのみ表示される(): void
    {
        // Arrange
        $targetLogName = 'laravel-2099-02-05.log';
        $otherDateLogName = 'laravel-2099-02-06.log';
        $sameDateErrorLogName = 'error-2099-02-05.log';

        $this->ログファイルを作成する($targetLogName, '対象日の通常ログ');
        $this->ログファイルを作成する($otherDateLogName, '別日の通常ログ');
        $this->ログファイルを作成する($sameDateErrorLogName, '同日のエラーログ');

        // Act
        $response = $this
            ->actingAs($this->admin, 'admin')
            ->get(route('admin.logs.index', [
                'type' => 'laravel',
                'date' => '2099-02-05',
            ]));

        // Assert
        $response->assertOk();

        $this->assertSame('laravel', $response->viewData('type'));
        $this->assertSame('2099-02-05', $response->viewData('date'));

        $logFileNames = $response->viewData('logFiles')
            ->pluck('name')
            ->values()
            ->all();

        $this->assertSame([$targetLogName], $logFileNames);
    }

    #[Test]
    public function 管理者ログ一覧で_不正なログ種別を指定した場合_エラーログ表示へ戻る(): void
    {
        // Arrange
        $errorLogName = 'error-2099-02-07.log';
        $laravelLogName = 'laravel-2099-02-07.log';

        $this->ログファイルを作成する($errorLogName, 'エラーログ本文');
        $this->ログファイルを作成する($laravelLogName, '通常ログ本文');

        // Act
        $response = $this
            ->actingAs($this->admin, 'admin')
            ->get(route('admin.logs.index', [
                'type' => 'unknown',
            ]));

        // Assert
        $response->assertOk();

        $this->assertSame('error', $response->viewData('type'));

        $logFileNames = $response->viewData('logFiles')
            ->pluck('name')
            ->values()
            ->all();

        $this->assertContains($errorLogName, $logFileNames);
        $this->assertNotContains($laravelLogName, $logFileNames);
    }

    #[Test]
    public function 管理者ログ詳細で_許可されたエラーログを指定した場合_ログ本文を確認できる(): void
    {
        // Arrange
        $fileName = 'error-2099-02-08.log';
        $content = '[2099-02-08 10:00:00] production.ERROR: テスト用エラーです。';

        $this->ログファイルを作成する($fileName, $content);

        // Act
        $response = $this
            ->actingAs($this->admin, 'admin')
            ->get(route('admin.logs.show', [
                'file' => $fileName,
                'type' => 'error',
                'date' => '2099-02-08',
            ]));

        // Assert
        $response->assertOk();

        $this->assertSame($fileName, $response->viewData('file'));
        $this->assertSame('error', $response->viewData('type'));
        $this->assertSame('2099-02-08', $response->viewData('date'));
        $this->assertSame($content, $response->viewData('content'));
    }

    #[Test]
    public function 管理者ログ詳細で_許可された通常ログを指定した場合_ログ本文を確認できる(): void
    {
        // Arrange
        $fileName = 'laravel-2099-02-09.log';
        $content = '[2099-02-09 10:00:00] local.INFO: テスト用通常ログです。';

        $this->ログファイルを作成する($fileName, $content);

        // Act
        $response = $this
            ->actingAs($this->admin, 'admin')
            ->get(route('admin.logs.show', [
                'file' => $fileName,
                'type' => 'laravel',
                'date' => '2099-02-09',
            ]));

        // Assert
        $response->assertOk();

        $this->assertSame($fileName, $response->viewData('file'));
        $this->assertSame('laravel', $response->viewData('type'));
        $this->assertSame('2099-02-09', $response->viewData('date'));
        $this->assertSame($content, $response->viewData('content'));
    }

    /**
     * @dataProvider 不正なログファイル名一覧
     */
    #[DataProvider('不正なログファイル名一覧')]
    #[Test]
    public function 管理者ログ詳細で_許可されていないファイル名を指定した場合_404になる(string $fileName): void
    {
        // Arrange

        // Act
        $response = $this
            ->actingAs($this->admin, 'admin')
            ->get(route('admin.logs.show', [
                'file' => $fileName,
            ]));

        // Assert
        $response->assertNotFound();
    }

    #[Test]
    public static function 不正なログファイル名一覧(): array
    {
        return [
            'ディレクトリトラバーサル' => ['../.env'],
            '許可していないprefix' => ['debug-2099-02-01.log'],
            '拡張子がlogではない' => ['error-2099-02-01.txt'],
            '日付形式が不正' => ['error-2099-2-1.log'],
            '任意のファイル名' => ['not-allowed.log'],
        ];
    }

    private function ログファイルを作成する(string $fileName, string $content): void
    {
        File::put(storage_path('logs/' . $fileName), $content);

        $this->createdLogFiles[] = $fileName;
    }
}
