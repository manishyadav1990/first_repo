<?php

namespace App\Models\Transaction;

use Illuminate\Database\Eloquent\Model;

class TransactionLog extends Model
{
    protected $table = 'transaction_log_monthly';
    protected $fillable = ['id', 'pertransactioncharge', 'transactionmonth', 'transactionyear', 'transactionsourceid', 'amount',
                          'source_id', 'no_of_transaction', 'ispaid', 'created_at', 'updated_at'];
}
