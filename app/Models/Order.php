<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'address_id',
        'shipping_address_snapshot',
        'status',
        'payment_method',
        'notes',
        'subtotal',
        'shipping_cost',
        'discount_amount',
        'total_amount',
        'coupon_code',
    ];

    protected $casts = [
        'shipping_address_snapshot' => 'array',
        'subtotal' => 'decimal:2',
        'discount_amount' => 'decimal:2',
        'total_amount' => 'decimal:2',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function items()
    {
        return $this->hasMany(OrderItem::class);
    }

    public function address()
    {
        return $this->belongsTo(Address::class); // Optional relation if address still exists
    }
}
