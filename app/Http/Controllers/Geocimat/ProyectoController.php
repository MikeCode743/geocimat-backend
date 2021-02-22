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
        $user_id = Auth::id() ?? 1;
        try {
            $proyectos = Proyecto::where('user_id', $user_id)
                ->select("identificador", "nombre")
                ->orderBy('fecha_creado', 'desc')
                ->get();

            $permisos = Administracion::where('user_id', $user_id)
                ->select("pj_list", "admin_panel")
                ->get();

            return response()->json([
                'proyectos' => $proyectos,
                'permisos' => $permisos
            ]);
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
        $user_id = Auth::id() ?? 1;
        $titulo = preg_replace('/\s+/', ' ', $request->nombre);
        $titulo = str_replace(" ", "-", $titulo);
        $identificador =  $titulo . "-" . Str::random(5);
        $nuevo_directorio = Storage::disk('public')->makeDirectory($this->geocimat . $identificador);

        if (!$nuevo_directorio) {
            return response()->json(['mensaje' => 'Error al crear el directorio.'], 500);
        }

        $proyecto = new Proyecto;
        $proyecto->identificador = $identificador;
        $proyecto->nombre = $titulo;
        $proyecto->id_clasificacion = $request->id_clasificacion;
        $proyecto->longitud = $request->longitud;
        $proyecto->latitud = $request->latitud;
        $proyecto->fecha_creado = date("Y-m-d");
        $proyecto->directorio_base = $this->geocimat . $identificador;
        $proyecto->descripcion = $request->descripcion;
        $proyecto->user_id =  $user_id;
        $proyecto->save();

        return response()->json(['mensaje' => 'Proyecto Almacenado.']);
    }
}


        // if (Proyecto::where('identificador', '=', $identificador)->exists() || !Storage::disk('public')->makeDirectory("geociomat/" . $identificador)) {
        //     return response()->json(['message' => "Hubo un error al crear proyecto intente enviar los datos nuevamente de nuevo."]);
        // } else {
        //     $directorio_base = storage_path("app/public/geocimat/");
        //     if (PHP_OS === "WINNT") $directorio_base = str_replace("/", "\\", $directorio_base);
        // }
