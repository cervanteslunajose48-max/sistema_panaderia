<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Recipe extends Model
{
    use HasFactory;

    protected $fillable = [
        'product_variant_id',
        'name',
        'description',
        'image',
        'instructions',
        'prep_time',
        'yield_quantity',
        'yield_unit',
    ];

    public function productVariant(): BelongsTo
    {
        return $this->belongsTo(ProductVariant::class);
    }

    public function ingredients(): HasMany
    {
        return $this->hasMany(RecipeIngredient::class);
    }

    /**
     * Get the total cost of all ingredients in this recipe
     */
    public function getTotalCostAttribute(): float
    {
        return $this->ingredients->sum(function ($ingredient) {
            return $ingredient->quantity * ($ingredient->supply->cost ?? 0);
        });
    }

    /**
     * Get the cost per unit of finished product
     */
    public function getUnitCostAttribute(): float
    {
        if ($this->yield_quantity <= 0) {
            return 0;
        }
        return $this->total_cost / $this->yield_quantity;
    }

    /**
     * Check if there's enough stock to produce this recipe
     */
    public function hasEnoughStock(int $quantity = 1): bool
    {
        foreach ($this->ingredients as $ingredient) {
            $requiredQuantity = $ingredient->quantity * $quantity;
            $availableStock = $ingredient->supply->stocks->sum('quantity');

            if ($availableStock < $requiredQuantity) {
                return false;
            }
        }
        return true;
    }

    /**
     * Scope for searching recipes
     */
    public function scopeSearch($query, $search)
    {
        return $query->where(function ($q) use ($search) {
            $q->where('name', 'like', "%{$search}%")
                ->orWhereHas('productVariant.product', function ($q) use ($search) {
                    $q->where('name', 'like', "%{$search}%");
                });
        });
    }
}
