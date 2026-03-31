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
        Schema::table('cliente_facturas', function (Blueprint $table) {
            $table->unsignedBigInteger('forma_pago_id')->nullable()->after('cliente_id');
            $table->decimal('falta_imputar', 15, 2)->default(0)->after('total');
            
            $table->foreign('forma_pago_id')->references('id')->on('forma_pagos');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('cliente_facturas', function (Blueprint $table) {
            $table->dropForeign(['forma_pago_id']);
            $table->dropColumn(['forma_pago_id', 'falta_imputar']);
        });
    }
};