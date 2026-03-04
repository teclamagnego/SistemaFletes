<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('agencies', function (Blueprint $table) {
            // Sacar comision_porcentaje y provincia
            $table->dropColumn(['localidad', 'provincia', 'comision_porcentaje']);

            // Agregar localidad_id, com_origen y com_destino
            $table->foreignId('localidad_id')->nullable()->after('nombre')->constrained('localidades');
            $table->decimal('com_origen', 8, 2)->default(0)->after('email');
            $table->decimal('com_destino', 8, 2)->default(0)->after('com_origen');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('agencies', function (Blueprint $table) {
            $table->dropForeign(['localidad_id']);
            $table->dropColumn(['localidad_id', 'com_origen', 'com_destino']);

            $table->string('localidad')->nullable();
            $table->string('provincia')->nullable();
            $table->decimal('comision_porcentaje', 8, 2)->default(0);
        });
    }
};