<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

use App\Models\FormaPago;

class FormasPagoSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $formas = ['Contado', 'Cuenta Corriente', 'Transferencia'];
        foreach ($formas as $forma) {
            FormaPago::firstOrCreate(['nombre' => $forma]);
        }
    }
}