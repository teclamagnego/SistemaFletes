<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Cliente extends Model
{
    use HasFactory;

    protected $fillable = [
        'nombre_fantasia',
        'razon_social',
        'tipodoc_id',
        'documento_nro',
        'telefono',
        'email',
        'direccion',
        'localidad_id',
        'tipocuenta_id',
        'tipoiva_id',
        'agenciaorigen_id',
        'agenciadestino_id',
        'observacion'
    ];

    public function tipoDoc()
    {
        return $this->belongsTo(TipoDoc::class , 'tipodoc_id');
    }

    public function localidad()
    {
        return $this->belongsTo(Localidad::class , 'localidad_id');
    }

    public function tipoCuenta()
    {
        return $this->belongsTo(TipoCuenta::class , 'tipocuenta_id');
    }

    public function tipoIva()
    {
        return $this->belongsTo(TipoIva::class , 'tipoiva_id');
    }

    public function agenciaOrigen()
    {
        return $this->belongsTo(Agency::class , 'agenciaorigen_id');
    }

    public function agenciaDestino()
    {
        return $this->belongsTo(Agency::class , 'agenciadestino_id');
    }

    public function shipmentsPaid()
    {
        return $this->hasMany(Shipment::class , 'cliente_id');
    }

    public function recibos()
    {
        return $this->hasMany(ClienteRecibo::class);
    }
}