<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UserDetail extends Model
{
    protected $fillable = [
        'name',
        'email',
        'phone',
        'subscription_type'
    ];

    public function addresses()
    {
        return $this->hasMany(UserAddress::class, 'user_id');
    }

    public function cards()
    {
        return $this->hasMany(UserCard::class, 'user_id');
    }

    public function transactions()
    {
        return $this->hasMany(Transaction::class, 'user_id');
    }
}
