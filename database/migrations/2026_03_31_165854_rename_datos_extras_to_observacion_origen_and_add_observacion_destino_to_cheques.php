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
        Schema::table('cheques', function (Blueprint $table) {
            if (Schema::hasColumn('cheques', 'datos_extras') && !Schema::hasColumn('cheques', 'observacion_origen')) {
                $table->renameColumn('datos_extras', 'observacion_origen');
            }
        });

        Schema::table('cheques', function (Blueprint $table) {
            if (!Schema::hasColumn('cheques', 'observacion_destino')) {
                $table->text('observacion_destino')->nullable()->after('observacion_origen');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('cheques', function (Blueprint $table) {
            if (Schema::hasColumn('cheques', 'observacion_origen') && !Schema::hasColumn('cheques', 'datos_extras')) {
                $table->renameColumn('observacion_origen', 'datos_extras');
            }
            if (Schema::hasColumn('cheques', 'observacion_destino')) {
                $table->dropColumn('observacion_destino');
            }
        });
    }
};
