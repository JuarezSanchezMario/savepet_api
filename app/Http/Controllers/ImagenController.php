<?php

namespace App\Http\Controllers;

use App\Animal;
use App\Imagen;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ImagenController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
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
            'imagen' => 'required|image',
            'animal_id' => 'required|exists:animales,id'
        ]);

        $this->authorize('create', [Imagen::class, Animal::find($request->animal_id)]);

        $path = $request->file('imagen')->store('animales/animal_' . $request->animal_id, ['disk' => 's3']);

        $imagen = new Imagen();
        $imagen->animal_id = $request->animal_id;
        $imagen->path = "https://s3.eu-west-3.amazonaws.com/savepet/".$path;
        $imagen->save();

        return $imagen;
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Imagen  $imagen
     * @return \Illuminate\Http\Response
     */
    public function show(Imagen $imagen)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Imagen  $imagen
     * @return \Illuminate\Http\Response
     */
    public function edit(Imagen $imagen)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Imagen  $imagen
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Imagen $imagen)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Imagen  $imagen
     * @return \Illuminate\Http\Response
     */
    public function destroy(Imagen $imagen)
    {
        Storage::disk('s3')->delete(str_replace("https://s3.eu-west-3.amazonaws.com/savepet/","",$imagen->path));
        $imagen->delete();
    }
}
