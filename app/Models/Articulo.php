<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Articulo extends Model
{
    use HasFactory;

    protected $fillable = [
        'codigo',
        'nombre',
        'nombre_mostrar',
        'descripcion',
        'precio',
        'com_origen',
        'com_destino',
    ];

    protected $casts = [
        'precio' => 'decimal:2',
        'com_origen' => 'decimal:2',
        'com_destino' => 'decimal:2',
    ];

}