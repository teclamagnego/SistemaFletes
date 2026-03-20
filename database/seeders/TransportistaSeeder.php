<?php

namespace Database\Seeders;

use App\Models\Carrier;
use Illuminate\Database\Seeder;

class TransportistaSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Carrier::updateOrCreate(
            ['documento' => '20000000'],
            [
                'nombre' => 'Transportista',
                'apellido' => 'General',
                'telefono' => '12345678',
                'email' => 'transportista@ejemplo.com',
                'vehiculo' => 'Camioneta',
                'patente' => 'ABC 123',
                'activo' => true,
            ]
        );
    }
}
