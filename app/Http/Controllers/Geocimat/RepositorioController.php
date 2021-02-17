<?php

namespace App\Http\Controllers\Geocimat;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\File;
use App\Http\Controllers\Controller;


class RepositorioController extends Controller
{
    protected $geocimat = 'geocimat/';

    function getFileList($path)
    {
        $listadoArchivos = collect();
        $archivos = Storage::disk('public')->files($path);
        foreach ($archivos as $archivo) {
            $nombreArchivo = array_reverse(explode('/', $archivo))[0];
            $listadoArchivos->push([
                'name' => $nombreArchivo,
                'id' => Str::random(30),
                'ruta' => $archivo,
                'mime' => Storage::disk('public')->mimeType($archivo),
            ]);
        }
        return $listadoArchivos;
    }

    function getSubDirectory($path)
    {
        $listadoCarpeta = collect();
        $directorios = Storage::disk('public')->directories($path);
        foreach ($directorios as $directorio) {
            $children = $this->getFileList($directorio);
            $carpeta = array_reverse(explode('/', $directorio))[0];
            $listadoCarpeta->push([
                'name' => $carpeta,
                'id' => Str::random(30),
                'children' => $children,
                'ruta' => $directorio,
                'mime' => 'folder',
            ]);
        }
        return $listadoCarpeta;
    }

    function getDirectory($path)
    {
        $proyecto = collect($this->getSubDirectory($path));
        $proyecto = $proyecto->merge($this->getFileList($path));
        return  $proyecto;
    }


    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index($id)
    {
        if (File::exists(Storage::disk('public')->path('/') . $this->geocimat . $id)) {
            return response()->json(['directorio' => $this->getDirectory($this->geocimat . $id), 'request' => $id]);
        }
        return response()->json(['mensaje' => 'El directorio no existe.', 'directorio' => []], 404);
    }


    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'nodoPadre' => 'required',
            'folder' => 'required',
        ]);

        if (!Storage::disk('public')->exists($this->geocimat . $validated['nodoPadre'])) {
            return response()->json(['mensaje' => 'El directorio no existe.'], 404);
        }

        $directorio = Storage::disk('public')->makeDirectory($this->geocimat . $validated['nodoPadre'] . '/' . $validated['folder']);
        if ($directorio) {
            return response()->json([
                'mensaje' => 'Directorio creado.'
            ]);
        }

        return response()->json([
            'mensaje' => 'No se pudo crear el directorio.'
        ], 404);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request)
    {
        $validated = $request->validate([
            'nodo' => 'required',
            'elemento' => 'required',
        ]);

        $nodo = $this->geocimat . $validated['nodo'];
        if (!Storage::disk('public')->exists($nodo)) {
            return response()->json(['mensaje' => 'Directorio no encontrado.'], 404);
        }

        $elementos = $validated['elemento'];
        foreach ($elementos as $elemento) {
            Storage::disk('public')->deleteDirectory($elemento);
        }
        return response()->json([
            'mensaje' => 'Elemento eliminado.'
        ]);


        // return response()->json([
        //     'directorio' => 'Error eliminar el directorio'
        // ], 404);
    }


    /**
     * Download file or zip.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function download(Request $request)
    {
        $validated = $request->validate([
            'nodo' => 'required',
            'elemento' => 'required',
        ]);

        return response()->download(asset($validated['elemento'][0]));


        // $nodo = $this->geocimat . $validated['nodo'];
        // if (!Storage::disk('public')->exists($nodo)) {
        //     return response()->json(['mensaje' => 'Directorio no encontrado.'], 404);
        // }

        // $zip_file =  $validated['nodo'] . '-' . Str::random(5) . '.zip';
        // $zip = new \ZipArchive();
        // $zip->open(public_path($zip_file), \ZipArchive::CREATE | \ZipArchive::OVERWRITE);
        // $elementos = $validated['elemento'];
        // foreach ($elementos as $elemento) {
        //     $name = basename($elemento);
        //     $zip->addFile(storage_path($elemento),  $name);
        // }
        // $zip->close();

        // return response()->download(public_path($zip_file), $zip_file);

        // return response()->json([
        //     'mensaje' => 'Elemento eliminado.'
        // ]);


        // return response()->json([
        //     'directorio' => 'Error eliminar el directorio'
        // ], 404);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function upload(Request $request)
    {
        $request->validate([
            'idProyecto' => 'required',
            'directorio' => 'required',
            'archivos' => 'required',
        ]);

        $idProyecto =  $request->idProyecto;
        $directorio =  $request->directorio;
        $archivos = $request->file('archivos');
        $paths  = [];

        foreach ($archivos as $archivo) {
            $nombreArchivo = pathinfo($archivo->getClientOriginalName(), PATHINFO_FILENAME);
            $extension = $archivo->extension();
            $archivoConExtension = $this->obtenerNombre($directorio, $nombreArchivo, $extension);
            $storePath = Storage::disk('public')->putFileAs($directorio, $archivo, $archivoConExtension);
            $paths[] = $storePath;
        }
        return response()->json(['message' => sizeof($paths) . 'Elemento agregado.']);
    }


    function obtenerNombre($ruta, $nombre, $extension)
    {
        $i = 1;
        $nombreConExtension =  $nombre . "." . $extension;
        $nombreTemporal = $nombre;
        while (Storage::disk('public')->exists($ruta . '/' . $nombre . "." . $extension)) {
            $nombre = (string)$nombreTemporal . ' (' . $i . ')';
            $nombreConExtension = $nombre . "." . $extension;
            $i++;
        }
        return $nombreConExtension;
    }
}
