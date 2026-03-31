<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AgenciaRecibo extends Model
{
    use HasFactory;

    protected $fillable = [
        'agency_id',
        'fecha',
        'monto',
        'nro_recibo',
        'forma_pago_id',
        'observaciones',
    ];

    public function agency()
    {
        return $this->belongsTo(Agency::class);
    }

    public function formaPago()
    {
        return $this->belongsTo(FormaPago::class);
    }
}