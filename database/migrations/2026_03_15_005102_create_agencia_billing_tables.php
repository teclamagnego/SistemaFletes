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
        Schema::create('agencia_facturas', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('agency_id');
            $table->string('nro_factura')->nullable();
            $table->date('fecha');
            $table->decimal('total', 15, 2);
            $table->text('observacion')->nullable();
            $table->timestamps();
        });

        Schema::create('agencia_recibos', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('agency_id');
            $table->date('fecha');
            $table->decimal('monto', 15, 2);
            $table->string('nro_recibo')->nullable();
            $table->unsignedBigInteger('forma_pago_id');
            $table->text('observaciones')->nullable();
            $table->timestamps();
        });

        Schema::table('shipments', function (Blueprint $table) {
            $table->unsignedBigInteger('agencia_f_origen_id')->default(0)->after('factura_id');
            $table->unsignedBigInteger('agencia_f_destino_id')->default(0)->after('agencia_f_origen_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('agencia_facturas');
        Schema::dropIfExists('agencia_recibos');
        Schema::table('shipments', function (Blueprint $table) {
            $table->dropColumn(['agencia_f_origen_id', 'agencia_f_destino_id']);
        });
    }
};