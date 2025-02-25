<?php

namespace App\Policies;

use App\Models\Equipement;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class EquipementPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return false;
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, Equipement $equipement): bool
    {
        return false;
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return false;
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, Equipement $equipement): bool
    {
        return false;
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Equipement $equipement): bool
    {
        return false;
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, Equipement $equipement): bool
    {
        return false;
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, Equipement $equipement): bool
    {
        return false;
    }

     /**
     * Détermine si l'utilisateur peut gérer l'équipement.
     */
    public function manage(User $user, Equipement $equipement): bool
    {
        return $user->role === 'responsable' && $user->id === $equipement->laboratoire->responsable_id;
    }
}
