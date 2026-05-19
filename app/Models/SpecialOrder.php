<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SpecialOrder extends Model
{
    use HasFactory;

    protected $fillable = [
        'customer_id',
        'pickup_date',
        'status',
        'total_amount',
        'deposit_amount',
        'notes',
    ];

    protected $casts = [
        'pickup_date' => 'datetime',
        'total_amount' => 'decimal:2',
        'deposit_amount' => 'decimal:2',
    ];

    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }

    public function items()
    {
        return $this->hasMany(SpecialOrderItem::class);
    }

    public function getBalanceAttribute()
    {
        return $this->total_amount - $this->deposit_amount;
    }
}
