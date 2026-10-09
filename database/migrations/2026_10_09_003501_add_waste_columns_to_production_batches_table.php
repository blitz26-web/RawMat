<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddWasteColumnsToProductionBatchesTable extends Migration
{
    public function up(): void
    {
        Schema::table('production_batches', function (Blueprint $table) {
            $table->decimal('normal_waste_qty', 10, 2)->default(0)->after('actual_qty');
            $table->decimal('abnormal_waste_qty', 10, 2)->default(0)->after('normal_waste_qty');
            $table->decimal('waste_cost', 15, 2)->default(0)->after('abnormal_waste_qty');
            $table->text('waste_notes')->nullable()->after('waste_cost');
        });
    }

    public function down(): void
    {
        Schema::table('production_batches', function (Blueprint $table) {
            $table->dropColumn(['normal_waste_qty', 'abnormal_waste_qty', 'waste_cost', 'waste_notes']);
        });
    }
}