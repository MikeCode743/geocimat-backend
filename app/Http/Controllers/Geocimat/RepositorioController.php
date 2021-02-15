<?php

namespace App\Http\Controllers\Geocimat;

use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\File;
use App\Http\Controllers\Controller;


class RepositorioController extends Controller
{

    //Estas funciones deben ir en un archivo aparte
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
            $carpeta = explode('/', $directorio)[1];
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
        // $proyecto = collect($this->getFileList($path));
        // $proyecto = $proyecto->merge($this->getSubDirectory($path));
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
        if (File::exists(Storage::disk('public')->path('/') . $id)) {
            return response()->json(['directorio' => $this->getDirectory($id), 'request' => $id]);
        }
        return response()->json(['mensaje' => 'Este directorio no existe', 'directorio' => []]);
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

        if (!Storage::disk('public')->exists($validated['nodoPadre'])) {
            return response()->json(['mensaje' => 'Proyecto no encontrado no existe'], 404);
        }

        $directorio = Storage::disk('public')->makeDirectory($validated['nodoPadre'] . '/' . $validated['folder']);
        if ($directorio) {
            return response()->json([
                'directorio' => 'Directorio creado'
            ]);
        }

        return response()->json([
            'directorio' => 'No se pudo crear el directorio'
        ]);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request)
    {
        //Eliminar un nuevo directorio
        $validated = $request->validate([
            'nodoPadre' => 'required',
            'folder' => 'required',
        ]);

        $subcarpeta = $validated['nodoPadre'] . '/' . $validated['folder'];
        if (!Storage::disk('public')->exists($subcarpeta)) {
            return response()->json(['mensaje' => 'Directorio no encontrado'], 404);
        }

        $directorio = Storage::disk('public')->deleteDirectory($subcarpeta);
        if ($directorio) {
            return response()->json([
                'directorio' => 'Directorio eliminado'
            ]);
        }

        return response()->json([
            'directorio' => 'No se pudo eliminar el directorio'
        ]);
    }


    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function upload(Request $request)
    {

        $validated = $request->validate([
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
        return response()->json(['message' => sizeof($paths) . ' Archivo Agregado']);
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
