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
        'status_from',
        'status_to',
        'notas',
    ];

    public function shipment()
    {
        return $this->belongsTo(Shipment::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}