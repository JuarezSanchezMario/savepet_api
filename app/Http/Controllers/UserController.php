<?php

namespace App\Http\Controllers;

use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class UserController extends Controller {
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    // GET /users
    public function index(Request $request) {
        $user = $request->user('api');
        if ($user) {
            return User::where([['id', '!=', $user->id],['nombre','like','%'.($request->input('filtro','')).'%']])->withCount('animales')->get();
        }
        else {
            return User::where('nombre','like','%'.($request->input('filtro','')).'%')->withCount('animales')->get();
        }

    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    // POST /users
    public function store(Request $request) {
        $request->validate([
            'nombre_usuario' => 'required|unique:users',
            'nombre' => 'required',
            'email' => 'required|email|unique:users',
            'password' => 'required|min:6',
            'imagen_perfil' => 'sometimes|nullable|image',
            'lat' => 'sometimes|nullable|numeric',
            'lng' => 'sometimes|nullable|numeric',
            'info' => 'sometimes|nullable',
            'telefono' => 'sometimes|nullable'
        ]);

        $user = new User();
        $user->fill($request->only([
            'nombre_usuario', 'nombre', 'apellidos', 'email', 'lat', 'lng', 'telefono','info'
        ]));

        $user->password = bcrypt($request->password);
        $user->api_token = Str::random(60);

        if ($request->hasFile('imagen_perfil')) {
            $path = $request->file('imagen_perfil')->store('usuarios', ['disk' => 's3']);
            $user->imagen_perfil = "https://s3.eu-west-3.amazonaws.com/savepet/".$path;
        }

        $user->save();

        return $user->makeVisible('api_token');
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\User $user
     * @return \Illuminate\Http\Response
     */
    // GET /user/{user} -> UserController@show
    public function show(User $user) {
        $user->load(['animales']);
        return $user;
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request $request
     * @param  \App\User $user
     * @return \Illuminate\Http\Response
     */
    // POST /user/{userid}
    public function update(Request $request, User $user) {
        $request->validate([
            'nombre' => 'sometimes',
            'apellidos' => 'sometimes',
            'email' => 'sometimes|unique:users|email',
            'password' => 'sometimes|min:6',
            'info' => 'sometimes|nullable',
            'imagen_perfil' => 'sometimes|nullable|image'
        ]);

        $user->fill($request->only(['nombre', 'apellidos', 'email', 'lat', 'lng', 'telefono','info']));

        if ($request->filled('password')) {
            $user->password = bcrypt($request->password);
        }


        if ($request->hasFile('imagen_perfil')) {

            if ($user->imagen_perfil) Storage::disk('s3')->delete('public/' . $user->imagen_perfil);

            $path = $request->file('imagen_perfil')->store('usuarios', ['disk' => 's3','visibility'=>'public']);

            $user->imagen_perfil = "https://s3.eu-west-3.amazonaws.com/savepet/".$path;
        }

        $user->save();

        return $user;
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\User $user
     * @return \Illuminate\Http\Response
     */
    // DELETE /user/{userid}
    public function destroy(User $user) {

        // Eliminar las imágenes de los animales del usuario.
        $animales = $user->animales;
        foreach ($animales as &$animal) {
            Storage::disk('s3')->deleteDirectory('animales/animal_' . $animal->id.'/');
        }

        if ($user->imagen_perfil)Storage::disk('s3')->delete('public/' . $user->imagen_perfil);
        $user->delete();
    }

    public function login(Request $request) {
        $request->validate([
            'nombre_usuario' => 'required_with:password',
            'password' => 'required_with:nombre_usuario',
            'api_token' => 'required_without:nombre_usuario,password'
        ]);

        $user = null;
        if ($request->filled('nombre_usuario')) {
            $user = User::where('nombre_usuario', $request->nombre_usuario)->first();
            if ($user && !Hash::check($request->password, $user->password)) {
                $user = null;
            }
        } else {
            $user = User::where('api_token', $request->api_token)->first();
        }

        if ($user) {
            return $user->makeVisible('api_token');
        }
        return response()->json(['message' => 'Credenciales inválidas.'], 400);

    }
}
