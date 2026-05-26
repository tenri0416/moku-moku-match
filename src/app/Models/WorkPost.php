<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class WorkPost extends Model
{
   use HasFactory;

    public const STATUS_OPEN = 1;
    public const STATUS_CLOSED = 2;
    public const STATUS_PRIVATE = 3;

    public const LOCATION_ONLINE = 'online';
    public const LOCATION_OFFLINE = 'offline';
    public const LOCATION_BOTH = 'both';

    public const TIME_ZONE_MORNING = 'morning';
    public const TIME_ZONE_DAYTIME = 'daytime';
    public const TIME_ZONE_NIGHT = 'night';

    protected $fillable = [
        'user_id',
        'title',
        'body',
        'purpose',
        'location_type',
        'meeting_tool',
        'prefecture',
        'start_at',
        'end_at',
        'time_zone',
        'max_participants',
        'status',
    ];

    protected $casts = [
        'start_at' => 'datetime',
        'end_at' => 'datetime',
    ];


    public function user()
{
    return $this->belongsTo(User::class);
}

public function applications()
{
    return $this->hasMany(Application::class);
}

public function messages()
{
    return $this->hasMany(Message::class);
}
public function reports()
    {
        return $this->hasMany(Report::class);
    }

    public function isOpen(): bool
    {
        return $this->status === self::STATUS_OPEN;
    }

    public function isOwner(User $user): bool
    {
        return $this->user_id === $user->id;
    }
    
}
