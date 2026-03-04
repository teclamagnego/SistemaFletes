<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Carrier extends Model
{
    use HasFactory;

    protected $fillable = [
        'nombre',
        'apellido',
        'documento',
        'telefono',
        'email',
        'vehiculo',
        'patente',
        'activo',
    ];

    protected $casts = [
        'activo' => 'boolean',
    ];

    public function getFullNameAttribute()
    {
        return "{$this->nombre} {$this->apellido}";
    }
}