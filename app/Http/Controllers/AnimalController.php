<?php

namespace App\Http\Controllers;

use App\Animal;
use App\Imagen;
use Faker\Provider\Image;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class AnimalController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $request->validate([
            'max_distancia' => 'sometimes|numeric',
            'lat' => 'required_with:max_distancia|numeric',
            'lng' => 'required_with:max_distancia|numeric',
        ]);

        $animales = Animal::query();

        if ($request->filled('dueno_id')) {
            $animales->where('dueno_id', '=', $request->dueno_id);
        }

        if ($request->filled('estado')) {
            $animales->where('estado', '=', $request->estado);
        }

        if ($request->filled('tipo')) {
            $animales->where('tipo', '=', $request->tipo);
        }

        if ($request->filled('max_distancia')) {
            $animales->where(DB::raw($this->calculoDistancia($request->lat, $request->lng)), '<', $request->max_distancia);
        }

        return $animales->get();
    }

    private function calculoDistancia($origenLat, $origenLng) {
        return "IFNULL((6371 * 2 * ATAN2(SQRT((SIN(RADIANS(" . $origenLat . " - lat) / 2)) * (SIN(RADIANS(" . $origenLat . " - lat) / 2))
+ (RADIANS(" . $origenLng . " - lng) / 2) * (RADIANS(" . $origenLng . " - lng) / 2) * COS(RADIANS(lat)) * COS(RADIANS(" . $origenLat . "))), SQRT(1 - (SIN(RADIANS(" . $origenLat . " - lat) / 2)) * (SIN(RADIANS(" . $origenLat . " - lat) / 2))
+ (RADIANS(" . $origenLng . " - lng) / 2) * (RADIANS(" . $origenLng . " - lng) / 2) * COS(RADIANS(lat)) * COS(RADIANS(" . $origenLat . "))))), 50000)";
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
            'tipo' => 'required',
            'raza' => 'sometimes|nullable',
            'fecha_nacimiento' => 'sometimes|nullable|date',
            'lat' => 'sometimes|nullable|numeric',
            'lng' => 'sometimes|nullable|numeric',
            'estado' => 'required|in:adopcion,tramite,adoptado',
            'imagen_perfil' => 'sometimes|nullable|image'
        ]);

        $animal = new Animal();
        $animal->fill($request->except(['imagen_perfil']));
        $animal->dueno_id = $request->user()->id;

        $animal->save();

        if ($request->hasFile('imagen_perfil')) {
            $path = $request->file('imagen_perfil')->store('animales/animal_' . $animal->id, ['disk' => 's3']);
            $animal->imagen_perfil = "https://s3.eu-west-3.amazonaws.com/savepet/".$path;
            $animal->save();
        }
        return $animal;
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Animal  $animal
     * @return \Illuminate\Http\Response
     */
    public function show(Animal $animal)
    {
        $animal->load(['imagenes','dueno']);
        return $animal;
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Animal  $animal
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Animal $animal)
    {
        $request->validate([
            'nombre' => 'sometimes',
            'tipo' => 'sometimes|required',
            'raza' => 'sometimes|nullable',
            'fecha_nacimiento' => 'sometimes|nullable|date',
            'lat' => 'sometimes|nullable|numeric',
            'lng' => 'sometimes|nullable|numeric',
            'estado' => 'sometimes|in:adopcion,tramite,adoptado',
            'imagen_perfil' => 'sometimes|nullable|image',
            'dueno_id' => 'sometimes|exists:users,id'
        ]);

        $animal->fill($request->except(['imagen_perfil']));
        $animal->save();

        if ($request->hasFile('imagen_perfil')) {
            if ($animal->imagen_perfil) Storage::delete('public/' . $animal->imagen_perfil);

            $path = $request->file('imagen_perfil')->store('animales/animal_' . $animal->id, ['disk' => 's3']);
            $animal->imagen_perfil = "https://s3.eu-west-3.amazonaws.com/savepet/".$path;
            $animal->save();
        }

        return $animal;
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Animal  $animal
     * @return \Illuminate\Http\Response
     */
    public function destroy(Animal $animal)
    {
        Storage::disk('s3')->deleteDirectory('animales/animal_' . $animal->id.'/');
        $animal->delete();
    }
}
