<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('empresas', function (Blueprint $table) {
            $table->increments('id'); // int UNSIGNED NOT NULL AUTO_INCREMENT
            $table->string('nombre_fantasia', 191);
            $table->string('razon_social', 191);
            $table->string('cuit', 191);
            $table->tinyInteger('tipoiva_id');
            $table->boolean('activo')->default(true);
            $table->string('logo', 191)->nullable();
            $table->boolean('esagenteretencioniva')->default(false);
            $table->timestamps(); // We add timestamps just in case, best practice
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('empresas');
    }
};