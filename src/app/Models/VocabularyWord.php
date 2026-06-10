<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class VocabularyWord extends Model
{
    use SoftDeletes;

    public const STATUS_NOT_REVIEWED = 'not_reviewed';
    public const STATUS_REVIEWING = 'reviewing';
    public const STATUS_UNDERSTOOD = 'understood';
    public const STATUS_WEAK = 'weak';
    public const STATUS_MASTERED = 'mastered';

    protected $fillable = [
        'user_id',
        'word',
        'meaning',
        'example_sentence',
        'memo',
        'source',
        'category',
        'importance',
        'review_status',
        'is_review_target',
        'review_count',
        'correct_count',
        'last_reviewed_at',
    ];

    protected $casts = [
        'importance' => 'integer',
        'is_review_target' => 'boolean',
        'review_count' => 'integer',
        'correct_count' => 'integer',
        'last_reviewed_at' => 'datetime',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function reviews(): HasMany
    {
        return $this->hasMany(VocabularyReview::class);
    }

    public function statusLabel(): string
    {
        return match ($this->review_status) {
            self::STATUS_MASTERED => '定着済み',
            self::STATUS_UNDERSTOOD => '理解できた',
            self::STATUS_REVIEWING => '復習中',
            self::STATUS_WEAK => '苦手',
            default => '未復習',
        };
    }

    public function correctRate(): int
    {
        if ($this->review_count <= 0) {
            return 0;
        }

        return (int) round(($this->correct_count / $this->review_count) * 100);
    }
}
