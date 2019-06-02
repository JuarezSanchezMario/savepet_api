<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateEventosTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('eventos', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->bigInteger('organizador_id')->unsigned();
            $table->string('nombre');
            $table->text('descripcion')->nullable();
            $table->timestamp('fecha');
            $table->string('imagen')->nullable();
            $table->integer('aforo')->unsigned();
            $table->double('lat');
            $table->double('lng');

            $table->foreign('organizador_id')->references('id')->on('users')->onDelete('CASCADE');

        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('eventos');
    }
}
