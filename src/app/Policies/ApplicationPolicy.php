<?php

namespace App\Policies;

use App\Models\Application;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class ApplicationPolicy
{
    public function approve(User $user, Application $application): bool
    {
        return $application->workPost->user_id === $user->id;
    }

    public function reject(User $user, Application $application): bool
    {
        return $application->workPost->user_id === $user->id;
    }
}
