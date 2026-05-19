<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SpecialOrderItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'special_order_id',
        'product_variant_id',
        'quantity',
        'unit_price',
        'specifications',
    ];

    public function specialOrder()
    {
        return $this->belongsTo(SpecialOrder::class);
    }

    public function productVariant()
    {
        return $this->belongsTo(ProductVariant::class);
    }

    public function getSubtotalAttribute()
    {
        return $this->quantity * $this->unit_price;
    }
}
