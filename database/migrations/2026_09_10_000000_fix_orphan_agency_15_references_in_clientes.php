<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // La agencia 15 era Tandil (duplicada de la agencia 1) y fue borrada sin
        // reasignar los clientes que la referenciaban, dejando FKs huérfanas.
        DB::table('clientes')->where('agenciaorigen_id', 15)->update(['agenciaorigen_id' => 1]);
        DB::table('clientes')->where('agenciadestino_id', 15)->update(['agenciadestino_id' => 1]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // No reversible: no hay forma de saber qué filas tenían originalmente el 15.
    }
};
