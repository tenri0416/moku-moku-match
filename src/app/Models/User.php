<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use App\Notifications\VerifyEmailNotification;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable(['name', 'email', 'password'])]
#[Hidden(['password', 'remember_token'])]
class User extends Authenticatable implements MustVerifyEmail
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable;

    public const ROLE_USER = 1;
    public const ROLE_ADMIN = 2;

    public const STATUS_ACTIVE = 1;
    public const STATUS_SUSPENDED = 2;
    public const STATUS_WITHDRAWN = 3;

    public const WITHDRAWAL_TYPE_SELF = 'self';
    public const WITHDRAWAL_TYPE_ADMIN = 'admin';

    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
        'status',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'withdrawn_at' => 'datetime',
            'suspended_at' => 'datetime',
        ];
    }

    public function profile()
    {
        return $this->hasOne(Profile::class);
    }

    public function workPosts()
    {
        return $this->hasMany(WorkPost::class);
    }

    public function applications()
    {
        return $this->hasMany(Application::class);
    }

    public function sentMessages()
    {
        return $this->hasMany(Message::class, 'sender_id');
    }
    public function receivedMessages()
    {
        return $this->hasMany(Message::class, 'receiver_id');
    }

    public function blocks()
    {
        return $this->hasMany(Block::class, 'blocker_id');
    }

    public function blockedBy()
    {
        return $this->hasMany(Block::class, 'blocked_user_id');
    }

    /**
     * 自分が対象ユーザーをブロックしているか
     */
    public function hasBlocked(User $user): bool
    {
        return Block::where('blocker_id', $this->id)
            ->where('blocked_user_id', $user->id)
            ->exists();
    }

    /**
     * 対象ユーザーから自分がブロックされているか
     */
    public function isBlockedBy(User $user): bool
    {
        return Block::where('blocker_id', $user->id)
            ->where('blocked_user_id', $this->id)
            ->exists();
    }

    /**
     * どちらかがブロックしているか
     */
    public function hasBlockRelationWith(User $user): bool
    {
        return $this->hasBlocked($user) || $this->isBlockedBy($user);
    }

    public function isAdmin(): bool
    {
        return $this->role === self::ROLE_ADMIN;
    }

    /**
     * メール認証通知を送信する
     */
    public function sendEmailVerificationNotification(): void
    {
        $this->notify(new VerifyEmailNotification());
    }

    /**
     * 満足度調査アンケート一覧を取得する。
     */
    public function satisfactionSurveys(): HasMany
    {
        return $this->hasMany(\App\Models\UserSatisfactionSurvey::class);
    }

    /**
     * 最新の満足度調査アンケートを取得する。
     */
    public function latestSatisfactionSurvey(): HasOne
    {
        return $this->hasOne(\App\Models\UserSatisfactionSurvey::class)->latestOfMany();
    }

    public function withdrawnByAdmin(): BelongsTo
    {
        return $this->belongsTo(Admin::class, 'withdrawn_by_admin_id');
    }

    public function suspendedByAdmin(): BelongsTo
    {
        return $this->belongsTo(Admin::class, 'suspended_by_admin_id');
    }

    public function isActive(): bool
    {
        return (int) $this->status === self::STATUS_ACTIVE;
    }

    public function isSuspended(): bool
    {
        return (int) $this->status === self::STATUS_SUSPENDED;
    }

    public function isWithdrawn(): bool
    {
        return (int) $this->status === self::STATUS_WITHDRAWN
            || $this->withdrawn_at !== null;
    }

    public function isSelfWithdrawn(): bool
    {
        return $this->withdrawal_type === self::WITHDRAWAL_TYPE_SELF;
    }

    public function isAdminWithdrawn(): bool
    {
        return $this->withdrawal_type === self::WITHDRAWAL_TYPE_ADMIN;
    }
}
