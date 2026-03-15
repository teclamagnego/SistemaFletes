<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ShipmentStatus extends Model
{
    const ADMITTED = 1;
    const IN_OFFICE = 2;
    const IN_TRANSIT = 3;
    const IN_DESTINATION = 4;
    const DELIVERED = 5;
    const CANCELLED = 6;

    protected $fillable = ['name', 'color'];
}