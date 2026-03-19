<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ClienteFactura extends Model
{
    use HasFactory;

    protected $fillable = [
        'cliente_id',
        'forma_pago_id',
        'nro_factura',
        'fecha',
        'total',
        'falta_imputar',
        'observacion',
    ];

    public function cliente()
    {
        return $this->belongsTo(Cliente::class);
    }

    public function formaPago()
    {
        return $this->belongsTo(FormaPago::class);
    }

    public function shipments()
    {
        return $this->hasMany(Shipment::class , 'factura_id');
    }
}