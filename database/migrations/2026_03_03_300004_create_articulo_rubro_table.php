<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('articulo_rubro', function (Blueprint $table) {
            $table->id();
            $table->foreignId('articulo_id')->constrained('articulos')->onDelete('cascade');
            $table->foreignId('rubro_id')->constrained('rubros')->onDelete('cascade');
            $table->timestamps();
            $table->unique(['articulo_id', 'rubro_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('articulo_rubro');
    }
};