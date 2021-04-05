<?php

namespace App\Http\Controllers\Geocimat;

use App\Models\Geocimat\Administracion;
use App\Models\Geocimat\Proyecto;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class ProyectoController extends Controller
{

    protected $geocimat = 'geocimat/';
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $user_id = Auth::id();
        try {
            if ($this->pj_list($user_id)) {
                $proyectos = Proyecto::select("identificador", "nombre")
                    ->orderBy('fecha_creado', 'desc')
                    ->get();
            } else {
                $proyectos = Proyecto::where('user_id', $user_id)
                    ->select("identificador", "nombre")
                    ->orderBy('fecha_creado', 'desc')
                    ->get();
            }
            $permisos = Administracion::where('user_id', $user_id)
                ->select("pj_list", "admin_panel")
                ->get();

            return response()->json(['proyectos' => $proyectos, 'permisos' => $permisos]);
        } catch (\Exception $th) {
            return response()->json(['mensaje' => "Ocurrio un error " . $th->getMessage()], 500);
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
        $validated = (object) $request->validate([
            'nombre' => 'required',
            'id_clasificacion' => 'required|numeric',
            'id_unidad' => 'required|numeric',
            'fecha_creado' => 'required|date',
            'longitud' => 'required|numeric',
            'latitud' => 'required|numeric',
            'descripcion' => 'nullable',
        ], ['nombre.alpha_num' => 'Debe contener solo valores alfanumérico.']);

        $user_id = Auth::id();
        $nombre = $validated->nombre;

        if (strpbrk($nombre, "\\/?%*:|\"<>")) {
            return response()->json(['error' => 'El nombre del proyecto debe contener solo valores alfanuméricos.'], 422);
        }

        $identificador = $this->GenerarIdentificador($nombre);
        $directorio_base = $this->geocimat . $identificador;
        $nuevo_directorio = Storage::disk('public')->makeDirectory($directorio_base);

        if (!$nuevo_directorio) {
            return response()->json(['error' => 'No se pudo crear el directorio.'], 500);
        }

        $proyecto = new Proyecto;
        $proyecto->user_id = $user_id;
        $proyecto->identificador = $identificador;
        $proyecto->nombre = $nombre;
        $proyecto->id_clasificacion = $validated->id_clasificacion;
        // $proyecto->id_unidad = $validated->id_unidad;
        $proyecto->longitud = $validated->longitud;
        $proyecto->latitud = $validated->latitud;
        $proyecto->fecha_creado = $validated->fecha_creado;
        $proyecto->directorio_base = $directorio_base;
        $proyecto->descripcion = $validated->descripcion;
        $proyecto->save();
        return response()->json(['mensaje' => 'Proyecto Almacenado.'], 201);
    }

    /**
     * Verifica el permiso de listado completo.
     *
     * @param  Auth::id 
     * @return Boolean
     */
    public function pj_list($user_id)
    {
        $pj_list = true;
        return Administracion::where('user_id', $user_id)->where("pj_list", $pj_list)->value("pj_list");
    }

    private function GenerarIdentificador($nombreProyecto)
    {
        $titulo = preg_replace('/\s+/', ' ', $nombreProyecto);
        return str_replace(" ", "-", $titulo) . "-" . Str::random(5);
    }
}
