<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MstUsuarios extends Model
{
    use HasFactory;

    protected $fillable = [
        'login',
        'password',
        'nombre',
        'habilitado',
        'cambiar_password',
        'id_grupo',
        'idioma',
        'messenger',
        'administrable',
        'correo',
        'cedula',
        'fecha_creacion',
        'fecha_ineactivaccion',
        'fecha_ultima_modificacion',
        'fecha_ultima_ingreso'

    ];
}
