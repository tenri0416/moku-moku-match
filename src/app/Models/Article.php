<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;


class Article extends Model
{
    use SoftDeletes;

    public const STATUS_DRAFT = 1;
    public const STATUS_PUBLIC = 2;
    public const STATUS_PRIVATE = 3;

    protected $fillable = [
        'admin_id',
        'prefecture_id',
        'title',
        'slug',
        'short_slug',
        'seo_title',
        'seo_description',
        'h1_title',
        'excerpt',
        'body_html',
        'body_css',
        'thumbnail_path',
        'status',
        'published_at',
        'article_category_id',
        'reading_minutes',
        'author_user_id',
        'point_text',
        'toc_text',
        'view_count',
    ];

    protected $casts = [
        'published_at' => 'datetime',
        'deleted_at' => 'datetime',
    ];

    /**
     * 記事を作成した管理者。
     */
    public function admin(): BelongsTo
    {
        return $this->belongsTo(Admin::class);
    }

    /**
     * 記事に紐づく都道府県。
     */
    public function prefecture(): BelongsTo
    {
        return $this->belongsTo(Prefecture::class);
    }

    /**
     * 記事カテゴリーを取得する。
     */
    public function category(): BelongsTo
    {
        return $this->belongsTo(ArticleCategory::class, 'article_category_id');
    }

    /**
     * 記事タグを取得する。
     */
    public function tags(): BelongsToMany
    {
        return $this->belongsToMany(ArticleTag::class, 'article_article_tag')
            ->withTimestamps();
    }

    /**
     * 記事閲覧ログを取得する。
     */
    public function views(): HasMany
    {
        return $this->hasMany(ArticleView::class);
    }

    /**
     * 記事の著者として表示するユーザー。
     */
    public function authorUser(): BelongsTo
    {
        return $this->belongsTo(User::class, 'author_user_id');
    }

    /**
     * 公開中の記事だけに絞り込む。
     */
    public function scopePublic(Builder $query): Builder
    {
        return $query
            ->where('status', self::STATUS_PUBLIC)
            ->whereNotNull('published_at')
            ->where('published_at', '<=', now());
    }

    /**
     * SEOタイトル。
     */
    public function getSeoTitleAttribute(): ?string
    {
        return $this->attributes['seo_title']
            ?? $this->attributes['title']
            ?? null;
    }

    /**
     * SEO説明文。
     */
    public function getSeoDescriptionTextAttribute(): ?string
    {
        $seoDescription = $this->attributes['seo_description'] ?? null;
        $excerpt = $this->attributes['excerpt'] ?? null;
        $bodyHtml = $this->attributes['body_html'] ?? '';

        return $seoDescription ?: ($excerpt ?: mb_substr(strip_tags($bodyHtml), 0, 120));
    }

    /**
     * 記事いいねを取得する。
     */
    public function likes(): HasMany
    {
        return $this->hasMany(ArticleLike::class);
    }

    /**
     * この記事のポイントを1行ごとの配列で取得する。
     */
    public function getPointItemsAttribute(): array
    {
        $text = trim((string) ($this->point_text ?? ''));

        if ($text === '') {
            return [
                '読みやすく内容を整理',
                '今日から使えるヒントを紹介',
                '働き方や学びを少し整える',
            ];
        }

        return collect(preg_split('/\r\n|\r|\n/', $text))
            ->map(fn($line) => trim($line))
            ->filter()
            ->values()
            ->all();
    }

    /**
     * 目次を1行ごとの配列で取得する。
     */
    public function getTocItemsAttribute(): array
    {
        $text = trim((string) ($this->toc_text ?? ''));

        if ($text === '') {
            return [
                'はじめに',
                '本文',
                'まとめ',
            ];
        }

        return collect(preg_split('/\r\n|\r|\n/', $text))
            ->map(fn($line) => trim($line))
            ->filter()
            ->values()
            ->all();
    }
}
