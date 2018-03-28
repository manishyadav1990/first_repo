<?php

namespace App\Models\Transaction;

use Illuminate\Database\Eloquent\Model;

class TransactionDetails extends Model
{
    protected $table = 'transaction_details';
    protected $fillable = ['id', 'patient_id', 'doctor_id', 'visit_id', 'transactionsourceid',
                           'source_id', 'is_paid', 'transactionchargesid', 'created_at', 'updated_at'];
}
