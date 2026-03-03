<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Cliente extends Model
{
    use HasFactory;

    protected $fillable = [
        'nombre',
        'apellido',
        'razon_social',
        'cuit',
        'telefono',
        'email',
        'direccion',
        'localidad',
        'provincia',
    ];
}