<?php

namespace Tests\Feature\User;

use App\Models\Article;
use App\Models\Prefecture;
use App\Models\User;
use App\Models\UserTrainingPointHistory;
use App\Models\WorkPost;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Schema;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class HomeRegressionTest extends TestCase
{
    use RefreshDatabase;

    private User $owner;

    protected function setUp(): void
    {
        parent::setUp();

        $this->owner = User::factory()->create([
            'email_verified_at' => now(),
        ]);

        $this->owner->profile()->create([
            'display_name' => '募集投稿者',
            'job_type' => 'Laravelエンジニア',
            'skills' => 'PHP, Laravel',
            'bio' => 'テスト用プロフィールです。',
            'purpose' => '作業仲間を探したい',
            'work_style' => '夜に作業したい',
        ]);
    }

    #[Test]
    public function ホーム画面へアクセスした時_正常に表示できる(): void
    {
        // Arrange

        // Act
        $response = $this->get(route('home'));

        // Assert
        $response->assertOk();
        $response->assertViewIs('home');
        $this->assertNotNull($response->viewData('homeWorkPosts'));
        $this->assertNotNull($response->viewData('homeRankingUsers'));
        $this->assertNotNull($response->viewData('prefectures'));
    }

    #[Test]
    public function ホーム画面で_募集一覧が表示用データに渡される(): void
    {
        // Arrange
        $workPost = $this->募集を作成する([
            'title' => 'ホーム表示用の募集',
            'status' => WorkPost::STATUS_OPEN,
        ]);

        // Act
        $response = $this->get(route('home'));

        // Assert
        $response->assertOk();

        $ids = collect($response->viewData('homeWorkPosts')->items())
            ->pluck('id')
            ->all();

        $this->assertContains($workPost->id, $ids);
    }

    #[Test]
    public function ホーム画面で_キーワード検索に一致する募集だけ表示される(): void
    {
        // Arrange
        $matched = $this->募集を作成する([
            'title' => 'Laravel勉強会の募集',
            'body' => '一緒にLaravelを学習します。',
        ]);

        $unmatched = $this->募集を作成する([
            'title' => 'React作業会の募集',
            'body' => 'Reactの作業をします。',
        ]);

        // Act
        $response = $this->get(route('home', [
            'keyword' => 'Laravel',
        ]));

        // Assert
        $response->assertOk();

        $ids = collect($response->viewData('homeWorkPosts')->items())
            ->pluck('id')
            ->all();

        $this->assertContains($matched->id, $ids);
        $this->assertNotContains($unmatched->id, $ids);
    }

    #[Test]
    public function ホーム画面で_目的で募集を絞り込める(): void
    {
        // Arrange
        $matched = $this->募集を作成する([
            'purpose' => '勉強',
        ]);

        $unmatched = $this->募集を作成する([
            'purpose' => '情報交換',
        ]);

        // Act
        $response = $this->get(route('home', [
            'purpose' => '勉強',
        ]));

        // Assert
        $response->assertOk();

        $ids = collect($response->viewData('homeWorkPosts')->items())
            ->pluck('id')
            ->all();

        $this->assertContains($matched->id, $ids);
        $this->assertNotContains($unmatched->id, $ids);
    }

    #[Test]
    public function ホーム画面で_開催形式で募集を絞り込める(): void
    {
        // Arrange
        $matched = $this->募集を作成する([
            'location_type' => WorkPost::LOCATION_ONLINE,
        ]);

        $unmatched = $this->募集を作成する([
            'location_type' => WorkPost::LOCATION_OFFLINE,
        ]);

        // Act
        $response = $this->get(route('home', [
            'location_type' => WorkPost::LOCATION_ONLINE,
        ]));

        // Assert
        $response->assertOk();

        $ids = collect($response->viewData('homeWorkPosts')->items())
            ->pluck('id')
            ->all();

        $this->assertContains($matched->id, $ids);
        $this->assertNotContains($unmatched->id, $ids);
    }

    #[Test]
    public function ホーム画面で_時間帯で募集を絞り込める(): void
    {
        // Arrange
        $matched = $this->募集を作成する([
            'time_zone' => WorkPost::TIME_ZONE_NIGHT,
        ]);

        $unmatched = $this->募集を作成する([
            'time_zone' => WorkPost::TIME_ZONE_MORNING,
        ]);

        // Act
        $response = $this->get(route('home', [
            'time_zone' => WorkPost::TIME_ZONE_NIGHT,
        ]));

        // Assert
        $response->assertOk();

        $ids = collect($response->viewData('homeWorkPosts')->items())
            ->pluck('id')
            ->all();

        $this->assertContains($matched->id, $ids);
        $this->assertNotContains($unmatched->id, $ids);
    }

    #[Test]
    public function ホーム画面で_都道府県で募集を絞り込める(): void
    {
        // Arrange
        $tokyo = $this->都道府県を作成する('tokyo', '東京都');
        $osaka = $this->都道府県を作成する('osaka', '大阪府');

        $matched = $this->募集を作成する([
            'prefecture_id' => $tokyo->id,
        ]);

        $unmatched = $this->募集を作成する([
            'prefecture_id' => $osaka->id,
        ]);

        // Act
        $response = $this->get(route('home', [
            'prefecture_id' => $tokyo->id,
        ]));

        // Assert
        $response->assertOk();

        $ids = collect($response->viewData('homeWorkPosts')->items())
            ->pluck('id')
            ->all();

        $this->assertContains($matched->id, $ids);
        $this->assertNotContains($unmatched->id, $ids);
    }

    #[Test]
    public function ホーム画面で_ランキングモードがtotalの場合_累計ランキングが表示用データに渡される(): void
    {
        // Arrange
        $rankingUser = User::factory()->create([
            'email_verified_at' => now(),
        ]);

        $rankingUser->profile()->create([
            'display_name' => 'ランキングユーザー',
            'job_type' => 'エンジニア',
        ]);

        UserTrainingPointHistory::create([
            'user_id' => $rankingUser->id,
            'training_type' => 'summary',
            'training_id' => 1,
            'point_type' => 'training',
            'points' => 10,
            'earned_on' => now()->subMonth(),
            'note' => 'テスト用ポイント',
        ]);

        // Act
        $response = $this->get(route('home', [
            'ranking_mode' => 'total',
        ]));

        // Assert
        $response->assertOk();
        $this->assertSame('total', $response->viewData('rankingMode'));

        $userIds = $response->viewData('homeRankingUsers')
            ->pluck('user_id')
            ->all();

        $this->assertContains($rankingUser->id, $userIds);
    }

    #[Test]
    public function ホーム画面で_記事がある場合_記事データが表示用データに渡される(): void
    {
        // Arrange
        $article = $this->記事を作成する();

        // Act
        $response = $this->get(route('home'));

        // Assert
        $response->assertOk();

        $articleIds = $response->viewData('homeArticles')
            ->pluck('id')
            ->all();

        $this->assertContains($article->id, $articleIds);
    }

    private function 募集を作成する(array $overrides = []): WorkPost
    {
        $workPost = new WorkPost();

        $data = [
            'user_id' => $this->owner->id,
            'title' => 'ホーム用募集' . uniqid(),
            'body' => 'ホーム用募集本文です。',
            'purpose' => '黙々作業',
            'location_type' => WorkPost::LOCATION_ONLINE,
            'meeting_tool' => 'Zoom',
            'prefecture_id' => null,
            'start_at' => now()->addDay(),
            'end_at' => now()->addDay()->addHour(),
            'time_zone' => WorkPost::TIME_ZONE_NIGHT,
            'max_participants' => 3,
            'status' => WorkPost::STATUS_OPEN,
        ];

        foreach ([...$data, ...$overrides] as $column => $value) {
            $workPost->{$column} = $value;
        }

        $workPost->save();

        return $workPost;
    }

    private function 都道府県を作成する(string $slug, string $name): Prefecture
    {
        $prefecture = new Prefecture();
        $prefecture->slug = $slug;
        $prefecture->name = $name;
        $prefecture->save();

        return $prefecture;
    }

    private function 記事を作成する(): Article
    {
        $article = new Article();

        $data = [
            'title' => 'ホーム表示用記事',
            'slug' => 'home-article-' . uniqid(),
            'status' => Article::STATUS_PUBLIC,
            'published_at' => now()->subDay(),
            'body' => '本文です。',
            'body_html' => '<p>本文です。</p>',
            'html' => '<p>本文です。</p>',
            'css' => '',
            'seo_title' => 'SEOタイトル',
            'seo_description' => 'SEO説明文',
            'seo_description_text' => 'SEO説明文',
        ];

        foreach ($data as $column => $value) {
            if (Schema::hasColumn('articles', $column)) {
                $article->{$column} = $value;
            }
        }

        $article->save();

        return $article;
    }
}
