<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Proveedor extends Model
{
    use HasFactory;

    protected $table = 'proveedores';

    protected $fillable = [
        'nombre',
        'razon_social',
        'cuit',
        'telefono',
        'email',
        'direccion',
        'localidad',
        'provincia',
    ];

    public function articulos()
    {
        return $this->belongsToMany(Articulo::class , 'articulo_proveedor')
            ->withPivot('precio_compra')
            ->withTimestamps();
    }
}