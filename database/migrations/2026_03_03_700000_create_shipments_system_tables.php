<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        // 1. Shipments (Guías)
        Schema::create('shipments', function (Blueprint $table) {
            $table->id();
            $table->string('tracking_number')->unique();

            $table->foreignId('sender_id')->constrained('clientes');
            $table->foreignId('receiver_id')->constrained('clientes');

            $table->foreignId('origin_agency_id')->constrained('agencies');
            $table->foreignId('destination_agency_id')->constrained('agencies');

            $table->foreignId('carrier_id')->nullable()->constrained('carriers');
            $table->foreignId('commission_agency_id')->constrained('agencies');

            $table->enum('payment_mode', ['PP', 'CC'])->default('PP'); // PP: Pagado en Origen, CC: Pago en Destino
            $table->string('status')->default('Admitted'); // Admitted, In Office, In Transit, Delivered, Cancelled

            $table->decimal('total_flete', 12, 2)->default(0);
            $table->decimal('comision_monto', 12, 2)->default(0);

            $table->text('notas')->nullable();
            $table->timestamps();
        });

        // 2. Shipment Items
        Schema::create('shipment_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('shipment_id')->constrained()->onDelete('cascade');
            $table->string('descripcion');
            $table->integer('cantidad')->default(1);
            $table->decimal('peso', 8, 2)->nullable();
            $table->string('dimensiones')->nullable(); // Ej: 30x30x30
            $table->string('tipo_mercancia')->nullable();
            $table->timestamps();
        });

        // 3. Shipment Logs (Trazabilidad)
        Schema::create('shipment_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('shipment_id')->constrained()->onDelete('cascade');
            $table->foreignId('user_id')->constrained();
            $table->string('status_from');
            $table->string('status_to');
            $table->text('notas')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('shipment_logs');
        Schema::dropIfExists('shipment_items');
        Schema::dropIfExists('shipments');
    }
};