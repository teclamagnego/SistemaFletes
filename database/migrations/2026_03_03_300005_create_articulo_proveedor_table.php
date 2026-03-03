<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('articulo_proveedor', function (Blueprint $table) {
            $table->id();
            $table->foreignId('articulo_id')->constrained('articulos')->onDelete('cascade');
            $table->foreignId('proveedor_id')->constrained('proveedores')->onDelete('cascade');
            $table->decimal('precio_compra', 12, 2)->nullable();
            $table->timestamps();
            $table->unique(['articulo_id', 'proveedor_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('articulo_proveedor');
    }
};