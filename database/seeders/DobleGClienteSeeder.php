<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Schema;

class DobleGClienteSeeder extends Seeder
{
    public function run(): void
    {
        $path = 'documentacion/sql/dobleg_clientes.sql';
        $tempTable = 'temp_clientes';

        if (!Schema::hasTable($tempTable)) {
            $this->command->info("Cargando clientes en tabla temporal...");
            $this->importSqlAsStaging($path, $tempTable, 'clientes');
        }
        else {
            $this->command->info("Tabla $tempTable ya existe, reutilizando...");
        }

        Schema::disableForeignKeyConstraints();

        $this->command->info("Migrando a tabla definitiva...");
        DB::statement("
            INSERT INTO clientes (id, nombre_fantasia, razon_social, direccion, telefono, email, documento_nro, tipodoc_id, tipoiva_id, localidad_id, tipocuenta_id, agenciaorigen_id, agenciadestino_id, created_at, updated_at)
            SELECT id, nombre, nombre, direccion, IFNULL(telefono, ''), IFNULL(mail, ''), IFNULL(numeroDoc, '0'), 1, 1, IFNULL(localidad_id, 1), 1, IFNULL(vendedor_id, 1), IFNULL(repartidor_id, 1), IFNULL(created_at, NOW()), IFNULL(updated_at, NOW())
            FROM $tempTable
            ON DUPLICATE KEY UPDATE 
                nombre_fantasia = VALUES(nombre_fantasia), 
                razon_social = VALUES(razon_social),
                direccion = VALUES(direccion),
                documento_nro = VALUES(documento_nro),
                updated_at = NOW()
        ");

        Schema::enableForeignKeyConstraints();

        $count = DB::table('clientes')->count();
        $this->command->info("Hecho. $count clientes en producción. Tabla $tempTable conservada.");
    }

    private function importSqlAsStaging($path, $tempTableName, $originalTable)
    {
        $realPath = base_path($path);
        if (!File::exists($realPath)) {
            $this->command->error("No se encontró $path");
            return;
        }
        $sql = File::get($realPath);

        // Limpiar el SQL de comandos de transacción de phpMyAdmin que rompen el flujo de Laravel
        $sql = preg_replace('/SET AUTOCOMMIT = 0;/i', '', $sql);
        $sql = preg_replace('/START TRANSACTION;/i', '', $sql);
        $sql = preg_replace('/COMMIT;/i', '', $sql);

        $sql = str_replace("`$originalTable`", "`$tempTableName`", $sql);
        $sql = "SET FOREIGN_KEY_CHECKS=0;\n" . $sql;
        Schema::dropIfExists($tempTableName);
        try {
            DB::unprepared($sql);
        }
        catch (\Exception $e) {
            $this->command->warn("Aviso en carga staging: " . substr($e->getMessage(), 0, 120));
        }
    }
}