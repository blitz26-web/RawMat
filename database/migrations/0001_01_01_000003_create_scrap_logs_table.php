<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('scrap_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('production_batch_id')->constrained('production_batches')->cascadeOnDelete();
            $table->foreignId('material_id')->constrained('materials')->restrictOnDelete();
            $table->decimal('scrap_qty', 12, 2);
            $table->string('scrap_reason'); // Contoh: Cacat Potong, Human Error, Mesin Overheat
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('scrap_logs');
    }
};