<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Crypt;

class UserCard extends Model
{
    //credit_card_number & cvv are encrypted
    protected $fillable = [
        'user_id',
        'credit_card_number',
        'expiration_month',
        'expiration_year',
        'cvv'
    ];

    public function user()
    {
        return $this->belongsTo(UserDetail::class, 'user_id');
    }
}
