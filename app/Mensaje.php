<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Mensaje extends Model
{
    public $timestamps = false;
    public $dates = ['fecha'];

    public function autor() {
        return $this->belongsTo('App\User');
    }

    public function destinatario() {
        return $this->belongsTo('App\User');
    }
}
