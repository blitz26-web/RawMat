<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Material extends Model
{
    use HasFactory;

    protected $fillable = [
        'code',
        'name',
        'unit',
        'current_stock',
        'safety_stock',
        'description',
    ];

    protected $casts = [
        'current_stock' => 'decimal:2',
        'safety_stock'  => 'decimal:2',
    ];

    // --- RELASI ELOQUENT ---

    public function batchUsages(): HasMany
    {
        return $this->hasMany(BatchMaterialUsage::class);
    }

    public function productionBatches(): BelongsToMany
    {
        return $this->belongsToMany(ProductionBatch::class, 'batch_material_usages')
                    ->withPivot(['qty_planned', 'qty_used'])
                    ->withTimestamps();
    }

    public function scrapLogs(): HasMany
    {
        return $this->hasMany(ScrapLog::class);
    }

    // --- ACCESSOR & BOOTSTRAP HELPER METHODS ---

    /**
     * Cek apakah stok di bawah atau sama dengan safety stock.
     */
    public function getIsLowStockAttribute(): bool
    {
        return $this->current_stock <= $this->safety_stock;
    }

    /**
     * Mendapatkan class badge Bootstrap 5 berdasarkan kondisi stok.
     */
    public function getStockBadgeClassAttribute(): string
    {
        if ($this->current_stock <= 0) {
            return 'bg-danger';
        }
        
        if ($this->is_low_stock) {
            return 'bg-warning text-dark';
        }

        return 'bg-success';
    }

    /**
     * Label status stok untuk komponen UI Bootstrap.
     */
    public function getStockStatusLabelAttribute(): string
    {
        if ($this->current_stock <= 0) {
            return 'Out of Stock';
        }

        if ($this->is_low_stock) {
            return 'Low Stock Alert';
        }

        return 'Safe';
    }
}