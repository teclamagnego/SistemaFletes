<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Spatie\Permission\Models\Role;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;

class AgencyUserSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Asegurar el rol agencia
        $agenciaRole = Role::firstOrCreate(['name' => 'agencia']);

        $agencies = [
            ['id' => 3, 'name' => 'Tandil'],
            ['id' => 9, 'name' => 'Mar del Plata'],
            ['id' => 10, 'name' => 'Bs As'],
            ['id' => 11, 'name' => 'Costa Atlantica'],
            ['id' => 12, 'name' => 'Neco'],
            ['id' => 13, 'name' => 'Bahia'],
            ['id' => 14, 'name' => 'Balcarce'],
        ];

        $this->command->info("Creando usuarios de agencia...");

        foreach ($agencies as $agency) {
            // Usamos DB::table para forzar el ID si es necesario o updateOrCreate
            $user = User::updateOrCreate(
                ['id' => $agency['id']],
                [
                    'name' => $agency['name'],
                    'email' => str_replace(' ', '', strtolower($agency['name'])) . '@dobleg.com.ar',
                    'password' => Hash::make('tecla'),
                ]
            );
            
            if (!$user->hasRole('agencia')) {
                $user->assignRole($agenciaRole);
            }
        }

        $this->command->info('Hecho. Usuarios de agencia creados.');
    }
}
