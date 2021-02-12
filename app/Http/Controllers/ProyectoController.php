<?php

namespace App\Http\Controllers;

use App\Clasificacion;
use App\Proyecto;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ProyectoController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $proyecto = Proyecto::all();
        // dd($proyecto);
        return response()->json(['proyecto' => $proyecto]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $clasificaciones = Clasificacion::all();
        // dd($clasificaciones);
        return response()->json(['clasificaciones' => $clasificaciones]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        // dd($request->all());
        //REEMPLAZA ESPACION Y CARACTERES DE ESPACIO POR GUION 
        $titulo = preg_replace('/\s+/', ' ', $request->nombre);
        $titulo = str_replace(" ", "-", $titulo);

        // FUNCION PARA GENERAR CODIGO RANDOM
        function generateRandomString($length = 5)
        {
            return substr(str_shuffle("0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ"), 0, $length);
        }

        // CONCATENA CODIGO CON EL NOMBRE
        $identificador =  $titulo . "-" . generateRandomString();

        //VALIDACION DE DIRECTORIO Y NOMBRE DUPLICADO
        if (Proyecto::where('identificador', '=', $identificador)->exists() || !Storage::disk('public')->makeDirectory($identificador)) {
            return back()->withInput()->with(["error_existe" => "Hubo un error al crear proyecto intente enviar los datos nuevamente de nuevo."]);
        } else {
            $directorio_base = Storage::disk('public')->getDriver()->getAdapter()->getPathPrefix();
            if (PHP_OS === "WINNT") $directorio_base = str_replace("/", "\\", $directorio_base);
        }

        $proyecto = new Proyecto;
        $proyecto->identificador = $identificador;
        $proyecto->nombre = $titulo;
        $proyecto->id_clasificacion = $request->id_clasificacion;
        $proyecto->longitud = $request->longitud;
        $proyecto->latitud = $request->latitud;
        $proyecto->fecha_creado = date("Y-m-d");
        $proyecto->directorio_base = $directorio_base;
        $proyecto->descripcion = $request->descripcion;
        $proyecto->save();
        // dd($proyecto);

        return response()->json(['message' => 'Proyecto Almacenado']);
    }



    /**
     * Display the specified resource.
     *
     * @param  \App\Proyecto  $proyecto
     * @return \Illuminate\Http\Response
     */
    public function show(Proyecto $proyecto)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Proyecto  $proyecto
     * @return \Illuminate\Http\Response
     */
    public function edit(Proyecto $proyecto)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Proyecto  $proyecto
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Proyecto $proyecto)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Proyecto  $proyecto
     * @return \Illuminate\Http\Response
     */
    public function destroy(Proyecto $proyecto)
    {
        //
    }
}
