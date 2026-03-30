<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Schema;

class DobleGGuiaSeederMarzo2026 extends Seeder
{
    /**
     * Run the database seeds.
     * Uso: SEED_LIMIT=10 php artisan db:seed --class=DobleGGuiaSeederMarzo2026
     */
    public function run(): void
    {
        ini_set('memory_limit', '1024M');

        $this->command->info("Migración de guías MARZO 2026. (Sin límite).");

        Schema::disableForeignKeyConstraints();

        // 0. Importación Staging (Solo si no existen)
        if (!Schema::hasTable('temp_facturas')) {
            $this->importSqlAsStaging('documentacion/sql/dobleg_cliente_facturas.sql', 'temp_facturas', 'cliente_facturas');
        }
        if (!Schema::hasTable('temp_items')) {
            $this->importSqlAsStaging('documentacion/sql/dobleg_cliente_item_facturas.sql', 'temp_items', 'cliente_item_facturas');
        }

        // LIMPIAR PRODUCCION ANTES DE RE-INSERTAR
        $truncate = env('SEED_TRUNCATE', true);
        if ($truncate) {
            $this->command->warn("Limpiando tablas de producción (shipments, items, facturas)...");
            DB::table('shipment_items')->truncate();
            DB::table('shipments')->truncate();
            DB::table('cliente_facturas')->truncate();
        } else {
            $this->command->info("Omitiendo truncado de tablas (SEED_START > 1 o SEED_TRUNCATE=false)");
        }

        $marchCondition = " updated_at >= '2026-03-01 00:00:00' AND updated_at <= '2026-03-31 23:59:59'";

        // 1. Migración de Guías
        $this->command->info("Insertando guías de Marzo 2026 en production...");
        DB::statement("
            INSERT IGNORE INTO shipments (
                id, tracking_number, status_id, sender_id, receiver_id, cliente_id, 
                origin_agency_id, destination_agency_id, carrier_id, commission_agency_id, forma_pago_id, 
                fecha, direccion_entrega, total_flete, comision_origen, comision_destino, notas, created_at, updated_at
            )
            SELECT 
                id, 
                numero, 
                5, -- Siempre entregado por el where
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
                ROUND(total, 2),
                com_venta,
                com_reparto,
                observacion,
                IFNULL(created_at, NOW()),
                IFNULL(updated_at, NOW())
            FROM temp_facturas
            WHERE $marchCondition
            ORDER BY fecha DESC, id DESC
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
                ROUND(i.punitario * 1.21, 2),
                21,
                ROUND(ROUND(i.punitario * 1.21, 2) * i.cantidad, 2),
                IFNULL(i.created_at, NOW()),
                IFNULL(i.updated_at, NOW())
            FROM temp_items i
            JOIN shipments s ON s.id = i.cliente_facturas_id
            JOIN (
                SELECT id 
                FROM temp_facturas 
                WHERE $marchCondition
                ORDER BY fecha DESC, id DESC 
            ) f ON f.id = s.id
            WHERE i.descripcion NOT LIKE 'Envio%' 
              AND i.descripcion NOT LIKE 'Envío%' 
              AND i.descripcion NOT LIKE 'Flete%'
              AND i.descripcion NOT LIKE 'GUIA%'
        ");

        // 3. Generación de Facturas Automáticas
        $this->command->info("Generando facturas automáticas (Contado y CC hasta 28/02/2026)...");
        DB::statement("
            INSERT INTO cliente_facturas (
                cliente_id, forma_pago_id, fecha, total, falta_imputar, observacion, nro_factura, created_at, updated_at
            )
            SELECT 
                s.cliente_id, 
                s.forma_pago_id, 
                s.fecha, 
                s.total_flete, 
                CASE WHEN s.forma_pago_id = 2 THEN s.total_flete ELSE 0 END, 
                CONCAT('Facturación automática migración guía #', s.id),
                CONCAT('TEMP_MIG_', s.id),
                s.created_at, 
                s.updated_at
            FROM shipments s
            LEFT JOIN temp_facturas t ON t.id = s.id
            WHERE s.forma_pago_id != 2
               OR (s.forma_pago_id = 2 AND t.activo = 4 AND DATE(t.updated_at) <= '2026-02-28')
        ");

        $this->command->info("Vinculando facturas a guías...");
        DB::statement("
            UPDATE shipments s
            JOIN cliente_facturas f ON f.nro_factura = CONCAT('TEMP_MIG_', s.id)
            SET s.factura_id = f.id
        ");

        DB::statement("
            UPDATE cliente_facturas 
            SET nro_factura = NULL 
            WHERE nro_factura LIKE 'TEMP_MIG_%'
        ");

        // 4. Shipment Logs para guías entregadas
        $this->command->info("Generando logs para guías entregadas en Marzo...");
        DB::statement("
            INSERT INTO shipment_logs (
                shipment_id, user_id, status_from_id, status_to_id, notas, created_at, updated_at
            )
            SELECT 
                t.id, 
                1, 
                1, 
                5, 
                'Guía entregada (migración)', 
                IFNULL(t.updated_at, NOW()), 
                IFNULL(t.updated_at, NOW())
            FROM (
                SELECT id, updated_at, activo 
                FROM temp_facturas 
                WHERE $marchCondition
                ORDER BY fecha DESC, id DESC 
            ) t
        ");

        Schema::enableForeignKeyConstraints();
        $this->command->info("Migración parcial de MARZO completada.");
    }

    private function importSqlAsStaging($path, $tempTableName, $originalTable)
    {
        $realPath = base_path($path);
        if (!File::exists($realPath))
            return;
        $this->command->info("Cargando $path en $tempTableName...");
        $sql = File::get($realPath);

        // Limpiar comandos de transacción de phpMyAdmin que rompen el flujo de Laravel
        $sql = preg_replace('/SET AUTOCOMMIT = 0;/i', '', $sql);
        $sql = preg_replace('/START TRANSACTION;/i', '', $sql);
        $sql = preg_replace('/COMMIT;/i', '', $sql);

        $sql = str_replace("`$originalTable`", "`$tempTableName`", $sql);
        $sql = "SET FOREIGN_KEY_CHECKS=0;\n" . $sql;
        Schema::dropIfExists($tempTableName);
        DB::unprepared($sql);
    }
}
