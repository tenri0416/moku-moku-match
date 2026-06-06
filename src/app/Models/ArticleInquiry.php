<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ArticleInquiry extends Model
{
    /**
     * 未対応
     */
    public const STATUS_OPEN = 1;

    /**
     * 返信済み
     */
    public const STATUS_REPLIED = 2;

    protected $fillable = [
        'email',
        'body',
        'admin_reply_body',
        'replied_at',
        'status',
    ];

    protected $casts = [
        'replied_at' => 'datetime',
    ];

    /**
     * 返信済みかどうか。
     */
    public function isReplied(): bool
    {
        return (int) $this->status === self::STATUS_REPLIED;
    }
}
