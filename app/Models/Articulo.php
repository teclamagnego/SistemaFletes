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
        'descripcion',
        'precio',
        'stock',
    ];

    public function rubros()
    {
        return $this->belongsToMany(Rubro::class , 'articulo_rubro')
            ->withTimestamps();
    }

    public function proveedores()
    {
        return $this->belongsToMany(Proveedor::class , 'articulo_proveedor')
            ->withPivot('precio_compra')
            ->withTimestamps();
    }
}