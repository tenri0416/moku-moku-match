<?php

namespace Database\Seeders;

use App\Models\ArticleCategory;
use Illuminate\Database\Seeder;

class ArticleCategorySeeder extends Seeder
{
    /**
     * 記事カテゴリーの初期データを登録する
     *
     * 最大3階層まで。
     */
    public function run(): void
    {
        $categories = [
            [
                'name' => '働き方',
                'slug' => 'work-style',
                'description' => 'フリーランス、リモートワーク、副業など、働き方に関する記事カテゴリーです。',
                'children' => [
                    [
                        'name' => 'フルリモート',
                        'slug' => 'remote-work',
                        'description' => 'フルリモートで働く人向けの働き方・習慣・課題解決に関する記事です。',
                        'children' => [
                            [
                                'name' => '孤独対策',
                                'slug' => 'remote-work-loneliness',
                                'description' => 'フルリモートで感じやすい孤独感や孤立を防ぐための記事です。',
                            ],
                            [
                                'name' => '集中環境',
                                'slug' => 'remote-work-focus',
                                'description' => '自宅や外出先で集中して作業するための環境づくりに関する記事です。',
                            ],
                            [
                                'name' => '生活リズム',
                                'slug' => 'remote-work-routine',
                                'description' => 'リモートワーク中の生活リズムや作業習慣を整える記事です。',
                            ],
                        ],
                    ],
                    [
                        'name' => 'フリーランス',
                        'slug' => 'freelance',
                        'description' => 'フリーランスとして働く人向けの働き方・案件・自己管理に関する記事です。',
                        'children' => [
                            [
                                'name' => '自己管理',
                                'slug' => 'freelance-self-management',
                                'description' => 'フリーランスの時間管理、体調管理、モチベーション維持に関する記事です。',
                            ],
                            [
                                'name' => '作業習慣',
                                'slug' => 'freelance-work-habit',
                                'description' => '継続して作業するための習慣づくりに関する記事です。',
                            ],
                        ],
                    ],
                ],
            ],
            [
                'name' => '作業仲間',
                'slug' => 'work-partner',
                'description' => '一緒に作業する相手や、作業コミュニティに関する記事カテゴリーです。',
                'children' => [
                    [
                        'name' => 'もくもく会',
                        'slug' => 'mokumoku-meetup',
                        'description' => 'もくもく会の参加方法、開催方法、活用方法に関する記事です。',
                        'children' => [
                            [
                                'name' => 'オンラインもくもく会',
                                'slug' => 'online-mokumoku',
                                'description' => 'オンラインで一緒に作業するための方法やコツに関する記事です。',
                            ],
                            [
                                'name' => 'オフラインもくもく会',
                                'slug' => 'offline-mokumoku',
                                'description' => 'カフェや会議室などで一緒に作業する方法に関する記事です。',
                            ],
                        ],
                    ],
                    [
                        'name' => '作業相手探し',
                        'slug' => 'find-work-partner',
                        'description' => '一緒に作業できる相手の探し方や声かけ方法に関する記事です。',
                        'children' => [
                            [
                                'name' => '募集のコツ',
                                'slug' => 'recruitment-tips',
                                'description' => '作業仲間を募集するときの文章や条件設定のコツに関する記事です。',
                            ],
                            [
                                'name' => 'マッチング活用',
                                'slug' => 'matching-tips',
                                'description' => 'MokuMoku Matchのようなマッチングサービスを活用するための記事です。',
                            ],
                        ],
                    ],
                ],
            ],
            [
                'name' => '作業環境',
                'slug' => 'workspace',
                'description' => '自宅、カフェ、コワーキングスペースなど、作業環境に関する記事カテゴリーです。',
                'children' => [
                    [
                        'name' => '自宅作業',
                        'slug' => 'home-workspace',
                        'description' => '自宅で快適に作業するための環境づくりに関する記事です。',
                    ],
                    [
                        'name' => 'カフェ作業',
                        'slug' => 'cafe-work',
                        'description' => 'カフェで作業するときの注意点や集中方法に関する記事です。',
                    ],
                    [
                        'name' => 'コワーキング',
                        'slug' => 'coworking',
                        'description' => 'コワーキングスペースの活用方法や選び方に関する記事です。',
                    ],
                ],
            ],
            [
                'name' => '学習・スキルアップ',
                'slug' => 'learning',
                'description' => 'プログラミング学習、資格学習、勉強仲間探しに関する記事カテゴリーです。',
                'children' => [
                    [
                        'name' => 'プログラミング学習',
                        'slug' => 'programming-learning',
                        'description' => 'プログラミング学習を継続するための記事です。',
                    ],
                    [
                        'name' => '勉強仲間',
                        'slug' => 'study-partner',
                        'description' => '一緒に勉強する仲間の見つけ方や継続のコツに関する記事です。',
                    ],
                    [
                        'name' => '学習習慣',
                        'slug' => 'learning-habit',
                        'description' => '学習を継続するための習慣づくりに関する記事です。',
                    ],
                ],
            ],
            [
                'name' => 'MokuMoku Match活用',
                'slug' => 'mokumoku-match-guide',
                'description' => 'MokuMoku Matchの使い方や活用方法に関する記事カテゴリーです。',
                'children' => [
                    [
                        'name' => '募集作成',
                        'slug' => 'create-work-post',
                        'description' => '作業仲間を募集する投稿の作り方に関する記事です。',
                    ],
                    [
                        'name' => 'プロフィール作成',
                        'slug' => 'profile-guide',
                        'description' => 'マッチングしやすいプロフィール作成に関する記事です。',
                    ],
                    [
                        'name' => '安心して使う',
                        'slug' => 'safe-use',
                        'description' => 'MokuMoku Matchを安心して使うための注意点やマナーに関する記事です。',
                    ],
                ],
            ],
        ];

        $sortOrder = 10;

        foreach ($categories as $categoryData) {
            $parent = $this->createCategory($categoryData, null, $sortOrder);

            foreach ($categoryData['children'] ?? [] as $childData) {
                $child = $this->createCategory($childData, $parent->id, $sortOrder);

                foreach ($childData['children'] ?? [] as $grandChildData) {
                    $this->createCategory($grandChildData, $child->id, $sortOrder);
                }
            }
        }
    }

    /**
     * カテゴリーを作成または更新する
     */
    private function createCategory(array $data, ?int $parentId, int &$sortOrder): ArticleCategory
    {
        $category = ArticleCategory::updateOrCreate(
            ['slug' => $data['slug']],
            [
                'parent_id' => $parentId,
                'name' => $data['name'],
                'description' => $data['description'] ?? null,
                'sort_order' => $sortOrder,
                'is_active' => true,
            ]
        );

        $sortOrder += 10;

        return $category;
    }
}
