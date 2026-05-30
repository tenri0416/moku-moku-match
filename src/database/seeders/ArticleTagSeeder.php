<?php

namespace Database\Seeders;

use App\Models\ArticleTag;
use Illuminate\Database\Seeder;

class ArticleTagSeeder extends Seeder
{
    /**
     * 記事タグの初期データを登録する
     */
    public function run(): void
    {
        $tags = [
            [
                'name' => 'フルリモート',
                'slug' => 'remote-work',
                'description' => 'フルリモートで働く人向けの記事に使用します。',
            ],
            [
                'name' => 'フリーランス',
                'slug' => 'freelance',
                'description' => 'フリーランスの働き方や自己管理に関する記事に使用します。',
            ],
            [
                'name' => 'もくもく会',
                'slug' => 'mokumoku',
                'description' => 'もくもく会や共同作業に関する記事に使用します。',
            ],
            [
                'name' => '作業仲間',
                'slug' => 'work-partner',
                'description' => '一緒に作業する相手探しに関する記事に使用します。',
            ],
            [
                'name' => '勉強仲間',
                'slug' => 'study-partner',
                'description' => '一緒に勉強する相手探しに関する記事に使用します。',
            ],
            [
                'name' => '孤独対策',
                'slug' => 'loneliness',
                'description' => 'リモートワークや一人作業の孤独対策に関する記事に使用します。',
            ],
            [
                'name' => '集中',
                'slug' => 'focus',
                'description' => '集中力や作業効率に関する記事に使用します。',
            ],
            [
                'name' => '習慣化',
                'slug' => 'habit',
                'description' => '作業や学習を継続する習慣づくりに関する記事に使用します。',
            ],
            [
                'name' => '朝活',
                'slug' => 'morning-work',
                'description' => '朝の作業や朝活に関する記事に使用します。',
            ],
            [
                'name' => '夜活',
                'slug' => 'night-work',
                'description' => '夜の作業や夜活に関する記事に使用します。',
            ],
            [
                'name' => 'オンライン作業',
                'slug' => 'online-work',
                'description' => 'オンラインで一緒に作業することに関する記事に使用します。',
            ],
            [
                'name' => 'オフライン作業',
                'slug' => 'offline-work',
                'description' => '対面で一緒に作業することに関する記事に使用します。',
            ],
            [
                'name' => '自宅作業',
                'slug' => 'home-work',
                'description' => '自宅での作業環境や働き方に関する記事に使用します。',
            ],
            [
                'name' => 'カフェ作業',
                'slug' => 'cafe-work',
                'description' => 'カフェで作業する方法や注意点に関する記事に使用します。',
            ],
            [
                'name' => 'コワーキング',
                'slug' => 'coworking',
                'description' => 'コワーキングスペースの活用に関する記事に使用します。',
            ],
            [
                'name' => 'リモートワーカー',
                'slug' => 'remote-worker',
                'description' => 'リモートワーカー向けの記事に使用します。',
            ],
            [
                'name' => 'エンジニア',
                'slug' => 'engineer',
                'description' => 'エンジニアや開発者向けの記事に使用します。',
            ],
            [
                'name' => 'デザイナー',
                'slug' => 'designer',
                'description' => 'デザイナー向けの記事に使用します。',
            ],
            [
                'name' => 'ライター',
                'slug' => 'writer',
                'description' => 'ライターや文章を書く仕事の人向けの記事に使用します。',
            ],
            [
                'name' => 'プログラミング学習',
                'slug' => 'programming-learning',
                'description' => 'プログラミング学習に関する記事に使用します。',
            ],
            [
                'name' => '副業',
                'slug' => 'side-job',
                'description' => '副業や複業に関する記事に使用します。',
            ],
            [
                'name' => 'プロフィール',
                'slug' => 'profile',
                'description' => 'プロフィール作成や自己紹介に関する記事に使用します。',
            ],
            [
                'name' => '募集文',
                'slug' => 'recruitment-text',
                'description' => '作業仲間を募集する文章に関する記事に使用します。',
            ],
            [
                'name' => 'マッチング',
                'slug' => 'matching',
                'description' => '作業仲間とのマッチングに関する記事に使用します。',
            ],
            [
                'name' => 'コミュニティ',
                'slug' => 'community',
                'description' => 'オンライン・オフラインのコミュニティに関する記事に使用します。',
            ],
            [
                'name' => 'モチベーション',
                'slug' => 'motivation',
                'description' => '作業や学習のモチベーション維持に関する記事に使用します。',
            ],
            [
                'name' => '時間管理',
                'slug' => 'time-management',
                'description' => '時間管理やスケジュール管理に関する記事に使用します。',
            ],
            [
                'name' => '生産性',
                'slug' => 'productivity',
                'description' => '生産性や作業効率に関する記事に使用します。',
            ],
            [
                'name' => '初心者向け',
                'slug' => 'beginner',
                'description' => 'MokuMoku Matchやもくもく会を初めて使う人向けの記事に使用します。',
            ],
            [
                'name' => '使い方',
                'slug' => 'how-to-use',
                'description' => 'MokuMoku Matchの使い方に関する記事に使用します。',
            ],
        ];

        foreach ($tags as $index => $tag) {
            ArticleTag::updateOrCreate(
                ['slug' => $tag['slug']],
                [
                    'name' => $tag['name'],
                    'description' => $tag['description'],
                    'sort_order' => ($index + 1) * 10,
                    'is_active' => true,
                ]
            );
        }
    }
}
