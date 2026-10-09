<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class ProductionBatch extends Model
{
    use HasFactory;

    protected $fillable = [
        'batch_number',
        'product_name',
        'target_qty',
        'actual_qty',
        'status',
        'start_date',
        'end_date',
        'yield_rate',
    ];

    protected $casts = [
        'target_qty' => 'decimal:2',
        'actual_qty' => 'decimal:2',
        'yield_rate' => 'decimal:2',
        'start_date' => 'datetime',
        'end_date'   => 'datetime',
    ];

    // --- RELASI ELOQUENT ---

    public function materialUsages(): HasMany
    {
        return $this->hasMany(BatchMaterialUsage::class);
    }

    public function materials(): BelongsToMany
    {
        return $this->belongsToMany(Material::class, 'batch_material_usages')
                    ->withPivot(['qty_planned', 'qty_used'])
                    ->withTimestamps();
    }

    public function scrapLogs(): HasMany
    {
        return $this->hasMany(ScrapLog::class);
    }

    // --- ACCESSOR & BOOTSTRAP HELPER METHODS ---

    /**
     * Mendapatkan class badge Bootstrap 5 berdasarkan status batch.
     */
    public function getStatusBadgeClassAttribute(): string
    {
        return match ($this->status) {
            'draft'       => 'bg-secondary',
            'in_progress' => 'bg-primary',
            'completed'   => 'bg-success',
            'cancelled'   => 'bg-danger',
            default       => 'bg-dark',
        };
    }

    /**
     * Class warna Bootstrap 5 untuk indikator Yield Rate.
     */
    public function getYieldBadgeClassAttribute(): string
    {
        if (is_null($this->yield_rate)) {
            return 'bg-secondary';
        }

        if ($this->yield_rate >= 95.00) {
            return 'bg-success';
        }

        if ($this->yield_rate >= 85.00) {
            return 'bg-warning text-dark';
        }

        return 'bg-danger';
    }
}