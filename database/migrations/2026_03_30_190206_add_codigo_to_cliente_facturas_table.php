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
        Schema::table('cliente_facturas', function (Blueprint $table) {
            $table->integer('codigo')->default(200)->after('nro_factura');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('cliente_facturas', function (Blueprint $table) {
            $table->dropColumn('codigo');
        });
    }
};
