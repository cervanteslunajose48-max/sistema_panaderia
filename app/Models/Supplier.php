<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Supplier extends Model
{
    protected $fillable = [
        'name',
        'contact_name',
        'document_number',
        'phone',
        'email',
        'address',
        'status',
    ];
    public function supplies()
    {
        return $this->hasMany(Supply::class);
    }
}
