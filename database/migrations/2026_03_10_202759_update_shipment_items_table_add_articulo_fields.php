<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('shipment_items', function (Blueprint $table) {
            $table->foreignId('articulo_id')->nullable()->after('shipment_id')->constrained('articulos');
            $table->decimal('precio_unitario', 12, 2)->default(0)->after('cantidad');
            $table->decimal('iva', 12, 2)->default(0)->after('precio_unitario');
            $table->decimal('bonificacion', 12, 2)->default(0)->after('iva');
            $table->decimal('total', 12, 2)->default(0)->after('bonificacion');
            $table->string('descripcion')->nullable()->change();
        });
    }

    public function down(): void
    {
        Schema::table('shipment_items', function (Blueprint $table) {
            $table->dropForeign(['articulo_id']);
            $table->dropColumn(['articulo_id', 'precio_unitario', 'iva', 'bonificacion', 'total']);
            $table->string('descripcion')->change();
        });
    }
};