<?php

/*
|--------------------------------------------------------------------------
| GEOCIMAT Routes
|--------------------------------------------------------------------------
|
| APP PARA EL DEPARTAMENTO DE GEOFISICA DE LA UNIVERSIDAD DE EL SALVADOR
| 
| 
|
*/


Route::group(['middleware' => 'cors'], function () {

    Route::prefix('repositorio')->group(function () {
        Route::get('/mostrar/{id}', 'RepositorioController@index');
        Route::post('/crear', 'RepositorioController@store');
        Route::delete('/destruir', 'RepositorioController@destroy');
        Route::post('/almacenar', 'RepositorioController@upload');
    });

    Route::prefix('proyecto')->group(function () {
        Route::get('/', 'ProyectoController@index');
        Route::post('/crear', 'ProyectoController@store');
    });

    Route::prefix('clasificacion')->group(function () {
        Route::get('/', 'ClasificacionController@index');
        Route::post('/crear', 'ClasificacionController@store');

    });

});

