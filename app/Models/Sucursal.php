<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Sucursal extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'sucursales';

    protected $fillable = [
        'nombre',
        'listasprecios',
        'empresa_id',
        'domicilio',
        'telefono',
        'localidad',
        'puntoventa',
        'fecha_inicio_actividades',
        'produccion',
        'alias_cbu',
        'tope_lineas_factura',
        'redondeo',
        'decimales',
        'depositos',
        'edita_remitos',
        'max_botones_articulos'
    ];

    protected $casts = [
        'fecha_inicio_actividades' => 'date',
        'edita_remitos' => 'boolean',
        'produccion' => 'integer',
        'tope_lineas_factura' => 'integer',
        'redondeo' => 'integer',
        'decimales' => 'integer',
        'max_botones_articulos' => 'integer'
    ];

    public function empresa()
    {
        return $this->belongsTo(Empresa::class , 'empresa_id', 'id');
    }
}