<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ShipmentLog extends Model
{
    use HasFactory;

    protected $fillable = [
        'shipment_id',
        'user_id',
        'status_from_id',
        'status_to_id',
        'notas',
    ];

    public function statusFrom()
    {
        return $this->belongsTo(ShipmentStatus::class , 'status_from_id');
    }

    public function statusTo()
    {
        return $this->belongsTo(ShipmentStatus::class , 'status_to_id');
    }

    public function shipment()
    {
        return $this->belongsTo(Shipment::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}