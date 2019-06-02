<?php

namespace App\Http\Controllers;

use App\Mensaje;
use Carbon\Carbon;
use Illuminate\Http\Request;

class MensajeController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $mensajes = null;
        if ($request->input('tipo', 'recibidos') == 'recibidos') {
            // Listar mensajes recibidos.
            $mensajes = $request->user()->mensajesRecibidos();
        }
        else {
            // Listar mensajes enviados.
            $mensajes = $request->user()->mensajesEnviados();
        }

        return $mensajes->orderBy('fecha', 'DESC')->get()->load('autor');
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
            'destinatario_id' => 'required|exists:users,id|not_in:' . $request->user()->id,
            'contenido' => 'required',
        ]);

        $mensaje = new Mensaje();
        $mensaje->contenido = $request->contenido;
        $mensaje->destinatario_id = $request->destinatario_id;
        $mensaje->autor_id = $request->user()->id;
        $mensaje->fecha = Carbon::now();
        $mensaje->save();

        return $mensaje;
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Mensaje  $mensaje
     * @return \Illuminate\Http\Response
     */
    public function show(Mensaje $mensaje)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Mensaje  $mensaje
     * @return \Illuminate\Http\Response
     */
    public function destroy(Mensaje $mensaje)
    {
        $mensaje->delete();
    }
}
