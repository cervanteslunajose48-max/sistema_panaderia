<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProductTransformation extends Model
{
    use HasFactory;

    protected $fillable = [
        'source_variant_id',
        'target_variant_id',
        'source_quantity',
        'target_quantity',
        'user_id',
        'notes',
    ];

    public function sourceVariant()
    {
        return $this->belongsTo(ProductVariant::class, 'source_variant_id');
    }

    public function targetVariant()
    {
        return $this->belongsTo(ProductVariant::class, 'target_variant_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
