<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class InventoryMovement extends Model
{
    use HasFactory;

    protected $fillable = [
        'inventory_id', // Optional, if linking to specific stock record
        'product_variant_id',
        'supply_id',
        'warehouse_id', // For supply movements tracking
        'type', // 'production_in', 'production_out', 'adjustment', 'purchase', 'sale'
        'quantity',
        'description',
        'user_id'
    ];

    public function productVariant()
    {
        return $this->belongsTo(ProductVariant::class);
    }

    public function supply()
    {
        return $this->belongsTo(Supply::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function warehouse()
    {
        return $this->belongsTo(Warehouse::class);
    }
}
