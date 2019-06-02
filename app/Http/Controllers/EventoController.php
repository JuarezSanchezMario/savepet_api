<?php

namespace App\Http\Controllers;

use App\Evento;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class EventoController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $soloEventosPasados = $request->input('pasado', 0) == 1;

        $eventos = Evento::query()->withCount('asistentes');

        if ($request->filled('organizador_id')) {
            $eventos->where('organizador_id', '=', $request->organizador_id);
        }

        if ($soloEventosPasados) {
            $eventos->where('fecha', '<', Carbon::now());
            $eventos->orderBy('fecha', 'DESC');
        }
        else {
            $eventos->where('fecha', '>', Carbon::now());
            $eventos->orderBy('fecha', 'ASC');
        }

        return $eventos->get();
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $request->validate([
            'nombre' => 'required',
            'fecha' => 'required|date|after:' . Carbon::now(),
            'imagen' => 'required|image',
            'aforo' => 'required|integer|gt:0',
            'lat' => 'required|numeric',
            'lng' => 'required|numeric'
        ]);

        $evento = new Evento();
        $evento->fill($request->except(['imagen', 'organizador_id']));
       // $evento->fecha = Carbon::now()->addDay(2);
        $evento->organizador_id = $request->user()->id;
        $evento->save();

        if ($request->hasFile('imagen')) {
            $path = $request->file('imagen')->store('eventos/evento_'.$evento->id, ['disk' => 's3']);
            $evento->imagen = "https://s3.eu-west-3.amazonaws.com/savepet/".$path;
            $evento->save();
        }


        return $evento;
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Evento  $evento
     * @return \Illuminate\Http\Response
     */
    public function show(Evento $evento)
    {
        $evento->load('asistentes', 'organizador');
        return $evento;
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Evento  $evento
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Evento $evento)
    {
        $request->validate([
            'nombre' => 'sometimes',
            'fecha' => 'sometimes|date|after:' . Carbon::now(),
            'imagen' => 'sometimes|image',
            'aforo' => 'sometimes|integer|gt:0',
            'lat' => 'sometimes|numeric',
            'lng' => 'sometimes|numeric'
        ]);

        $evento->fill($request->except(['imagen', 'organizador_id']));

        if ($request->hasFile('imagen')) {

            Storage::disk('s3')->delete(str_replace("https://s3.eu-west-3.amazonaws.com/savepet/","",$evento->imagen));

            $path = $request->file('imagen')->store('eventos', ['disk' => 's3']);
            $evento->imagen = "https://s3.eu-west-3.amazonaws.com/savepet/".$path;
        }

        $evento->save();

        return $evento;
    }

    public function unirse(Request $request, Evento $evento) {
        $evento->asistentes()->attach($request->user()->id);
    }

    public function abandonar(Request $request, Evento $evento) {
        $evento->asistentes()->detach($request->user()->id);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Evento  $evento
     * @return \Illuminate\Http\Response
     */
    public function destroy(Evento $evento)
    {
        Storage::disk('s3')->delete($evento->imagen);
        $evento->delete();
    }
}
