<?php

namespace Tests\Feature\User;

use App\Models\Article;
use App\Models\ArticleCategory;
use App\Models\ArticleTag;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Schema;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class ArticleRegressionTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function 記事一覧で_公開済み記事が表示用データに渡される(): void
    {
        // Arrange
        $publicArticle = $this->記事を作成する([
            'title' => '公開済み記事',
            'slug' => 'public-article',
            'status' => Article::STATUS_PUBLIC,
            'published_at' => now()->subDay(),
        ]);

        $this->記事を作成する([
            'title' => '未来公開記事',
            'slug' => 'future-article',
            'status' => Article::STATUS_PUBLIC,
            'published_at' => now()->addDay(),
        ]);

        // Act
        $response = $this->get($this->記事URL('articles.index', '/articles'));

        // Assert
        $response->assertOk();
        $response->assertViewIs('articles.index');

        $articles = $response->viewData('articles');
        $ids = collect($articles->items())->pluck('id')->all();

        $this->assertContains($publicArticle->id, $ids);
    }

    #[Test]
    public function 記事詳細で_公開済み記事なら表示でき閲覧履歴が作成される(): void
    {
        // Arrange
        $user = User::factory()->create(['email_verified_at' => now()]);

        $article = $this->記事を作成する([
            'title' => '詳細表示記事',
            'slug' => 'detail-article',
            'status' => Article::STATUS_PUBLIC,
            'published_at' => now()->subDay(),
        ]);

        // Act
        $response = $this
            ->actingAs($user)
            ->get($this->記事URL('articles.show', "/articles/{$article->slug}", [$article]));

        // Assert
        $response->assertOk();
        $response->assertViewIs('articles.show');
        $this->assertSame($article->id, $response->viewData('article')->id);

        $this->assertDatabaseHas('article_views', [
            'article_id' => $article->id,
            'user_id' => $user->id,
        ]);
    }

    #[Test]
    public function 記事詳細で_非公開記事なら404になる(): void
    {
        // Arrange
        $article = $this->記事を作成する([
            'title' => '非公開記事',
            'slug' => 'private-article',
            'status' => 1,
            'published_at' => now()->subDay(),
        ]);

        // Act
        $response = $this->get($this->記事URL('articles.show', "/articles/{$article->slug}", [$article]));

        // Assert
        $response->assertNotFound();
    }

    #[Test]
    public function 記事詳細で_未来公開記事なら404になる(): void
    {
        // Arrange
        $article = $this->記事を作成する([
            'title' => '未来公開記事',
            'slug' => 'future-show-article',
            'status' => Article::STATUS_PUBLIC,
            'published_at' => now()->addDay(),
        ]);

        // Act
        $response = $this->get($this->記事URL('articles.show', "/articles/{$article->slug}", [$article]));

        // Assert
        $response->assertNotFound();
    }

    #[Test]
    public function 短縮URLで_公開済み記事を表示できる(): void
    {
        // Arrange
        $article = $this->記事を作成する([
            'title' => '短縮URL記事',
            'slug' => 'short-article',
            'short_slug' => 'short-test',
            'status' => Article::STATUS_PUBLIC,
            'published_at' => now()->subDay(),
        ]);

        // Act
        $response = $this->get('/short-test');

        // Assert
        $response->assertOk();
        $response->assertViewIs('articles.show');
        $this->assertSame($article->id, $response->viewData('article')->id);
    }

    #[Test]
    public function カテゴリ記事一覧で_有効カテゴリなら対象記事が表示される(): void
    {
        // Arrange
        $category = $this->カテゴリを作成する(true);

        $article = $this->記事を作成する([
            'title' => 'カテゴリ記事',
            'slug' => 'category-article',
            'article_category_id' => $category->id,
            'status' => Article::STATUS_PUBLIC,
            'published_at' => now()->subDay(),
        ]);

        // Act
        $response = $this->get($this->記事URL('articles.category', "/articles/category/{$category->slug}", [$category]));

        // Assert
        $response->assertOk();

        $ids = collect($response->viewData('articles')->items())->pluck('id')->all();
        $this->assertContains($article->id, $ids);
        $this->assertSame($category->id, $response->viewData('currentCategory')->id);
    }

    #[Test]
    public function カテゴリ記事一覧で_無効カテゴリなら404になる(): void
    {
        // Arrange
        $category = $this->カテゴリを作成する(false);

        // Act
        $response = $this->get($this->記事URL('articles.category', "/articles/category/{$category->slug}", [$category]));

        // Assert
        $response->assertNotFound();
    }

    #[Test]
    public function タグ記事一覧で_有効タグなら対象記事が表示される(): void
    {
        // Arrange
        $tag = $this->タグを作成する(true);

        $article = $this->記事を作成する([
            'title' => 'タグ記事',
            'slug' => 'tag-article',
            'status' => Article::STATUS_PUBLIC,
            'published_at' => now()->subDay(),
        ]);

        $this->記事とタグを紐づける($article, $tag);

        // Act
        $response = $this->get($this->記事URL('articles.tag', "/articles/tag/{$tag->slug}", [$tag]));

        // Assert
        $response->assertOk();

        $ids = collect($response->viewData('articles')->items())->pluck('id')->all();
        $this->assertContains($article->id, $ids);
        $this->assertSame($tag->id, $response->viewData('currentTag')->id);
    }

    #[Test]
    public function タグ記事一覧で_無効タグなら404になる(): void
    {
        // Arrange
        $tag = $this->タグを作成する(false);

        // Act
        $response = $this->get($this->記事URL('articles.tag', "/articles/tag/{$tag->slug}", [$tag]));

        // Assert
        $response->assertNotFound();
    }

    private function 記事を作成する(array $attributes): Article
    {
        $article = new Article();

        $defaults = [
            'title' => 'テスト記事',
            'slug' => 'test-article-' . uniqid(),
            'short_slug' => null,
            'status' => Article::STATUS_PUBLIC,
            'published_at' => now()->subDay(),
            'body' => '本文です。',
            'body_html' => '<p>本文です。</p>',
            'html' => '<p>本文です。</p>',
            'css' => '',
            'seo_title' => 'SEOタイトル',
            'seo_description' => 'SEO説明文',
            'seo_description_text' => 'SEO説明文',
            'article_category_id' => null,
        ];

        foreach ([...$defaults, ...$attributes] as $column => $value) {
            if (Schema::hasColumn('articles', $column)) {
                $article->{$column} = $value;
            }
        }

        $article->save();

        return $article->fresh();
    }

    private function カテゴリを作成する(bool $isActive): ArticleCategory
    {
        $category = new ArticleCategory();
        $category->name = $isActive ? '有効カテゴリ' : '無効カテゴリ';
        $category->slug = $isActive ? 'active-category' : 'inactive-category';
        $category->description = 'カテゴリ説明文';
        $category->is_active = $isActive;
        $category->save();

        return $category;
    }

    private function タグを作成する(bool $isActive): ArticleTag
    {
        $tag = new ArticleTag();
        $tag->name = $isActive ? '有効タグ' : '無効タグ';
        $tag->slug = $isActive ? 'active-tag' : 'inactive-tag';
        $tag->description = 'タグ説明文';
        $tag->is_active = $isActive;
        $tag->save();

        return $tag;
    }

    private function 記事とタグを紐づける(Article $article, ArticleTag $tag): void
    {
        if (Schema::hasTable('article_article_tag')) {
            DB::table('article_article_tag')->insert([
                'article_id' => $article->id,
                'article_tag_id' => $tag->id,
            ]);

            return;
        }

        if (Schema::hasTable('article_tag')) {
            DB::table('article_tag')->insert([
                'article_id' => $article->id,
                'article_tag_id' => $tag->id,
            ]);
        }
    }

    private function 記事URL(string $routeName, string $fallback, array $parameters = []): string
    {
        if (Route::has($routeName)) {
            return route($routeName, $parameters);
        }

        return url($fallback);
    }
}
