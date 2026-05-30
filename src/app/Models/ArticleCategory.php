<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ArticleCategory extends Model
{
    protected $fillable = [
        'parent_id',
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
     * 親カテゴリーを取得する
     */
    public function parent(): BelongsTo
    {
        return $this->belongsTo(self::class, 'parent_id');
    }

    /**
     * 子カテゴリーを取得する
     */
    public function children(): HasMany
    {
        return $this->hasMany(self::class, 'parent_id')
            ->orderBy('sort_order')
            ->orderBy('id');
    }

    /**
     * 記事を取得する
     */
    public function articles(): HasMany
    {
        return $this->hasMany(Article::class);
    }

    /**
     * カテゴリー階層の深さを取得する
     *
     * 親なし: 1
     * 親あり: 2
     * 親の親あり: 3
     */
    public function depth(): int
    {
        $depth = 1;
        $parent = $this->parent;

        while ($parent) {
            $depth++;
            $parent = $parent->parent;
        }

        return $depth;
    }

    /**
     * 最大階層を超えていないか判定する
     */
    public function canHaveChild(): bool
    {
        return $this->depth() < 3;
    }

    /**
     * 管理画面表示用の階層名を取得する
     */
    public function displayName(): string
    {
        $names = collect([$this->name]);
        $parent = $this->parent;

        while ($parent) {
            $names->prepend($parent->name);
            $parent = $parent->parent;
        }

        return $names->implode(' > ');
    }
}
