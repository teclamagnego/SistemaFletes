<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('shipment_statuses', function (Blueprint $table) {
            $table->id();
            $table->string('name')->unique();
            $table->string('color')->nullable();
            $table->timestamps();
        });

        // Seed default statuses
        DB::table('shipment_statuses')->insert([
            ['name' => 'Admitted', 'color' => 'secondary'],
            ['name' => 'In Office', 'color' => 'info'],
            ['name' => 'In Transit', 'color' => 'warning'],
            ['name' => 'In Destination', 'color' => 'primary'],
            ['name' => 'Delivered', 'color' => 'success'],
            ['name' => 'Cancelled', 'color' => 'danger'],
        ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('shipment_statuses');
    }
};