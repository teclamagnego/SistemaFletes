<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Limpiamos tablas dependientes para evitar errores de FK
        Schema::disableForeignKeyConstraints();
        DB::table('shipment_logs')->truncate();
        DB::table('shipment_items')->truncate();
        DB::table('shipments')->truncate();
        DB::table('clientes')->truncate();
        Schema::enableForeignKeyConstraints();

        $colsToDrop = ['nombre', 'apellido', 'tipo_documento', 'numero_documento', 'cuit', 'provincia', 'localidad'];
        foreach ($colsToDrop as $col) {
            if (Schema::hasColumn('clientes', $col)) {
                Schema::table('clientes', function (Blueprint $table) use ($col) {
                    $table->dropColumn($col);
                });
            }
        }

        Schema::table('clientes', function (Blueprint $table) {
            if (!Schema::hasColumn('clientes', 'nombre_fantasia')) {
                $table->string('nombre_fantasia')->after('id');
            }
            if (!Schema::hasColumn('clientes', 'tipodoc_id')) {
                $table->foreignId('tipodoc_id')->after('nombre_fantasia')->constrained('tipos_doc');
            }
            if (!Schema::hasColumn('clientes', 'documento_nro')) {
                $table->string('documento_nro')->after('tipodoc_id');
            }
            
            if (!Schema::hasColumn('clientes', 'localidad_id')) {
                $table->foreignId('localidad_id')->after('direccion')->constrained('localidades');
            }
            if (!Schema::hasColumn('clientes', 'tipocuenta_id')) {
                $table->foreignId('tipocuenta_id')->after('localidad_id')->constrained('tipos_cuenta');
            }
            if (!Schema::hasColumn('clientes', 'tipoiva_id')) {
                $table->foreignId('tipoiva_id')->after('tipocuenta_id')->constrained('tipos_iva');
            }
            
            if (!Schema::hasColumn('clientes', 'agenciaorigen_id')) {
                $table->foreignId('agenciaorigen_id')->after('tipoiva_id')->constrained('agencies');
            }
            if (!Schema::hasColumn('clientes', 'agenciadestino_id')) {
                $table->foreignId('agenciadestino_id')->after('agenciaorigen_id')->constrained('agencies');
            }
            
            if (!Schema::hasColumn('clientes', 'observacion')) {
                $table->text('observacion')->nullable()->after('agenciadestino_id');
            }
        });
    }

    public function down(): void
    {
        Schema::table('clientes', function (Blueprint $table) {
            $table->string('nombre');
            $table->string('apellido');
            $table->string('tipo_documento');
            $table->string('numero_documento');
            $table->string('cuit')->nullable();
            $table->string('provincia');
            $table->string('localidad');

            $table->dropForeign(['tipodoc_id']);
            $table->dropForeign(['localidad_id']);
            $table->dropForeign(['tipocuenta_id']);
            $table->dropForeign(['tipoiva_id']);
            $table->dropForeign(['agenciaorigen_id']);
            $table->dropForeign(['agenciadestino_id']);

            $table->dropColumn(['nombre_fantasia', 'tipodoc_id', 'documento_nro', 'localidad_id', 'tipocuenta_id', 'tipoiva_id', 'agenciaorigen_id', 'agenciadestino_id', 'observacion']);
        });
    }
};