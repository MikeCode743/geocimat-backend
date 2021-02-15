<?php

namespace App\Http\Controllers\Geocimat;

use App\Models\Geocimat\Calendario;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Geocimat\EstadoVisita;
use App\Models\Geocimat\Proyecto;
use Illuminate\Support\Facades\DB;

class CalendarioController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $calendario = DB::table('geo_calendario')
            ->join('geo_proyecto', 'geo_calendario.identificador', '=', 'geo_proyecto.identificador')
            ->join('geo_clasificacion', 'geo_clasificacion.id', '=', 'geo_proyecto.id_clasificacion')
            ->join('geo_estado_visita', 'geo_estado_visita.id', '=', 'geo_calendario.id_estado')
            ->select('geo_calendario.id', 'geo_calendario.descripcion', 'geo_calendario.fecha_inicio AS start', 'geo_calendario.fecha_fin AS end', 'geo_calendario.identificador AS name', 'geo_calendario.id_estado AS id_status', 'geo_proyecto.nombre as proyecto', 'geo_estado_visita.material_color AS materialColor', 'geo_clasificacion.nombre as clasificacion', 'geo_clasificacion.material_color as cla_material_color')
            ->get();

        $estadoVisita = EstadoVisita::select("id", "nombre", "material_color")->get();

        //VALIDAR LUEGO POR USUARIOS
        $proyectos = Proyecto::select("nombre", "identificador")->get();

        return response()->json(["calendario" => $calendario, "estadoVisita" => $estadoVisita, "proyectos" => $proyectos]);
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
        try {
            if (Proyecto::findOrFail($request->identificador) || EstadoVisita::findOrFail($request->id_estado)) {

                $calendario = new Calendario;
                $calendario->fecha_inicio = $request->fecha_inicio;
                $calendario->fecha_fin = $request->fecha_fin;
                $calendario->id_estado = $request->id_estado;
                $calendario->identificador = $request->identificador;
                $calendario->descripcion = $request->descripcion;
                $calendario->save();
                return response()->json(["message" => "Evento agregado al calendario","newDate"=>$calendario->id]);
            }
            return response()->json(["message" => "Este proyecto no se encuentra en nuestros registros", 406]);
        } catch (\Exception $th) {
            //throw $th;
            return response()->json(["message" => "Ocurrio un error " . $th->getMessage()]);
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Geocimat\Calendario  $calendario
     * @return \Illuminate\Http\Response
     */
    public function show(Calendario $calendario)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Geocimat\Calendario  $calendario
     * @return \Illuminate\Http\Response
     */
    public function edit(Calendario $calendario)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Geocimat\Calendario  $calendario
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request)
    {
        try {
            if (Calendario::Find($request->id) && EstadoVisita::Find($request->id_estado)) {
                Calendario::where("id", "=", $request->id)->update(["id_estado" => $request->id_estado, "descripcion" => $request->descripcion]);
                return response()->json(["message"=>"Evento Actualizado"]);
            }
            return response()->json(["message" => "El evento no se fue actualizado"],406);

        } catch (\Exception $th) {
            //throw $th;
            return response()->json(["message" => "Ocurrio un error ". $th->getMessage()]);

        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Geocimat\Calendario  $calendario
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request)
    {
        //
        try {
            if (Calendario::Find($request->id)) {
                Calendario::where("id", "=", $request->id)->delete();
                return response()->json(["message" => "Evento eliminado"]);
            }
            return response()->json(["message" => "El evento no se fue eliminado"], 406);
        } catch (\Exception $th) {
            //throw $th;
            return response()->json(["message" => "Ocurrio un error " . $th->getMessage()]);
        }
    }
}
