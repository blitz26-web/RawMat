<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Product extends Model
{
    use HasFactory;

    protected $fillable = ['code', 'name', 'unit', 'description'];

    public function bomItems(): HasMany
    {
        return $this->hasMany(BillOfMaterial::class);
    }

    public function materials(): BelongsToMany
    {
        return $this->belongsToMany(Material::class, 'bill_of_materials')
                    ->withPivot('quantity_required')
                    ->withTimestamps();
    }
}