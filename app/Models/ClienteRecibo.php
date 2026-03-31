<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ClienteRecibo extends Model
{
    use HasFactory;

    protected $fillable = [
        'cliente_id',
        'fecha',
        'monto',
        'nro_recibo',
        'forma_pago_id',
        'observaciones',
    ];

    public function cliente()
    {
        return $this->belongsTo(Cliente::class);
    }

    public function formaPago()
    {
        return $this->belongsTo(FormaPago::class);
    }
}