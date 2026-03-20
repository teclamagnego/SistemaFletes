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
            $this->importSqlAsStaging('documentacion/sql/dobleg_cliente_facturas.sql', 'temp_facturas', 'cliente_facturas');
        }
        if (!Schema::hasTable('temp_items')) {
            $this->importSqlAsStaging('documentacion/sql/dobleg_cliente_item_facturas.sql', 'temp_items', 'cliente_item_facturas');
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
                fecha, direccion_entrega, total_flete, comision_origen, comision_destino, notas, created_at, updated_at
            )
            SELECT 
                id, 
                numero, 
                CASE 
                    WHEN activo = 0 THEN 6 -- Cancelled
                    WHEN activo = 5 THEN 1 -- Admitted
                    WHEN activo = 4 THEN 5 -- Delivered
                    ELSE activo 
                END, 
                CASE WHEN origen_id = 0 THEN 1 ELSE origen_id END, -- sender_id
                CASE WHEN destino_id = 0 THEN 1 ELSE destino_id END, -- receiver_id
                cliente_id, -- Quien paga (Cuenta)
                CASE WHEN vendedor_id = 0 THEN 1 ELSE vendedor_id END, -- origin_agency_id
                CASE WHEN repartidor_id = 0 THEN 1 ELSE repartidor_id END, -- destination_agency_id
                1, -- carrier_id
                CASE WHEN vendedor_id = 0 THEN 1 ELSE vendedor_id END, -- commission_agency_id
                CASE WHEN contado = 1 THEN 1 ELSE 2 END, -- 1: Contado, 2: Cuenta Corriente
                fecha,
                lugardeentrega,
                total,
                com_venta,
                com_reparto,
                observacion,
                IFNULL(created_at, NOW()),
                IFNULL(updated_at, NOW())
            FROM temp_facturas
            ORDER BY fecha DESC, id DESC
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

        // Limpiar el SQL de comandos de transacción de phpMyAdmin que rompen el flujo de Laravel
        $sql = preg_replace('/SET AUTOCOMMIT = 0;/i', '', $sql);
        $sql = preg_replace('/START TRANSACTION;/i', '', $sql);
        $sql = preg_replace('/COMMIT;/i', '', $sql);

        $sql = str_replace("`$originalTable`", "`$tempTableName`", $sql);
        $sql = "SET FOREIGN_KEY_CHECKS=0;\n" . $sql;
        Schema::dropIfExists($tempTableName);
        DB::unprepared($sql);
    }
}