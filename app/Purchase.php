<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Purchase extends Model
{
    protected $fillable = [
        'purchase_code',
        'supplier_id',
        'product_id',
        'quantity',
        'purchase_price',
        // tambahkan kolom lain sesuai kebutuhan
    ];

    // Relasi dengan Supplier
    public function supplier()
    {
        return $this->belongsTo(Supplier::class);
    }

    // Relasi dengan Product
    public function product()
    {
        return $this->belongsTo(Product::class);
    }
}