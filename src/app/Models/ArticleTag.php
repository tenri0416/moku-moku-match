<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class ArticleTag extends Model
{
    protected $fillable = [
        'name',
        'slug',
        'description',
        'sort_order',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    /**
     * タグに紐づく記事を取得する
     */
    public function articles(): BelongsToMany
    {
        return $this->belongsToMany(Article::class, 'article_article_tag')
            ->withTimestamps();
    }
}
