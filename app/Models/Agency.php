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
        'localidad_id',
        'telefono',
        'email',
        'com_origen',
        'com_destino',
        'activa',
    ];

    protected $casts = [
        'com_origen' => 'decimal:2',
        'com_destino' => 'decimal:2',
        'activa' => 'boolean',
    ];

    public function localidad()
    {
        return $this->belongsTo(Localidad::class);
    }

    public function shipmentsAsOrigin()
    {
        return $this->hasMany(\App\Models\Shipment::class , 'origin_agency_id');
    }

    public function shipmentsAsDestination()
    {
        return $this->hasMany(\App\Models\Shipment::class , 'destination_agency_id');
    }
}