<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Schema;

class DobleGGuiaSeeder extends Seeder
{
    /**
     * Run the database seeds.
     * Uso: SEED_LIMIT=10 php artisan db:seed --class=DobleGGuiaSeeder
     */
    public function run(): void
    {
        ini_set('memory_limit', '1024M');

        // Obtenemos el límite desde una variable de entorno o por defecto 10
        $limit = env('SEED_LIMIT', 10);

        $this->command->info("Migración de guías (Shipments). Límite actual: $limit");

        Schema::disableForeignKeyConstraints();

        // 0. Importación Staging (Solo si no existen)
        if (!Schema::hasTable('temp_facturas')) {
            $this->importSqlAsStaging('documentacion/dobleg_cliente_facturas.sql', 'temp_facturas', 'cliente_facturas');
        }
        if (!Schema::hasTable('temp_items')) {
            $this->importSqlAsStaging('documentacion/dobleg_cliente_item_facturas.sql', 'temp_items', 'cliente_item_facturas');
        }

        // LIMPIAR PRODUCCION ANTES DE RE-INSERTAR PROBA
        $this->command->warn("Limpiando tablas de producción (shipments y shipment_items)...");
        DB::table('shipment_items')->truncate();
        DB::table('shipments')->truncate();

        // 1. Migración de Guías
        $this->command->info("Insertando $limit guías en production...");
        DB::statement("
            INSERT INTO shipments (
                id, tracking_number, status_id, sender_id, receiver_id, cliente_id, 
                origin_agency_id, destination_agency_id, carrier_id, commission_agency_id, forma_pago_id, 
                fecha, total_flete, notas, created_at, updated_at
            )
            SELECT 
                id, 
                CONCAT('OLD-', numero, '-', id), 
                5, -- Entregado
                vendedor_id, -- Remitente
                repartidor_id, -- Destinatario
                cliente_id, -- Quien paga (Cuenta)
                CASE WHEN origen_id = 0 THEN 1 ELSE origen_id END,
                CASE WHEN destino_id = 0 THEN 1 ELSE destino_id END,
                1, 
                CASE WHEN origen_id = 0 THEN 1 ELSE origen_id END, 
                CASE 
                    WHEN cliente_id = repartidor_id THEN 4 -- Pago en Destino
                    WHEN contado = 1 THEN 1 -- Contado
                    ELSE 2 -- Cuenta Corriente
                END,
                fecha,
                total,
                observacion,
                IFNULL(created_at, NOW()),
                IFNULL(updated_at, NOW())
            FROM temp_facturas
            ORDER BY id ASC
            LIMIT $limit
        ");

        // 2. Migración de Items de esas guías
        $this->command->info("Insertando ítems correspondientes...");
        DB::statement("
            INSERT INTO shipment_items (
                shipment_id, articulo_id, descripcion, cantidad, precio_unitario, iva, total, created_at, updated_at
            )
            SELECT 
                i.cliente_facturas_id,
                i.articulo_id,
                i.descripcion,
                i.cantidad,
                (i.punitario * 1.21),
                21,
                (i.cantidad * (i.punitario * 1.21)),
                IFNULL(i.created_at, NOW()),
                IFNULL(i.updated_at, NOW())
            FROM temp_items i
            JOIN shipments s ON s.id = i.cliente_facturas_id
            WHERE i.descripcion NOT LIKE 'Envio%' 
              AND i.descripcion NOT LIKE 'Envío%' 
              AND i.descripcion NOT LIKE 'Flete%'
              AND i.descripcion NOT LIKE 'GUIA%'
        ");

        Schema::enableForeignKeyConstraints();
        $this->command->info("Migración parcial completada. Tablas temp_facturas y temp_items conservadas.");
    }

    private function importSqlAsStaging($path, $tempTableName, $originalTable)
    {
        $realPath = base_path($path);
        if (!File::exists($realPath))
            return;
        $this->command->info("Cargando $path en $tempTableName...");
        $sql = File::get($realPath);
        $sql = str_replace("`$originalTable`", "`$tempTableName`", $sql);
        $sql = "SET FOREIGN_KEY_CHECKS=0;\n" . $sql;
        Schema::dropIfExists($tempTableName);
        DB::unprepared($sql);
    }
}