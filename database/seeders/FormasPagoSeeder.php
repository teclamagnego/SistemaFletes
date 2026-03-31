<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use App\Models\FormaPago;

class FormasPagoSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        FormaPago::truncate();
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        $formas = ['Contado', 'Cuenta Corriente', 'Transferencia', 'Cheques'];
        foreach ($formas as $forma) {
            FormaPago::firstOrCreate(['nombre' => $forma]);
        }
    }
}