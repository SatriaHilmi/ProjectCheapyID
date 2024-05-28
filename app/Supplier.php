<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Supplier extends Model
{
    protected $table = 'suppliers';

    protected $fillable = [
        'name', 'address', 'phone'
    ];

    // Relasi dengan model Purchase
    public function purchases()
    {
        return $this->hasMany(Purchase::class);
    }
}