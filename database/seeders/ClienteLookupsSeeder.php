<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ClienteLookupsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Tipos de Documento
        \App\Models\TipoDoc::firstOrCreate(['codigo' => 'DNI'], ['nombre' => 'Documento Nacional de Identidad']);
        \App\Models\TipoDoc::firstOrCreate(['codigo' => 'CUIT'], ['nombre' => 'CUIT/CUIL']);
        \App\Models\TipoDoc::firstOrCreate(['codigo' => 'PAS'], ['nombre' => 'Pasaporte']);


        // Tipos de Cuenta
        \App\Models\TipoCuenta::firstOrCreate(['nombre' => 'Cuenta Corriente']);
        \App\Models\TipoCuenta::firstOrCreate(['nombre' => 'Contado']);

        // Tipos de IVA
        \App\Models\TipoIva::firstOrCreate(['nombre' => 'Responsable Inscripto']);
        \App\Models\TipoIva::firstOrCreate(['nombre' => 'Monotributo']);
        \App\Models\TipoIva::firstOrCreate(['nombre' => 'Exento']);
        \App\Models\TipoIva::firstOrCreate(['nombre' => 'Consumidor Final']);
    }
}