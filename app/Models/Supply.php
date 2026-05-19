<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Supply extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'sku',
        'base_unit',
        'min_stock',
        'cost',
        'supplier_id',
        'status',
    ];

    public function stocks(): HasMany
    {
        return $this->hasMany(SupplyStock::class);
    }

    public function supplier()
    {
        return $this->belongsTo(\App\Models\Supplier::class);
    }

    // Helper to get total stock across all warehouses/batches
    public function getCurrentStockAttribute()
    {
        return $this->stocks()->sum('quantity');
    }
}
