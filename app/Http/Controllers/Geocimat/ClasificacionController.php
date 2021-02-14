<?php

namespace App\Http\Controllers\Geocimat;

use App\Models\Geocimat\Clasificacion;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;


class ClasificacionController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $clasificaciones =  Clasificacion::all();
        return response()->json(['clasificaciones' => $clasificaciones]);
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
        
        //
        
        try {
            $clasificacion  = new Clasificacion;
            $clasificacion->nombre = $request->nombre;
            $clasificacion->material_color = $request->material_color;
            $clasificacion->visible = $request->visible;
            $clasificacion->save();
            return response()->json(["message"=>"Clasificacion almacenado con exito"]);
        } catch (\Exception $e) {
            return response()->json(["message" => "Ocurrio un error ". $e->getMessage()]);
        }
        
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Clasificacion  $clasificacion
     * @return \Illuminate\Http\Response
     */
    public function show(Clasificacion $clasificacion)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Clasificacion  $clasificacion
     * @return \Illuminate\Http\Response
     */
    public function edit(Clasificacion $clasificacion)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Clasificacion  $clasificacion
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Clasificacion $clasificacion)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Clasificacion  $clasificacion
     * @return \Illuminate\Http\Response
     */
    public function destroy(Clasificacion $clasificacion)
    {
        //
    }
}
