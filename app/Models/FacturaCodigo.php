<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FacturaCodigo extends Model
{
    use HasFactory;

    protected $fillable = [
        'nombre',
    ];

    // The primary key is 'id' and it's an integer, but by default Laravel expects 'id' to be auto-incrementing.
    // Since the migration sets $table->integer('id')->primary(); we need to set the primary key and incrementing.
    protected $primaryKey = 'id';
    public $incrementing = false; // Because we are setting it manually? Actually, the migration doesn't have autoIncrement.
    // Let's check the migration: $table->integer('id')->primary(); -> This is NOT auto-increment unless we do ->autoIncrement().
    // So we set incrementing to false.
    // However, note that the seeder might be setting the id. We'll leave it as false for now.
}