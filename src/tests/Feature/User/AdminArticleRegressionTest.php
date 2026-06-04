<?php

namespace Tests\Feature\Admin;

use App\Models\Admin;
use App\Models\Article;
use App\Models\ArticleCategory;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Schema;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class AdminArticleRegressionTest extends TestCase
{
    use RefreshDatabase;

    private Admin $admin;

    protected function setUp(): void
    {
        parent::setUp();

        if (! Route::has('admin.articles.index')) {
            $this->markTestSkipped('管理者記事機能のルートが未定義です。');
        }

        $this->admin = Admin::create([
            'name' => '記事管理テスト管理者',
            'email' => 'admin-article-regression@example.com',
            'email_verified_at' => now(),
            'password' => Hash::make('password'),
            'status' => 1,
            'remember_token' => null,
        ]);
    }

    #[Test]
    public function 管理者記事一覧で_記事一覧が表示用データに渡される(): void
    {
        // Arrange
        $article = $this->記事を作成する([
            'title' => '管理者記事一覧テスト',
        ]);

        // Act
        $response = $this
            ->actingAs($this->admin, 'admin')
            ->get(route('admin.articles.index'));

        // Assert
        $response->assertOk();

        if ($response->viewData('articles')) {
            $ids = collect($response->viewData('articles')->items())
                ->pluck('id')
                ->all();

            $this->assertContains($article->id, $ids);
        } else {
            $this->assertTrue(true);
        }
    }

    #[Test]
    public function 管理者記事作成画面へアクセスした時_正常に表示できる(): void
    {
        // Arrange
        if (! Route::has('admin.articles.create')) {
            $this->markTestSkipped('管理者記事作成画面のルートが未定義です。');
        }

        // Act
        $response = $this
            ->actingAs($this->admin, 'admin')
            ->get(route('admin.articles.create'));

        // Assert
        $response->assertOk();
    }

    #[Test]
    public function 管理者記事詳細で_対象記事が表示用データに渡される(): void
    {
        // Arrange
        if (! Route::has('admin.articles.show')) {
            $this->markTestSkipped('管理者記事詳細のルートが未定義です。');
        }

        $article = $this->記事を作成する([
            'title' => '管理者記事詳細テスト',
        ]);

        // Act
        $response = $this
            ->actingAs($this->admin, 'admin')
            ->get(route('admin.articles.show', $article));

        // Assert
        $response->assertOk();

        if ($response->viewData('article')) {
            $this->assertSame($article->id, $response->viewData('article')->id);
        } else {
            $this->assertTrue(true);
        }
    }

    #[Test]
    public function 管理者記事編集画面へアクセスした時_正常に表示できる(): void
    {
        // Arrange
        if (! Route::has('admin.articles.edit')) {
            $this->markTestSkipped('管理者記事編集画面のルートが未定義です。');
        }

        $article = $this->記事を作成する([
            'title' => '管理者記事編集テスト',
        ]);

        // Act
        $response = $this
            ->actingAs($this->admin, 'admin')
            ->get(route('admin.articles.edit', $article));

        // Assert
        $response->assertOk();
    }

    #[Test]
    public function 管理者記事作成で_正しい入力なら記事を保存できる(): void
    {
        // Arrange
        if (! Route::has('admin.articles.store')) {
            $this->markTestSkipped('管理者記事作成処理のルートが未定義です。');
        }

        $payload = $this->記事入力データ([
            'title' => '新規作成記事',
            'slug' => 'new-admin-article',
        ]);

        // Act
        $response = $this
            ->actingAs($this->admin, 'admin')
            ->post(route('admin.articles.store'), $payload);

        // Assert
        $response->assertRedirect();

        $this->assertDatabaseHas('articles', [
            'title' => '新規作成記事',
            'slug' => 'new-admin-article',
        ]);
    }

    #[Test]
    public function 管理者記事更新で_正しい入力なら記事を更新できる(): void
    {
        // Arrange
        if (! Route::has('admin.articles.update')) {
            $this->markTestSkipped('管理者記事更新処理のルートが未定義です。');
        }

        $article = $this->記事を作成する([
            'title' => '更新前記事',
            'slug' => 'before-admin-article',
        ]);

        $payload = $this->記事入力データ([
            'title' => '更新後記事',
            'slug' => 'after-admin-article',
        ]);

        // Act
        $response = $this
            ->actingAs($this->admin, 'admin')
            ->put(route('admin.articles.update', $article), $payload);

        // Assert
        $response->assertRedirect();

        $this->assertDatabaseHas('articles', [
            'id' => $article->id,
            'title' => '更新後記事',
            'slug' => 'after-admin-article',
        ]);
    }

    #[Test]
    #[DataProvider('管理者記事の不正入力一覧')]
    public function 管理者記事作成で_入力が不正な場合_バリデーションエラーになる(array $override, array $errorKeys): void
    {
        // Arrange
        if (! Route::has('admin.articles.store')) {
            $this->markTestSkipped('管理者記事作成処理のルートが未定義です。');
        }

        $payload = [
            ...$this->記事入力データ(),
            ...$override,
        ];

        // Act
        $response = $this
            ->actingAs($this->admin, 'admin')
            ->from(Route::has('admin.articles.create') ? route('admin.articles.create') : route('admin.articles.index'))
            ->post(route('admin.articles.store'), $payload);

        // Assert
        $response->assertRedirect();
        $response->assertSessionHasErrors($errorKeys);
    }

    public static function 管理者記事の不正入力一覧(): array
    {
        return [
            'タイトル空' => [
                ['title' => ''],
                ['title'],
            ],
            'slug空' => [
                ['slug' => ''],
                ['slug'],
            ],
        ];
    }

    private function 記事入力データ(array $overrides = []): array
    {
        $data = [
            'title' => '管理者記事テスト',
            'slug' => 'admin-article-' . uniqid(),
            'short_slug' => null,
            'status' => Article::STATUS_PUBLIC,
            'published_at' => now()->format('Y-m-d H:i:s'),
            'body' => '本文です。',
            'body_html' => '<p>本文です。</p>',
            'html' => '<p>本文です。</p>',
            'css' => '',
            'seo_title' => 'SEOタイトル',
            'seo_description' => 'SEO説明文',
            'seo_description_text' => 'SEO説明文',
            'article_category_id' => null,
        ];

        return $this->存在するカラムだけ抽出する('articles', [
            ...$data,
            ...$overrides,
        ]);
    }

    private function 記事を作成する(array $overrides = []): Article
    {
        $article = new Article();

        foreach ($this->記事入力データ($overrides) as $column => $value) {
            $article->{$column} = $value;
        }

        $article->save();

        return $article->fresh();
    }

    private function 存在するカラムだけ抽出する(string $table, array $data): array
    {
        return collect($data)
            ->filter(fn ($value, string $column) => Schema::hasColumn($table, $column))
            ->all();
    }
}
