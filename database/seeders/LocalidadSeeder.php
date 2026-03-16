<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Schema;

class LocalidadSeeder extends Seeder
{
    public function run(): void
    {
        $path = 'documentacion/dobleg_localidades.sql';
        $tempTable = 'temp_localidades';

        $this->command->info("Cargando localidades en tabla temporal...");
        $this->importSqlAsStaging($path, $tempTable, 'localidades');

        $this->command->info("Migrando a tabla definitiva...");
        DB::statement("
            INSERT INTO localidades (id, nombre, created_at, updated_at)
            SELECT id, nombre, IFNULL(created_at, NOW()), IFNULL(updated_at, NOW())
            FROM $tempTable
            ON DUPLICATE KEY UPDATE nombre = VALUES(nombre), updated_at = NOW()
        ");

        $this->command->info("Hecho. Tabla $tempTable conservada para inspección.");
    }

    private function importSqlAsStaging($path, $tempTableName, $originalTable)
    {
        $realPath = base_path($path);
        if (!File::exists($realPath)) {
            $this->command->error("No se encontró $path");
            return;
        }

        $sql = File::get($realPath);
        $sql = str_replace("`$originalTable`", "`$tempTableName`", $sql);
        $sql = "SET FOREIGN_KEY_CHECKS=0;\n" . $sql;

        Schema::dropIfExists($tempTableName);
        DB::unprepared($sql);
    }
}