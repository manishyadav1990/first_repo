<?php

namespace App\Models\Transaction;

use Illuminate\Database\Eloquent\Model;

class TransactionCharges extends Model
{
    protected $table = 'transaction_charges';
    protected $fillable = ['id','name','description', 'transactioncharge', 'transactionsourceid', 'transactionno_rangefrom',
        'transactionno_rangeto', 'min_monthly_amt', 'country', 'country_code', 'currency', 'created_at', 'updated_at'];
}
