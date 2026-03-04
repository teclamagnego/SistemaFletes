<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Agency extends Model
{
    use HasFactory;

    protected $fillable = [
        'codigo',
        'nombre',
        'direccion',
        'localidad',
        'provincia',
        'telefono',
        'email',
        'comision_porcentaje',
        'activa',
    ];

    protected $casts = [
        'comision_porcentaje' => 'decimal:2',
        'activa' => 'boolean',
    ];
}