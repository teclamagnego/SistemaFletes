<?php

namespace Database\Seeders;

use App\Models\Empresa;
use App\Models\Sucursal;
use Illuminate\Database\Seeder;

class EmpresaSucursalSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Empresa por defecto
        $empresa = Empresa::updateOrCreate(
        ['id' => 1],
        [
            'nombre_fantasia' => 'DobleG',
            'razon_social' => 'DobleG',
            'cuit' => '2222222',
            'tipoiva_id' => 1,
            'activo' => true,
            'esagenteretencioniva' => false,
        ]
        );

        // Sucursal por defecto
        Sucursal::updateOrCreate(
        ['id' => 1],
        [
            'nombre' => 'Central',
            'empresa_id' => $empresa->id,
            'domicilio' => 'Calle Principal 123',
            'telefono' => '0249 154 550005 / 0249 154 571106',
            'localidad' => 'Tandil',
            'puntoventa' => 1,
            'produccion' => 1,
            'edita_remitos' => true,
            'fecha_inicio_actividades' => '2025-01-01',
        ]
        );
    }
}