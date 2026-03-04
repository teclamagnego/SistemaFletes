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
        Schema::table('shipments', function (Blueprint $table) {
            $table->date('fecha')->nullable()->after('status');
            $table->string('direccion_entrega')->nullable()->after('fecha');
            $table->dropColumn('payment_mode');
            $table->foreignId('forma_pago_id')->nullable()->after('status')->constrained('forma_pagos');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('shipments', function (Blueprint $table) {
            $table->dropForeign(['forma_pago_id']);
            $table->dropColumn(['forma_pago_id', 'fecha', 'direccion_entrega']);
            $table->string('payment_mode')->default('PP');
        });
    }
};