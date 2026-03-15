<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('sucursales', function (Blueprint $table) {
            $table->id(); // bigint UNSIGNED NOT NULL AUTO_INCREMENT
            $table->string('nombre', 191);
            $table->text('listasprecios')->nullable();

            // Assuming empresa_id is related to empresas.id which is an unsigned int (increments)
            // If the SQL definition specified `int NOT NULL DEFAULT '1'`, we'll use unsignedInteger to match
            $table->unsignedInteger('empresa_id')->default(1);

            $table->string('domicilio', 191);
            $table->string('telefono', 191);
            $table->string('localidad', 191);
            $table->string('puntoventa', 191);
            $table->date('fecha_inicio_actividades');
            $table->integer('produccion')->default(0);
            $table->string('alias_cbu', 200)->nullable();
            $table->integer('tope_lineas_factura')->default(10000);
            $table->integer('redondeo')->default(0);
            $table->integer('decimales')->default(2);
            $table->string('depositos', 191)->nullable();
            $table->boolean('edita_remitos')->default(false);
            $table->integer('max_botones_articulos')->default(0);
            $table->timestamps();
            $table->softDeletes(); // For deleted_at
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('sucursales');
    }
};