<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('agencia_facturas', function (Blueprint $table) {
            $table->decimal('falta_imputar', 15, 2)->after('total');
        });
        
        // Inicializar falta_imputar con el total de las facturas existentes
        DB::statement('UPDATE agencia_facturas SET falta_imputar = total');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('agencia_facturas', function (Blueprint $table) {
            $table->dropColumn('falta_imputar');
        });
    }
};
