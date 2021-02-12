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

Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});

Route::prefix('repositorio')->group(function () {
    Route::get('/mostrar/{id}', 'RepositorioController@index');
    Route::post('/crear', 'RepositorioController@store');
    Route::delete('/destruir', 'RepositorioController@destroy');
    Route::post('/almacenar', 'RepositorioController@upload');
});


Route::prefix('proyecto')->group(function () {
    Route::get('/index', 'ProyectoController@index');
    Route::get('/formulario', 'ProyectoController@create');
    Route::post('/crear', 'ProyectoController@store');
});
