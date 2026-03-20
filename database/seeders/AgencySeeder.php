<?php

namespace Database\Seeders;

use App\Models\Agency;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\File;

class AgencySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $this->command->info("Starting AgencySeeder...");

        // Find the INSERT INTO statement
        $path = base_path('documentacion/sql/agencias.sql');
        if (!File::exists($path)) {
            $this->command->error("No SQL file.");
            return;
        }

        $sqlContent = File::get($path);

        $insertPos = strpos($sqlContent, 'INSERT INTO `agencias`');
        if ($insertPos !== false) {
            $valuesPos = strpos($sqlContent, 'VALUES', $insertPos);
            if ($valuesPos !== false) {
                $endPos = strpos($sqlContent, ';', $valuesPos);
                $valuesBlock = substr($sqlContent, $valuesPos + 6, $endPos - ($valuesPos + 6));

                $rows = preg_split('/\),\s*\(/', trim($valuesBlock, "() \t\n\r\0\x0B"));

                $this->command->info("Parsed " . count($rows) . " rows. Updating or creating agencies...");

                $count = 0;
                foreach ($rows as $row) {
                    $data = str_getcsv($row, ",", "'");

                    if (count($data) >= 6) {
                        try {
                            Agency::updateOrCreate(
                            ['id' => trim($data[0])], // Update by ID match
                            [
                                'codigo' => trim($data[1]),
                                'nombre' => trim($data[2]),
                                'com_origen' => $this->parseNumber($data[3]),
                                'com_destino' => $this->parseNumber($data[4]),
                                'activa' => trim($data[5]) == '1' ? true : false,
                            ]
                            );
                            $count++;
                        }
                        catch (\Exception $e) {
                            $this->command->error("Failed to insert row: " . trim($data[0]) . ". Error: " . $e->getMessage());
                        }
                    }
                }
                $this->command->info("Se actualizaron/cargaron {$count} agencias.");

            }
        }
    }

    private function parseNumber($value)
    {
        $value = trim($value);
        if (strtoupper($value) === 'NULL' || $value === '')
            return 0;
        return (float)$value;
    }
}
