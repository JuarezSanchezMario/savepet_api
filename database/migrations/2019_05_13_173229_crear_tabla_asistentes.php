<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CrearTablaAsistentes extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('asistentes', function (Blueprint $table) {
            $table->bigInteger('participante_id')->unsigned();
            $table->bigInteger('evento_id')->unsigned();

            $table->foreign('participante_id')->references('id')->on('users')->onDelete('CASCADE');
            $table->foreign('evento_id')->references('id')->on('eventos')->onDelete('CASCADE');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('asistentes');
    }
}
