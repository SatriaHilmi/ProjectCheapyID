<?php

namespace App;

use App\User;

use App\Customer;
use App\Models\DetailTransaction;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Transaction extends Model
{
    use HasFactory;

    protected $primaryKey = 'transaction_code';
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'transaction_code',
        'user_id',
        'total',
        'payment_method',
        'status',
    ];

    public function detailTransactions()
    {
        return $this->hasMany(DetailTransaction::class, 'transaction_id', 'transaction_code');
    }
    public function user()
    {
        return $this->belongsTo(User::class);
    }
    public static function generateTransactionCode()
    {
        $lastTransaction = Transaction::orderBy('transaction_code', 'desc')->first();

        if ($lastTransaction) {
            $lastCode = (int) substr($lastTransaction->transaction_code, 2);
            $newCode = 'TR' . str_pad($lastCode + 1, 6, '0', STR_PAD_LEFT);
        } else {
            $newCode = 'TR000001';
        }

        return $newCode;
    }
}
