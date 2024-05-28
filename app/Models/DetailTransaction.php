<?php

namespace App\Models;

use App\Product;
use App\Transaction;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class DetailTransaction extends Model
{
    use HasFactory;

    protected $table = 'detail_transactions';

    protected $fillable = [
        'transaction_id',
        'product_id',
    ];

    public function transaction()
    {
        return $this->belongsTo(Transaction::class, 'transaction_id', 'transaction_code');
    }

    public function product()
    {
        return $this->belongsTo(Product::class);
    }
}
