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
        // 1. Modificar Shipments
        Schema::table('shipments', function (Blueprint $table) {
            $table->foreignId('status_id')->nullable()->after('tracking_number')->constrained('shipment_statuses');
        });

        // Migrar datos de shipments
        $statuses = DB::table('shipment_statuses')->get();
        foreach ($statuses as $status) {
            DB::table('shipments')
                ->where('status', $status->name)
                ->update(['status_id' => $status->id]);
        }

        Schema::table('shipments', function (Blueprint $table) {
            $table->dropColumn('status');
        });

        // 2. Modificar Shipment Logs
        Schema::table('shipment_logs', function (Blueprint $table) {
            $table->foreignId('status_from_id')->nullable()->after('user_id')->constrained('shipment_statuses');
            $table->foreignId('status_to_id')->nullable()->after('status_from_id')->constrained('shipment_statuses');
        });

        // Migrar datos de logs
        foreach ($statuses as $status) {
            DB::table('shipment_logs')
                ->where('status_from', $status->name)
                ->update(['status_from_id' => $status->id]);

            DB::table('shipment_logs')
                ->where('status_to', $status->name)
                ->update(['status_to_id' => $status->id]);
        }

        Schema::table('shipment_logs', function (Blueprint $table) {
            $table->dropColumn(['status_from', 'status_to']);
        });
    }

    public function down(): void
    {
        Schema::table('shipment_logs', function (Blueprint $table) {
            $table->string('status_from')->nullable()->after('user_id');
            $table->string('status_to')->nullable()->after('status_from');
        });

        $statuses = DB::table('shipment_statuses')->get();
        foreach ($statuses as $status) {
            DB::table('shipment_logs')->where('status_from_id', $status->id)->update(['status_from' => $status->name]);
            DB::table('shipment_logs')->where('status_to_id', $status->id)->update(['status_to' => $status->name]);
        }

        Schema::table('shipment_logs', function (Blueprint $table) {
            $table->dropForeign(['status_from_id']);
            $table->dropForeign(['status_to_id']);
            $table->dropColumn(['status_from_id', 'status_to_id']);
        });

        Schema::table('shipments', function (Blueprint $table) {
            $table->string('status')->nullable()->after('payment_mode');
        });

        foreach ($statuses as $status) {
            DB::table('shipments')->where('status_id', $status->id)->update(['status' => $status->name]);
        }

        Schema::table('shipments', function (Blueprint $table) {
            $table->dropForeign(['status_id']);
            $table->dropColumn('status_id');
        });
    }
};