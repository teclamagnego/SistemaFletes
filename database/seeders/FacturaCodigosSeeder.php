<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class FacturaCodigosSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('factura_codigos')->insert([
            ['id' => 1, 'nombre' => 'FA'],
            ['id' => 2, 'nombre' => 'DA'],
            ['id' => 3, 'nombre' => 'CA'],
            ['id' => 6, 'nombre' => 'FB'],
            ['id' => 7, 'nombre' => 'DB'],
            ['id' => 8, 'nombre' => 'CB'],
            ['id' => 200, 'nombre' => 'REM'],
        ]);
    }
}
