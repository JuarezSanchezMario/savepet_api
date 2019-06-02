<?php

use Illuminate\Http\Request;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::post('/user/login', 'UserController@login');
Route::get('/user', 'UserController@index');
Route::post('/user', 'UserController@store');
Route::post('/user/{user}', 'UserController@update')->middleware(['auth:api', 'can:update,user']);
Route::get('/user/{user}', 'UserController@show');
Route::delete('/user/{user}', 'UserController@destroy')->middleware(['auth:api', 'can:delete,user']);

Route::post('/animal', 'AnimalController@store')->middleware('auth:api');
Route::post('/animal/{animal}', 'AnimalController@update')->middleware(['auth:api', 'can:update,animal']);
Route::get('/animal/{animal}', 'AnimalController@show');
Route::get('/animal', 'AnimalController@index');
Route::delete('/animal/{animal}', 'AnimalController@destroy')->middleware(['auth:api', 'can:delete,animal']);

Route::post('/imagen', 'ImagenController@store')->middleware(['auth:api']);
Route::delete('/imagen/{imagen}', 'ImagenController@destroy')->middleware(['auth:api', 'can:delete,imagen']);

Route::get('/evento', 'EventoController@index')->middleware(['auth:api']);
Route::get('/evento/{evento}', 'EventoController@show')->middleware(['auth:api']);
Route::post('/evento', 'EventoController@store')->middleware(['auth:api']);
Route::post('/evento/{evento}', 'EventoController@update')->middleware(['auth:api', 'can:update,evento']);
Route::post('/evento/{evento}/unirse', 'EventoController@unirse')->middleware(['auth:api', 'can:unirse,evento']);
Route::post('/evento/{evento}/abandonar', 'EventoController@abandonar')->middleware(['auth:api', 'can:abandonar,evento']);
Route::delete('/evento/{evento}', 'EventoController@destroy')->middleware(['auth:api', 'can:delete,evento']);

Route::get('/mensaje', 'MensajeController@index')->middleware(['auth:api']);
Route::post('/mensaje', 'MensajeController@store')->middleware(['auth:api']);
Route::delete('/mensaje/{mensaje}', 'MensajeController@destroy')->middleware(['auth:api']);