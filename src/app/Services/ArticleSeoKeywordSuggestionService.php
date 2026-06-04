<?php

namespace App\Services;

use App\Models\Article;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;

class ArticleSeoKeywordSuggestionService
{
    /**
     * 記事から検索確認用キーワード候補を5件作成する。
     *
     * 上に表示するほど、検索で確認しやすいキーワードになるように並べる。
     */
    public function make(Article $article): array
    {
        $article->loadMissing(['category', 'tags']);

        $title = $this->cleanText($article->seo_title ?: $article->h1_title ?: $article->title);
        $h1 = $this->cleanText($article->h1_title ?: $article->title);
        $description = $this->cleanText($article->seo_description ?: $article->excerpt ?: '');

        $brand = 'MokuMoku Match';

        $keywords = collect();

        /*
         * 1. SEOタイトル完全一致
         * 一番確認しやすいため最上位にする。
         */
        if ($title !== '') {
            $keywords->push([
                'label' => 'SEOタイトル完全一致',
                'keyword' => '"' . $title . '"',
                'note' => '記事がGoogleに登録されているか確認しやすい検索です。',
                'strength' => '高',
            ]);
        }

        /*
         * 2. ブランド名 + H1の主要語
         * サービス名を含めるため、比較的ヒットしやすい。
         */
        $mainPhrase = $this->extractMainPhrase($h1);

        if ($mainPhrase !== '') {
            $keywords->push([
                'label' => 'サービス名 + 記事テーマ',
                'keyword' => $brand . ' ' . $mainPhrase,
                'note' => 'サービス名と記事テーマを組み合わせた確認用キーワードです。',
                'strength' => '高',
            ]);
        }

        /*
         * 3. site検索 + 記事テーマ
         * Googleにインデックスされているか確認しやすい。
         */
        if ($mainPhrase !== '') {
            $keywords->push([
                'label' => 'サイト内インデックス確認',
                'keyword' => 'site:mokumokumatch.top "' . $mainPhrase . '"',
                'note' => 'MokuMoku Match内の記事としてGoogleに認識されているか確認できます。',
                'strength' => '高',
            ]);
        }

        /*
         * 4. 説明文から複合キーワードを作成
         */
        $descriptionPhrase = $this->extractDescriptionPhrase($description);

        if ($descriptionPhrase !== '') {
            $keywords->push([
                'label' => '本文テーマの複合検索',
                'keyword' => $descriptionPhrase,
                'note' => '一般ユーザーが検索しそうな言葉に近い候補です。',
                'strength' => '中',
            ]);
        }

        /*
         * 5. タグ・カテゴリーから補助キーワードを作成
         */
        $tagPhrase = $this->buildTagPhrase($article);

        if ($tagPhrase !== '') {
            $keywords->push([
                'label' => 'カテゴリー・タグ検索',
                'keyword' => $tagPhrase,
                'note' => 'カテゴリーやタグを元にした確認用キーワードです。',
                'strength' => '低',
            ]);
        }

        /*
         * 候補が5件未満の場合の補完。
         */
        $fallbacks = collect([
            [
                'label' => 'ブランド検索',
                'keyword' => $brand . 'とは',
                'note' => 'サービス名を含むため、比較的確認しやすい検索です。',
                'strength' => '高',
            ],
            [
                'label' => '課題検索',
                'keyword' => 'リモートワーク 孤独 作業仲間',
                'note' => 'サービスの課題軸に近い一般検索です。',
                'strength' => '中',
            ],
            [
                'label' => '利用シーン検索',
                'keyword' => '黙々作業 仲間 オンライン',
                'note' => 'MokuMoku Matchの利用シーンに近い検索です。',
                'strength' => '低',
            ],
        ]);

        $keywords = $keywords
            ->merge($fallbacks)
            ->unique('keyword')
            ->take(5)
            ->values();

        return $keywords->all();
    }

    /**
     * Google検索用URLを作成する。
     */
    public function googleSearchUrl(string $keyword): string
    {
        return 'https://www.google.com/search?q=' . urlencode($keyword);
    }

    private function cleanText(?string $text): string
    {
        $text = strip_tags((string) $text);
        $text = preg_replace('/\s+/u', ' ', $text);

        return trim($text ?? '');
    }

    /**
     * H1やSEOタイトルから主要語を作成する。
     */
    private function extractMainPhrase(string $text): string
    {
        if ($text === '') {
            return '';
        }

        $text = str_replace(['？', '?', '！', '!', '。'], ' ', $text);
        $text = preg_replace('/\s+/u', ' ', $text);
        $text = trim($text ?? '');

        /*
         * 長すぎると検索しづらいため、40文字程度にする。
         */
        return Str::limit($text, 40, '');
    }

    /**
     * SEOディスクリプションから検索されやすい複合語を作る。
     */
    private function extractDescriptionPhrase(string $description): string
    {
        if ($description === '') {
            return '';
        }

        $candidateWords = [
            'フルリモート',
            'リモートワーク',
            '在宅作業',
            '孤独',
            '作業仲間',
            '勉強仲間',
            '黙々作業',
            'メッセージ',
            'トレーニング',
            '自己成長',
            'ランキング',
            '募集検索',
            'プロフィール',
        ];

        $matched = collect($candidateWords)
            ->filter(fn (string $word) => Str::contains($description, $word))
            ->values();

        if ($matched->isEmpty()) {
            return '';
        }

        return $matched->take(4)->implode(' ');
    }

    /**
     * カテゴリー・タグから検索候補を作成する。
     */
    private function buildTagPhrase(Article $article): string
    {
        $words = collect();

        if ($article->category?->name) {
            $words->push($article->category->name);
        }

        if ($article->tags instanceof Collection) {
            $article->tags
                ->pluck('name')
                ->filter()
                ->take(3)
                ->each(fn (string $name) => $words->push($name));
        }

        return $words
            ->filter()
            ->unique()
            ->take(4)
            ->implode(' ');
    }
}
