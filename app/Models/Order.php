<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Order extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'address_id',
        'status',
        'payment_method',
        'total_price',
    ];

    protected $casts = [
        'status' => 'string',
        'total_price' => 'decimal:2',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function address()
    {
        return $this->belongsTo(Endereco::class, 'address_id');
    }

    public function items()
    {
        return $this->hasMany(OrderItem::class);
    }
}