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
        'origin_agency_id',
        'destination_agency_id',
        'carrier_id',
        'commission_agency_id',
        'forma_pago_id',
        'fecha',
        'direccion_entrega',
        'status',
        'total_flete',
        'comision_monto',
        'notas',
    ];

    public function sender()
    {
        return $this->belongsTo(Cliente::class , 'sender_id');
    }

    public function receiver()
    {
        return $this->belongsTo(Cliente::class , 'receiver_id');
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