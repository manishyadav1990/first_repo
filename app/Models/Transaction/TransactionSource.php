<?php

namespace App\Models\Transaction;

use Illuminate\Database\Eloquent\Model;

class TransactionSource extends Model
{
    protected $table = 'transaction_source';
    protected $fillable = ['id', 'transactionfor', 'created_at', 'updated_at'];

}
