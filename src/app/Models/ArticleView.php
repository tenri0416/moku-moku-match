<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ArticleView extends Model
{
    protected $fillable = [
        'article_id',
        'user_id',
        'ip_address',
        'user_agent',
        'referer',
    ];

    /**
     * 閲覧された記事を取得する
     */
    public function article(): BelongsTo
    {
        return $this->belongsTo(Article::class);
    }

    /**
     * 閲覧したユーザーを取得する
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
