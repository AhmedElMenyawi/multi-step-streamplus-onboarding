<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Transaction extends Model
{
    protected $fillable = [
        'user_id',
        'address_id',
        'card_id',
        'amount',
        'currency',
        'transaction_status'
    ];

    public function user()
    {
        return $this->belongsTo(UserDetail::class);
    }

    public function address()
    {
        return $this->belongsTo(UserAddress::class);
    }

    // Can be null
    public function card()
    {
        return $this->belongsTo(UserCard::class);
    }
}
