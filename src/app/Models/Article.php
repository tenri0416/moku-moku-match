<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
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
        'thumbnail_path',
        'status',
        'published_at',
    ];

    protected $casts = [
        'published_at' => 'datetime',
        'deleted_at' => 'datetime',
    ];

    public function admin(): BelongsTo
    {
        return $this->belongsTo(Admin::class);
    }

    public function prefecture(): BelongsTo
    {
        return $this->belongsTo(Prefecture::class);
    }

    public function scopePublic(Builder $query): Builder
    {
        return $query
            ->where('status', self::STATUS_PUBLIC)
            ->whereNotNull('published_at')
            ->where('published_at', '<=', now());
    }

    public function getSeoTitleAttribute(): ?string
    {
        return $this->attributes['seo_title']
            ?? $this->attributes['title']
            ?? null;
    }
    
    public function getSeoDescriptionTextAttribute(): ?string
    {
        $seoDescription = $this->attributes['seo_description'] ?? null;
        $excerpt = $this->attributes['excerpt'] ?? null;
        $bodyHtml = $this->attributes['body_html'] ?? '';
    
        return $seoDescription ?: ($excerpt ?: mb_substr(strip_tags($bodyHtml), 0, 120));
    }
}
