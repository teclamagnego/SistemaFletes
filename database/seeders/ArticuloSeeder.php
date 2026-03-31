<?php

namespace Database\Seeders;

use App\Models\Articulo;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\File;

class ArticuloSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // 1. Borrar artículos actuales
        Schema::disableForeignKeyConstraints();
        Articulo::truncate();
        Schema::enableForeignKeyConstraints();

        $path = base_path('documentacion/sql/dobleg_articulos.sql');

        if (!File::exists($path)) {
            $this->command->error("No se encontró el archivo SQL en: {$path}");
            return;
        }

        $this->command->info("Cargando artículos desde {$path}...");

        $sqlContent = File::get($path);

        // Extraer los bloques de valores de los INSERT INTO
        // El formato es (...), (...), (...);
        if (preg_match('/INSERT INTO `articulos` .* VALUES\s+(.*);/sU', $sqlContent, $matches)) {
            $valuesBlock = $matches[1];

            // Separar por filas. Las filas terminan en ), y empiezan con (
            // Pero cuidado con los paréntesis dentro de los textos.
            // Para un dump de este tamaño, podemos usar un enfoque de línea por línea si el dump está formateado así.

            $rows = preg_split('/\),\s*\(/', trim($valuesBlock, "() \t\n\r\0\x0B"));

            $count = 0;
            foreach ($rows as $row) {
                // La fila es algo como: 2, '001', 'Bultos', NULL, 1, 1, 1, '21.00', '0.00', 1, 0, 0, NULL, NULL, '0.000', '0.000', '0.000', '25.000', '25.000', ...
                // Podemos usar str_getcsv para parsear la línea considerando las comas y comillas.
                $data = str_getcsv($row, ",", "'");

                if (count($data) >= 19) {
                    // Mapeo según el orden del SQL:
                    // 0: id
                    // 1: codigo
                    // 2: descripcion (-> nombre)
                    // 3: descripcion_larga (-> descripcion)
                    // 13: pventa1 (-> precio)
                    // 17: com_venta (-> com_origen)
                    // 18: com_reparto (-> com_destino)

                    Articulo::create([
                        'id' => trim($data[0]),
                        'codigo' => trim($data[1]),
                        'nombre' => trim($data[2]),
                        'descripcion' => trim($data[3]) === 'NULL' ? null : trim($data[3]),
                        'precio' => $this->parseNumber($data[13]),
                        'com_origen' => $this->parseNumber($data[17]),
                        'com_destino' => $this->parseNumber($data[18]),
                    ]);
                    $count++;
                }
            }
            $this->command->info("Se cargaron {$count} artículos correctamente.");
        }
        else {
            $this->command->error("No se pudieron encontrar los datos de INSERT en el archivo SQL.");
        }
    }

    /**
     * Limpia y parsea valores numéricos de SQL (NULL o strings con decimales)
     */
    private function parseNumber($value)
    {
        $value = trim($value);
        if (strtoupper($value) === 'NULL' || $value === '') {
            return 0;
        }
        return (float)$value;
    }
}