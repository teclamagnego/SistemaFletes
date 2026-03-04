<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TipoDoc extends Model
{
    protected $table = 'tipos_doc';
    protected $fillable = ['codigo', 'nombre'];
}