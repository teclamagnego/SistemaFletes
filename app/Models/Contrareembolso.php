<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Contrareembolso extends Model
{
    use HasFactory;

    protected $fillable = [
        'guia_id',
        'cliente_id',
        'monto',
        'fecha_cobrado',
        'fecha_rendido',
    ];

    protected $casts = [
        'monto' => 'decimal:2',
        'fecha_cobrado' => 'date',
        'fecha_rendido' => 'date',
    ];

    public function shipment()
    {
        return $this->belongsTo(Shipment::class, 'guia_id');
    }

    public function cliente()
    {
        return $this->belongsTo(Cliente::class);
    }
}
