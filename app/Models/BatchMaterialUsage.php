<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class BatchMaterialUsage extends Model
{
    use HasFactory;

    protected $fillable = [
        'production_batch_id',
        'material_id',
        'qty_planned',
        'qty_used',
    ];

    protected $casts = [
        'qty_planned' => 'decimal:2',
        'qty_used'    => 'decimal:2',
    ];

    // --- RELASI ELOQUENT ---

    public function productionBatch(): BelongsTo
    {
        return $this->belongsTo(ProductionBatch::class);
    }

    public function material(): BelongsTo
    {
        return $this->belongsTo(Material::class);
    }
}