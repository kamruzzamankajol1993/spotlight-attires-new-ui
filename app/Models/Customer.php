<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
class Customer extends Model
{
     use HasFactory;
    protected $fillable = [
        'name',
        'image',
        'status',
        'phone',
        'address',
        'email',
        'admin_id',
        'nid_front_image',
        'nid_back_image',
    ];

    public function user()
{
    return $this->hasOne(User::class); // Or User::class if you prefer
}

public function generalTickets(): HasMany
{
    return $this->hasMany(GeneralTicket::class, 'customer_id');
}
}
