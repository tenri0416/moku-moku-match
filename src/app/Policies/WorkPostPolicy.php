<?php

namespace App\Policies;

use App\Models\User;
use App\Models\WorkPost;
use Illuminate\Auth\Access\Response;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class WorkPostPolicy
{
    public function update(User $user, WorkPost $workPost): bool
    {
        return $workPost->user_id === $user->id;
    }

    public function close(User $user, WorkPost $workPost): bool
    {
        return $workPost->user_id === $user->id;
    }

    public function manage(User $user): bool
    {
        return $user->isAdmin();
    }
    public function prefecture(): BelongsTo
    {
        return $this->belongsTo(Prefecture::class);
    }
}
