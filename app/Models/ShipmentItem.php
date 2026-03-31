<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ShipmentItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'shipment_id',
        'articulo_id',
        'descripcion',
        'cantidad',
        'precio_unitario',
        'iva',
        'bonificacion',
        'total',
        'peso',
        'dimensiones',
        'tipo_mercancia',
    ];

    public function shipment()
    {
        return $this->belongsTo(Shipment::class);
    }

    public function articulo()
    {
        return $this->belongsTo(Articulo::class);
    }
}