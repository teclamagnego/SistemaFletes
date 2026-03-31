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
        Schema::table('shipments', function (Blueprint $table) {
            $table->dropColumn('comision_monto');
            $table->decimal('comision_origen', 12, 2)->default(0)->after('total_flete');
            $table->decimal('comision_destino', 12, 2)->default(0)->after('comision_origen');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('shipments', function (Blueprint $table) {
            $table->decimal('comision_monto', 12, 2)->default(0)->after('total_flete');
            $table->dropColumn(['comision_origen', 'comision_destino']);
        });
    }
};
