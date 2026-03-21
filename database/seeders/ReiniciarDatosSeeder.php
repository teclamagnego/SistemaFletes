<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class ReiniciarDatosSeeder extends Seeder
{
    /**
     * Limpia las tablas de movimientos (vaciado total).
     */
    public function run(): void
    {
        // Desactivar restricciones de claves foráneas
        Schema::disableForeignKeyConstraints();

        // Lista de tablas a vaciar
        $tables = [
            'agencia_facturas',
            'agencia_recibos',
            'cliente_facturas',
            'cliente_recibos',
            'shipments',
            'shipment_items',
            'shipment_logs',
        ];

        foreach ($tables as $table) {
            DB::table($table)->truncate();
        }

        // Reactivar restricciones de claves foráneas
        Schema::enableForeignKeyConstraints();

        $this->command->info('Las tablas de movimientos han sido reiniciadas correctamente.');
    }
}
