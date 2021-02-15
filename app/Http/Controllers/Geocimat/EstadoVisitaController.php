<?php

namespace App\Http\Controllers\Geocimat;

use App\Models\Geocimat\EstadoVisita;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;


class EstadoVisitaController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
        $estadoVisita = EstadoVisita::all();
        return response()->json(["estadovisita"=>$estadoVisita]);
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
            $estadoVisita  = new EstadoVisita;
            $estadoVisita->nombre = $request->nombre;
            $estadoVisita->material_color = $request->material_color;
            $estadoVisita->visible = $request->visible;
            $estadoVisita->save();

            return response()->json(["message" => "Estado de visita almacenado con exito", "estadovisita" => $estadoVisita]);

        } catch (\Exception $th) {
            return response()->json(["message" => "Ocurrio un error " . $th->getMessage()]);
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Geocimat\EstadoVisita  $estadoVisita
     * @return \Illuminate\Http\Response
     */
    public function show(EstadoVisita $estadoVisita)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Geocimat\EstadoVisita  $estadoVisita
     * @return \Illuminate\Http\Response
     */
    public function edit(EstadoVisita $estadoVisita)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Geocimat\EstadoVisita  $estadoVisita
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request)
    {
        //
        try {
            if (EstadoVisita::where("id", "=", $request->id)->update(["nombre" => $request->nombre, "material_color" => $request->material_color])) {
                return response()->json(["message" => "Estado de Visita actualizada"]);
            } else {
                return response()->json(["message" => "No se encontro la estado de visita"]);
            }
        } catch (\Exception $e) {
            //throw $th;
            return response()->json(["message" => "Ocurrio un error " . $e->getMessage()]);
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Geocimat\EstadoVisita  $estadoVisita
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request)
    {
        //
        try {
            if (EstadoVisita::where("id", "=", $request->id)->update(["visible" => $request->visible])) {
                return response()->json(["message" => "Estado actualizada"]);
            } else {
                return response()->json(["message" => "No se encontro la estado"]);
            }
        } catch (\Exception $e) {
            //throw $th;
            return response()->json(["message" => "Ocurrio un error " . $e->getMessage()]);
        }
    }
}
