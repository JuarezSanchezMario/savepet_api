<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Animal extends Model
{
    public $table = 'animales';
    public $timestamps = false;
    public $fillable = ['nombre', 'tipo', 'raza', 'fecha_nacimiento', 'lat', 'lng', 'estado', 'dueno_id', 'descripcion_corta', 'descripcion_larga'];
    public $dates = ['fecha_nacimiento'];

    public function dueno() {
        return $this->belongsTo('App\User', 'dueno_id');
    }

    public function imagenes() {
        return $this->hasMany('App\Imagen', 'animal_id');
    }
}