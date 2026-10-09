<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ScrapLog extends Model
{
    use HasFactory;

    protected $fillable = [
        'production_batch_id',
        'material_id',
        'scrap_qty',
        'scrap_reason',
    ];

    protected $casts = [
        'scrap_qty' => 'decimal:2',
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