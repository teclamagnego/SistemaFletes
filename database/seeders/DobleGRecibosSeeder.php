<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Schema;

class DobleGRecibosSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        ini_set('memory_limit', '1024M');

        $this->command->info("Migración de Recibos de Clientes...");

        Schema::disableForeignKeyConstraints();

        // 1. Importación Staging (Solo si no existen)
        if (!Schema::hasTable('temp_cliente_recibos')) {
            $this->importSqlAsStaging('documentacion/sql/cliente_recibos.sql', 'temp_cliente_recibos', 'cliente_recibos');
        }
        if (!Schema::hasTable('temp_cliente_recibos_formas_pago')) {
            $this->importSqlAsStaging('documentacion/sql/cliente_recibos_formas_pago.sql', 'temp_cliente_recibos_formas_pago', 'cliente_recibos_formas_pago');
        }

        // LIMPIAR PRODUCCION
        $this->command->warn("Limpiando tabla de producción (cliente_recibos)...");
        DB::table('cliente_recibos')->truncate();

        // 2. Migración de datos cruzados
        $this->command->info("Insertando recibos...");
        DB::statement("
            INSERT INTO cliente_recibos (
                cliente_id, fecha, monto, nro_recibo, forma_pago_id, observaciones, created_at, updated_at
            )
            SELECT 
                r.cliente_id,
                r.fecha,
                fp.importe,
                r.numero,
                fp.formas_pago_id,
                'Migrado desde sistema anterior',
                NOW(),
                NOW()
            FROM temp_cliente_recibos r
            JOIN temp_cliente_recibos_formas_pago fp ON fp.cliente_recibos_id = r.id
        ");

        Schema::enableForeignKeyConstraints();
        $this->command->info("Migración completada. Tablas temporales conservadas.");
    }

    private function importSqlAsStaging($path, $tempTableName, $originalTable)
    {
        $realPath = base_path($path);
        if (!File::exists($realPath)) {
            $this->command->warn("No se encontró el archivo $path.");
            return;
        }

        $this->command->info("Cargando $path en $tempTableName...");
        $sql = File::get($realPath);

        // Limpiar comandos de transacción de phpMyAdmin que rompen el flujo de Laravel
        $sql = preg_replace('/SET AUTOCOMMIT = 0;/i', '', $sql);
        $sql = preg_replace('/START TRANSACTION;/i', '', $sql);
        $sql = preg_replace('/COMMIT;/i', '', $sql);

        // Renombrar tabla en el SQL para que se importe como variable temporal usando las comillas traseras ` (backticks)
        $sql = str_replace("`$originalTable`", "`$tempTableName`", $sql);
        $sql = "SET FOREIGN_KEY_CHECKS=0;\n" . $sql;
        
        Schema::dropIfExists($tempTableName);
        DB::unprepared($sql);
    }
}
