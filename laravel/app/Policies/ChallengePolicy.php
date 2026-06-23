<?php

namespace App\Policies;

use App\Models\Challenge;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class ChallengePolicy
{
    public function viewAny(User $user): bool
    {
        return false;
    }

    public function view(User $user, Challenge $challenge): bool
    {
        return false;
    }

    public function create(User $user): bool
    {
        return true;
    }

    public function update(User $user, Challenge $challenge): bool
    {
        return $user->id === $challenge->owner_id;
    }

    public function delete(User $user, Challenge $challenge): bool
    {
        return $user->id === $challenge->owner_id;
    }

    public function restore(User $user, Challenge $challenge): bool
    {
        return false;
    }

    public function forceDelete(User $user, Challenge $challenge): bool
    {
        return false;
    }
}
