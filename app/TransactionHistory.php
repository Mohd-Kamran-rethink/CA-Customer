<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class TransactionHistory extends Model
{
    protected $fillable = [
        'exchange_id', 'client_id','amount','bonus','type','created_at','transaction_id'
    ];
}
