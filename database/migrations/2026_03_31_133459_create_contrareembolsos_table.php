<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('contrareembolsos', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('guia_id');
            $table->unsignedBigInteger('cliente_id');
            $table->decimal('monto', 12, 2);
            $table->date('fecha_cobrado')->nullable();
            $table->date('fecha_rendido')->nullable();
            $table->timestamps();

            $table->foreign('guia_id')->references('id')->on('shipments')->onDelete('cascade');
            $table->foreign('cliente_id')->references('id')->on('clientes')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('contrareembolsos');
    }
};
