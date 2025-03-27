<?php

namespace App\Policies;

use App\Models\Lead;
use App\Models\User;

class LeadPolicy
{
    public function view(User $user, Lead $lead)
    {
        return $user->id === $lead->user_id || $user->isAdmin();
    }

    public function create(User $user)
    {
        return true;
    }

    public function update(User $user, Lead $lead)
    {
        return $user->id === $lead->user_id || $user->isAdmin();
    }

    public function delete(User $user, Lead $lead)
    {
        return $user->id === $lead->user_id || $user->isAdmin();
    }
} 