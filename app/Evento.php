<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Evento extends Model
{
    public $fillable = ['nombre', 'descripcion', 'fecha', 'aforo', 'lat', 'lng'];
    public $timestamps = false;
    public $dates = ['fecha'];

    function asistentes() {
        return $this->belongsToMany('App\User', 'asistentes', 'evento_id', 'asistente_id');
    }

    function organizador() {
        return $this->belongsTo('App\User', 'organizador_id', 'id');
    }
}
