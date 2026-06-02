<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AdminTraining extends Model
{
    public const TYPE_DIARY = 'diary';
    public const TYPE_CHALLENGE = 'challenge';
    public const TYPE_SUMMARY = 'summary';
    public const TYPE_VERBALIZATION = 'verbalization';
    public const TYPE_ABSTRACTION = 'abstraction';
    public const TYPE_CONCRETIZATION = 'concretization';

    protected $fillable = [
        'admin_id',
        'type',
        'training_date',

        'question_title',
        'question_body',
        'answer_body',

        'diary_body',
        'challenged_thing',
        'completed_thing',
        'difficult_thing',
        'next_improvement',

        'total_score',
        'readability_score',
        'specificity_score',
        'structure_score',
        'expression_score',

        'good_point',
        'improvement_point',
        'next_task',

        'ai_response',
    ];

    protected $casts = [
        'training_date' => 'date',
        'ai_response' => 'array',
    ];

    /**
     * 管理者
     */
    public function admin(): BelongsTo
    {
        return $this->belongsTo(Admin::class);
    }

    /**
     * 日記トレーニングか判定する
     */
    public function isDiary(): bool
    {
        return $this->type === self::TYPE_DIARY;
    }

    /**
     * 今日のチャレンジか判定する
     */
    public function isChallenge(): bool
    {
        return $this->type === self::TYPE_CHALLENGE;
    }

    /**
     * AI出題型トレーニングか判定する
     */
    public function isAiQuestionTraining(): bool
    {
        return in_array($this->type, [
            self::TYPE_SUMMARY,
            self::TYPE_VERBALIZATION,
            self::TYPE_ABSTRACTION,
            self::TYPE_CONCRETIZATION,
        ], true);
    }

    /**
     * 種類名を日本語で取得する
     */
    public function typeLabel(): string
    {
        return match ($this->type) {
            self::TYPE_DIARY => '日記トレーニング',
            self::TYPE_CHALLENGE => '今日のチャレンジ',
            self::TYPE_SUMMARY => '要約力トレーニング',
            self::TYPE_VERBALIZATION => '言語化力トレーニング',
            self::TYPE_ABSTRACTION => '抽象化力トレーニング',
            self::TYPE_CONCRETIZATION => '具体化力トレーニング',
            default => 'トレーニング',
        };
    }

    /**
     * 採点項目の表示名を取得する
     */
    public function scoreLabels(): array
    {
        return match ($this->type) {
            self::TYPE_SUMMARY => [
                'readability_score' => '重要点の抽出',
                'specificity_score' => '簡潔さ',
                'structure_score' => '正確性',
                'expression_score' => 'わかりやすさ',
            ],
            self::TYPE_VERBALIZATION => [
                'readability_score' => '考えの明確さ',
                'specificity_score' => '理由の具体性',
                'structure_score' => '構成',
                'expression_score' => '伝わりやすさ',
            ],
            self::TYPE_ABSTRACTION => [
                'readability_score' => '共通点の発見',
                'specificity_score' => '本質の捉え方',
                'structure_score' => '理由の説明',
                'expression_score' => '言葉の簡潔さ',
            ],
            self::TYPE_CONCRETIZATION => [
                'readability_score' => '具体例のわかりやすさ',
                'specificity_score' => '行動への落とし込み',
                'structure_score' => '相手目線',
                'expression_score' => '実行しやすさ',
            ],
            default => [
                'readability_score' => '読みやすさ',
                'specificity_score' => '具体性',
                'structure_score' => '構成',
                'expression_score' => '表現力',
            ],
        };
    }

    /**
     * 回答済みか判定する
     */
    public function isAnswered(): bool
    {
        return filled($this->answer_body)
            || filled($this->diary_body)
            || filled($this->challenged_thing);
    }
}
