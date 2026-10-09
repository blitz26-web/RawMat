<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('production_batches', function (Blueprint $table) {
            $table->id();
            $table->string('batch_number')->unique();
            $table->string('product_name');
            $table->decimal('target_qty', 12, 2);
            $table->decimal('actual_qty', 12, 2)->nullable();
            $table->enum('status', ['draft', 'in_progress', 'completed', 'cancelled'])->default('draft');
            $table->dateTime('start_date')->nullable();
            $table->dateTime('end_date')->nullable();
            $table->decimal('yield_rate', 5, 2)->nullable(); // Menyimpan persentase (contoh: 98.50%)
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('production_batches');
    }
};