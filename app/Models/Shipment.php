<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Shipment extends Model
{
    use HasFactory;

    protected $fillable = [
        'tracking_number',
        'sender_id',
        'receiver_id',
        'cliente_id',
        'origin_agency_id',
        'destination_agency_id',
        'carrier_id',
        'commission_agency_id',
        'forma_pago_id',
        'fecha',
        'direccion_entrega',
        'status_id',
        'total_flete',
        'comision_monto',
        'factura_id',
        'agencia_f_origen_id',
        'agencia_f_destino_id',
        'notas',
    ];

    public function status()
    {
        return $this->belongsTo(ShipmentStatus::class , 'status_id');
    }

    public function factura()
    {
        return $this->belongsTo(ClienteFactura::class , 'factura_id');
    }

    public function sender()
    {
        return $this->belongsTo(Cliente::class , 'sender_id');
    }

    public function receiver()
    {
        return $this->belongsTo(Cliente::class , 'receiver_id');
    }

    public function cliente()
    {
        return $this->belongsTo(Cliente::class , 'cliente_id');
    }

    public function originAgency()
    {
        return $this->belongsTo(Agency::class , 'origin_agency_id');
    }

    public function destinationAgency()
    {
        return $this->belongsTo(Agency::class , 'destination_agency_id');
    }

    public function carrier()
    {
        return $this->belongsTo(Carrier::class);
    }

    public function commissionAgency()
    {
        return $this->belongsTo(Agency::class , 'commission_agency_id');
    }

    public function items()
    {
        return $this->hasMany(ShipmentItem::class);
    }

    public function logs()
    {
        return $this->hasMany(ShipmentLog::class);
    }

    public function formaPago()
    {
        return $this->belongsTo(FormaPago::class , 'forma_pago_id');
    }
}