<?php

namespace App;

use Illuminate\Notifications\Notifiable;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Foundation\Auth\User as Authenticatable;

class User extends Authenticatable
{
    use Notifiable;

    public $timestamps = false;
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'nombre_usuario', 'nombre', 'apellidos', 'email', 'password','info','telefono,'
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'email', 'api_token'
    ];

    public function animales() {
        return $this->hasMany('App\Animal', 'dueno_id');
    }

    public function mensajesEnviados() {
        return $this->hasMany('App\Mensaje', 'autor_id');
    }

    public function mensajesRecibidos() {
        return $this->hasMany('App\Mensaje', 'destinatario_id');
    }

}
