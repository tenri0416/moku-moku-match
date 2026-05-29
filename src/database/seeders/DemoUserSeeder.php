<?php

namespace Database\Seeders;

use App\Models\Profile;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\Prefecture;

class DemoUserSeeder extends Seeder
{
    /**
     * デモ用ユーザーとプロフィールを作成する
     */
    public function run(): void
    {
        $users = [
            [
                'name' => '佐藤 健太',
                'email' => 'kenta.sato@example.com',
                'profile' => [
                    'avatar_path' => null,
                    'display_name' => '佐藤 健太',
                    'job_type' => 'Webエンジニア',
                    'prefecture' => '東京都',
                    'skills' => 'Laravel, PHP, Vue.js, MySQL, Docker',
                    'bio' => 'LaravelとVue.jsを中心に開発しています。フルリモートで働いており、朝の時間帯に一緒に作業できる方を探しています。',
                    'purpose' => '黙々作業・技術相談',
                    'work_style' => '平日午前に集中して作業することが多いです。',
                ],
            ],
            [
                'name' => '田中 美咲',
                'email' => 'misaki.tanaka@example.com',
                'profile' => [
                    'avatar_path' => null,
                    'display_name' => '田中 美咲',
                    'job_type' => 'Webデザイナー',
                    'prefecture' => '大阪府',
                    'skills' => 'Figma, Photoshop, Illustrator, HTML, CSS',
                    'bio' => 'LP制作やバナー制作を中心に活動しています。作業通話やデザインレビューができる相手を探しています。',
                    'purpose' => '黙々作業・デザイン相談',
                    'work_style' => '午前中にデザイン作業、午後に修正対応をすることが多いです。',
                ],
            ],
            [
                'name' => '鈴木 翔太',
                'email' => 'shota.suzuki@example.com',
                'profile' => [
                    'avatar_path' => null,
                    'display_name' => '翔太',
                    'job_type' => 'バックエンドエンジニア',
                    'prefecture' => '神奈川県',
                    'skills' => 'PHP, Laravel, AWS, MySQL, API設計',
                    'bio' => 'API設計やDB設計が得意です。フリーランスとして受託開発をしています。',
                    'purpose' => '情報交換・作業仲間探し',
                    'work_style' => '夜に作業することが多く、週に数回作業通話できる方を探しています。',
                ],
            ],
            [
                'name' => '高橋 由紀',
                'email' => 'yuki.takahashi@example.com',
                'profile' => [
                    'avatar_path' => null,
                    'display_name' => '高橋 由紀',
                    'job_type' => 'ライター',
                    'prefecture' => '京都府',
                    'skills' => 'SEOライティング, WordPress, 記事構成, 校正',
                    'bio' => 'SEO記事や採用広報の記事を執筆しています。一人作業だと集中が切れやすいので、黙々作業できる方を探しています。',
                    'purpose' => '黙々作業',
                    'work_style' => '平日の日中に作業しています。',
                ],
            ],
            [
                'name' => '伊藤 大輔',
                'email' => 'daisuke.ito@example.com',
                'profile' => [
                    'avatar_path' => null,
                    'display_name' => '伊藤 大輔',
                    'job_type' => 'フロントエンドエンジニア',
                    'prefecture' => '愛知県',
                    'skills' => 'React, Next.js, TypeScript, Tailwind CSS',
                    'bio' => 'ReactとNext.jsを使った開発をしています。技術の話をしながら作業できる方とつながりたいです。',
                    'purpose' => '技術相談・情報交換',
                    'work_style' => '平日夜と休日に作業しています。',
                ],
            ],
            [
                'name' => '山本 彩',
                'email' => 'aya.yamamoto@example.com',
                'profile' => [
                    'avatar_path' => null,
                    'display_name' => 'Aya',
                    'job_type' => 'マーケター',
                    'prefecture' => '福岡県',
                    'skills' => 'SNS運用, Google Analytics, 広告運用, Notion',
                    'bio' => 'SNS運用や広告改善を担当しています。分析作業や資料作成を一緒に進められる方を探しています。',
                    'purpose' => '黙々作業・情報交換',
                    'work_style' => 'カフェや自宅でリモート作業をしています。',
                ],
            ],
            [
                'name' => '中村 拓也',
                'email' => 'takuya.nakamura@example.com',
                'profile' => [
                    'avatar_path' => null,
                    'display_name' => '中村 拓也',
                    'job_type' => 'インフラエンジニア',
                    'prefecture' => '北海道',
                    'skills' => 'AWS, Docker, Linux, Terraform, GitHub Actions',
                    'bio' => 'AWSやDocker周りの構築をしています。作業中に軽く相談できるつながりがほしいです。',
                    'purpose' => '技術相談・黙々作業',
                    'work_style' => '午前中と夜に作業することが多いです。',
                ],
            ],
            [
                'name' => '小林 奈々',
                'email' => 'nana.kobayashi@example.com',
                'profile' => [
                    'avatar_path' => null,
                    'display_name' => '小林 奈々',
                    'job_type' => 'UIデザイナー',
                    'prefecture' => '兵庫県',
                    'skills' => 'Figma, UI設計, UXリサーチ, プロトタイピング',
                    'bio' => 'WebサービスのUI改善を中心に活動しています。作業会やレビュー会に参加したいです。',
                    'purpose' => 'デザイン相談・作業仲間探し',
                    'work_style' => '平日午後にまとまった作業時間を取っています。',
                ],
            ],
            [
                'name' => '加藤 直樹',
                'email' => 'naoki.kato@example.com',
                'profile' => [
                    'avatar_path' => null,
                    'display_name' => 'Naoki',
                    'job_type' => '個人開発者',
                    'prefecture' => '奈良県',
                    'skills' => 'Laravel, PHP, JavaScript, MySQL, Bootstrap',
                    'bio' => 'Laravelで個人開発をしています。継続して作業できる環境を作りたいと思っています。',
                    'purpose' => '個人開発・黙々作業',
                    'work_style' => '夜に2〜3時間ほど作業することが多いです。',
                ],
            ],
            [
                'name' => '吉田 真由',
                'email' => 'mayu.yoshida@example.com',
                'profile' => [
                    'avatar_path' => null,
                    'display_name' => 'まゆ',
                    'job_type' => '動画編集者',
                    'prefecture' => '宮城県',
                    'skills' => 'Premiere Pro, After Effects, Canva, YouTube運用',
                    'bio' => 'YouTube動画やショート動画の編集をしています。集中して作業できる相手を探しています。',
                    'purpose' => '黙々作業',
                    'work_style' => '午後から夜にかけて作業することが多いです。',
                ],
            ],
            [
                'name' => '松本 悠太',
                'email' => 'yuta.matsumoto@example.com',
                'profile' => [
                    'avatar_path' => null,
                    'display_name' => '松本 悠太',
                    'job_type' => 'Webディレクター',
                    'prefecture' => '埼玉県',
                    'skills' => '要件定義, 進行管理, Notion, Backlog, Figma',
                    'bio' => '制作進行や要件整理を担当しています。資料作成やタスク整理を一緒に進めたいです。',
                    'purpose' => '作業仲間探し・情報交換',
                    'work_style' => '平日の日中にリモートで作業しています。',
                ],
            ],
            [
                'name' => '井上 千尋',
                'email' => 'chihiro.inoue@example.com',
                'profile' => [
                    'avatar_path' => null,
                    'display_name' => 'Chihiro',
                    'job_type' => 'コーダー',
                    'prefecture' => '千葉県',
                    'skills' => 'HTML, CSS, JavaScript, WordPress, Sass',
                    'bio' => 'HTML/CSSコーディングやWordPress実装をしています。静かに作業できる相手がいると集中しやすいです。',
                    'purpose' => '黙々作業',
                    'work_style' => '午前中に集中してコーディングすることが多いです。',
                ],
            ],
            [
                'name' => '森田 遼',
                'email' => 'ryo.morita@example.com',
                'profile' => [
                    'avatar_path' => null,
                    'display_name' => '森田 遼',
                    'job_type' => 'アプリエンジニア',
                    'prefecture' => '静岡県',
                    'skills' => 'Flutter, Dart, Firebase, GitHub',
                    'bio' => 'Flutterでアプリ開発をしています。個人開発の進捗管理も兼ねて作業会に参加したいです。',
                    'purpose' => '個人開発・技術相談',
                    'work_style' => '休日と平日夜に作業しています。',
                ],
            ],
            [
                'name' => '清水 花',
                'email' => 'hana.shimizu@example.com',
                'profile' => [
                    'avatar_path' => null,
                    'display_name' => 'Hana',
                    'job_type' => 'イラストレーター',
                    'prefecture' => '長野県',
                    'skills' => 'Illustrator, Clip Studio Paint, Canva, SNS運用',
                    'bio' => 'SNSアイコンやWeb用イラストを制作しています。制作作業を一緒に進められる方を探しています。',
                    'purpose' => '黙々作業・制作仲間探し',
                    'work_style' => '午後から夕方に制作することが多いです。',
                ],
            ],
            [
                'name' => '橋本 亮',
                'email' => 'ryo.hashimoto@example.com',
                'profile' => [
                    'avatar_path' => null,
                    'display_name' => '橋本 亮',
                    'job_type' => 'システムエンジニア',
                    'prefecture' => '広島県',
                    'skills' => 'Java, PHP, SQL, Laravel, Git',
                    'bio' => '業務システムの開発に携わっています。休日にスキルアップの時間を作りたいです。',
                    'purpose' => '勉強・技術相談',
                    'work_style' => '土日や平日夜に学習しています。',
                ],
            ],
            [
                'name' => '岡田 梨沙',
                'email' => 'risa.okada@example.com',
                'profile' => [
                    'avatar_path' => null,
                    'display_name' => '梨沙',
                    'job_type' => 'カスタマーサクセス',
                    'prefecture' => '沖縄県',
                    'skills' => 'Notion, Google Workspace, 資料作成, 顧客対応',
                    'bio' => 'SaaS企業でカスタマーサクセスをしています。資料作成や業務改善の作業時間を確保したいです。',
                    'purpose' => '黙々作業・情報交換',
                    'work_style' => '平日夜に作業時間を取りたいです。',
                ],
            ],
            [
                'name' => '藤田 和也',
                'email' => 'kazuya.fujita@example.com',
                'profile' => [
                    'avatar_path' => null,
                    'display_name' => 'Kazuya',
                    'job_type' => 'データアナリスト',
                    'prefecture' => '東京都',
                    'skills' => 'SQL, Python, Looker Studio, BigQuery, Excel',
                    'bio' => 'データ集計やダッシュボード作成をしています。分析作業を集中して進めたいです。',
                    'purpose' => '黙々作業・情報交換',
                    'work_style' => '午前中に分析作業を進めることが多いです。',
                ],
            ],
            [
                'name' => '石川 杏奈',
                'email' => 'anna.ishikawa@example.com',
                'profile' => [
                    'avatar_path' => null,
                    'display_name' => '杏奈',
                    'job_type' => 'オンライン講師',
                    'prefecture' => '京都府',
                    'skills' => 'Laravel, HTML, CSS, JavaScript, 教材作成',
                    'bio' => 'オンラインでプログラミング講師をしています。教材作成や学習時間の確保に使いたいです。',
                    'purpose' => '勉強・黙々作業',
                    'work_style' => '日中に教材作成、夜に学習することが多いです。',
                ],
            ],
            [
                'name' => '長谷川 誠',
                'email' => 'makoto.hasegawa@example.com',
                'profile' => [
                    'avatar_path' => null,
                    'display_name' => '長谷川 誠',
                    'job_type' => 'プロジェクトマネージャー',
                    'prefecture' => '大阪府',
                    'skills' => 'PM, スクラム, Backlog, Jira, Notion',
                    'bio' => 'リモートチームの進行管理をしています。作業時間を区切って集中する目的で利用しています。',
                    'purpose' => '作業習慣化・情報交換',
                    'work_style' => '朝にタスク整理、夕方に振り返りをしています。',
                ],
            ],
            [
                'name' => '村上 莉子',
                'email' => 'riko.murakami@example.com',
                'profile' => [
                    'avatar_path' => null,
                    'display_name' => 'Riko',
                    'job_type' => 'フリーランス事務',
                    'prefecture' => '奈良県',
                    'skills' => 'Google Workspace, Excel, Notion, 経理補助, 事務作業',
                    'bio' => 'オンライン秘書や事務サポートをしています。請求書作成やタスク処理を一緒に進めたいです。',
                    'purpose' => '黙々作業',
                    'work_style' => '平日午前に事務作業をまとめて行っています。',
                ],
            ],
        ];

        foreach ($users as $userData) {
            $profileData = $userData['profile'];
            unset($userData['profile']);
        
            $user = User::updateOrCreate(
                ['email' => $userData['email']],
                [
                    'name' => $userData['name'],
                    'password' => Hash::make('Password123'),
                    'email_verified_at' => now(),
                ]
            );
        
            // prefecture（都道府県名）を prefecture_id に変換する
            $prefectureName = $profileData['prefecture'] ?? null;
            unset($profileData['prefecture']);
        
            $profileData['prefecture_id'] = $prefectureName
                ? Prefecture::where('name', $prefectureName)->value('id')
                : null;
        
            Profile::updateOrCreate(
                ['user_id' => $user->id],
                $profileData
            );
        }
    }
}
