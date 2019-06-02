<?php

namespace App\Policies;

use App\Animal;
use App\User;
use App\Imagen;
use Illuminate\Auth\Access\HandlesAuthorization;

class ImagenPolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can view the imagen.
     *
     * @param  \App\User  $user
     * @param  \App\Imagen  $imagen
     * @return mixed
     */
    public function view(User $user, Imagen $imagen)
    {
        //
    }

    /**
     * Determine whether the user can create imagens.
     *
     * @param  \App\User  $user
     * @return mixed
     */
    public function create(User $user, Animal $animal)
    {
        return $user->id == $animal->dueno_id;
    }

    /**
     * Determine whether the user can update the imagen.
     *
     * @param  \App\User  $user
     * @param  \App\Imagen  $imagen
     * @return mixed
     */
    public function update(User $user, Imagen $imagen)
    {
        //
    }

    /**
     * Determine whether the user can delete the imagen.
     *
     * @param  \App\User  $user
     * @param  \App\Imagen  $imagen
     * @return mixed
     */
    public function delete(User $user, Imagen $imagen)
    {
        //
    }

    /**
     * Determine whether the user can restore the imagen.
     *
     * @param  \App\User  $user
     * @param  \App\Imagen  $imagen
     * @return mixed
     */
    public function restore(User $user, Imagen $imagen)
    {
        //
    }

    /**
     * Determine whether the user can permanently delete the imagen.
     *
     * @param  \App\User  $user
     * @param  \App\Imagen  $imagen
     * @return mixed
     */
    public function forceDelete(User $user, Imagen $imagen)
    {
        //
    }
}
