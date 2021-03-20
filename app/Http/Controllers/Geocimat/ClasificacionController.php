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
        try {
            $clasificaciones =  Clasificacion::all();
            return response()->json(['clasificaciones' => $clasificaciones]);
        } catch (\Exception $th) {
            return response()->json(['message' => "Ocurrio un error " . $th->getMessage()],500);
        }
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        try {
            $clasificacion  = new Clasificacion;
            $clasificacion->nombre = $request->nombre;
            $clasificacion->material_color = $request->material_color;
            $clasificacion->visible = $request->visible;
            $clasificacion->save();

            return response()->json([ "message" => "Clasificacion almacenado con exito", "clasificacion" => $clasificacion ]);
        } catch (\Exception $e) {
            return response()->json(["message" => "Ocurrio un error " . $e->getMessage()],500);
        }
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Clasificacion  $clasificacion
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request)
    {
        try {
            if (Clasificacion::where("id", "=", $request->id)->update(["nombre" => $request->nombre, "material_color" => $request->material_color])) {
                return response()->json(["message" => "Clasificacion actualizada"]);
            } else {
                return response()->json(["message" => "No se encontro la clasificacion"],404);
            }
        } catch (\Exception $e) {
            //throw $th;
            return response()->json(["message" => "Ocurrio un error " . $e->getMessage()],500);
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Clasificacion  $clasificacion
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request)
    {
        try {
            if (Clasificacion::where("id", "=", $request->id)->update(["visible" => $request->visible])) {
                return response()->json(["message" => "Clasificacion actualizada"]);
            } else {
                return response()->json(["message" => "No se encontro la clasificacion"],404);
            }
        } catch (\Exception $e) {
            return response()->json(["message" => "Ocurrio un error " . $e->getMessage()],500);
        }
    }
}
