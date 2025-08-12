<?php

namespace App\Policies;

use App\Models\User;
use App\Models\Ticket;
use App\Enums\User\UserRole;
use Illuminate\Auth\Access\Response;

class TicketPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return $user->role == UserRole::ADMIN;
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, Ticket $ticket): bool
    {
        return $user->role == UserRole::ADMIN || $user->id === $ticket->user_id;
    }


    public function create(User $user): bool
    {
        return true;
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, Ticket $ticket): bool
    {
        return $user->role == UserRole::ADMIN || $user->id === $ticket->user_id;
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Ticket $ticket): bool
    {
        return $user->role == UserRole::ADMIN || $user->id === $ticket->user_id;
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, Ticket $ticket): bool
    {
        return $user->role == UserRole::ADMIN;
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, Ticket $ticket): bool
    {
        return $user->role == UserRole::ADMIN;
    }



    public function updateStatus(User $user, Ticket $ticket): bool
    {

        return $user->role === UserRole::ADMIN;
    }


    public function reply(User $user, Ticket $ticket): bool
    {
        return $user->role === UserRole::ADMIN || $user->id === $ticket->user_id;
    }

    public function canDownloadAttachment(User $user, Ticket $ticket): bool
    {
        return $user->role === UserRole::ADMIN || $user->id === $ticket->user_id;
    }
}
