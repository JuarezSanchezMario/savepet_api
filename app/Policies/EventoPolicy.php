<?php

namespace App\Policies;

use App\User;
use App\Evento;
use Illuminate\Auth\Access\HandlesAuthorization;

class EventoPolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can view the evento.
     *
     * @param  \App\User  $user
     * @param  \App\Evento  $evento
     * @return mixed
     */
    public function view(User $user, Evento $evento)
    {
        //
    }

    /**
     * Determine whether the user can create eventos.
     *
     * @param  \App\User  $user
     * @return mixed
     */
    public function create(User $user)
    {
        //
    }

    /**
     * Determine whether the user can update the evento.
     *
     * @param  \App\User  $user
     * @param  \App\Evento  $evento
     * @return mixed
     */
    public function update(User $user, Evento $evento)
    {
        return $evento->organizador_id == $user->id;
    }

    public function unirse(User $user, Evento $evento) {
        return $user->id != $evento->organizador_id &&
            !$evento->asistentes()->where('asistente_id', '=', $user->id)->exists();
    }

    public function abandonar(User $user, Evento $evento) {
        return $evento->asistentes()->where('asistente_id', '=', $user->id)->exists();
    }

    /**
     * Determine whether the user can delete the evento.
     *
     * @param  \App\User  $user
     * @param  \App\Evento  $evento
     * @return mixed
     */
    public function delete(User $user, Evento $evento)
    {
        return $evento->organizador_id == $user->id;
    }

    /**
     * Determine whether the user can restore the evento.
     *
     * @param  \App\User  $user
     * @param  \App\Evento  $evento
     * @return mixed
     */
    public function restore(User $user, Evento $evento)
    {
        //
    }

    /**
     * Determine whether the user can permanently delete the evento.
     *
     * @param  \App\User  $user
     * @param  \App\Evento  $evento
     * @return mixed
     */
    public function forceDelete(User $user, Evento $evento)
    {
        //
    }
}
