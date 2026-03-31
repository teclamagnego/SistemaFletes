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
        Schema::table('articulos', function (Blueprint $table) {
            $table->dropColumn('stock');
            $table->decimal('com_origen', 8, 2)->default(0)->after('precio');
            $table->decimal('com_destino', 8, 2)->default(0)->after('com_origen');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('articulos', function (Blueprint $table) {
            $table->dropColumn(['com_origen', 'com_destino']);
            $table->integer('stock')->default(0)->after('precio');
        });
    }
};