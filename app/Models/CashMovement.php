<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CashMovement extends Model
{
    protected $fillable = [
        'cash_register_id',
        'type', // 'in', 'out'
        'amount',
        'description',
        'reference_id' // Optional, e.g., order_id
    ];

    public function register()
    {
        return $this->belongsTo(CashRegister::class);
    }
}
