<?php

namespace App\Models\Geocimat;

use Illuminate\Database\Eloquent\Model;

class Proyecto extends Model
{
    //
    protected $table = 'geo_proyecto';
    protected $primaryKey = 'identificador';
    protected $fillable = ['identificador', 'nombre', 'id_clasificacion', 'fecha_creado', 'descripcion', 'longitud', 'latitud','directorio_base'];

    public $incrementing = false;
    public $timestamps = false;


}
