<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Cheque extends Model
{
    protected $fillable = [
        'recibo_id',
        'origen_id',
        'destino_id',
        'monto',
        'fecha',
        'numero',
        'observacion_origen',
        'observacion_destino',
    ];

    public function cliente()
    {
        return $this->belongsTo(Cliente::class, 'origen_id');
    }
}
