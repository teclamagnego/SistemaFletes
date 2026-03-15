<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Empresa extends Model
{
    use HasFactory;

    protected $table = 'empresas';

    protected $fillable = [
        'nombre_fantasia',
        'razon_social',
        'cuit',
        'tipoiva_id',
        'activo',
        'logo',
        'esagenteretencioniva'
    ];

    protected $casts = [
        'activo' => 'boolean',
        'esagenteretencioniva' => 'boolean'
    ];

    public function sucursales()
    {
        return $this->hasMany(Sucursal::class , 'empresa_id', 'id');
    }
}