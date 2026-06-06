<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ArticleLike extends Model
{
    protected $fillable = [
        'article_id',
        'user_id',
        'browser_key',
        'ip_address',
        'user_agent',
    ];

    /**
     * いいね対象の記事。
     */
    public function article(): BelongsTo
    {
        return $this->belongsTo(Article::class);
    }

    /**
     * いいねしたログインユーザー。
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
