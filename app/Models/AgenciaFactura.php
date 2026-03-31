<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AgenciaFactura extends Model
{
    use HasFactory;

    protected $fillable = [
        'agency_id',
        'nro_factura',
        'fecha',
        'total',
        'falta_imputar',
        'observacion',
    ];

    public function agency()
    {
        return $this->belongsTo(Agency::class);
    }
}