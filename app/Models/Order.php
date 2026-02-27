<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Order extends Model
{
    protected $fillable = [
        'user_id', 
        'total_amount', 
        'status', 
        'payment_method', 
        'shipping_address', 
        'phone_number',
        'idempotency_key'
    ];

    // Ek order mein bohat saari items ho sakti hain
    public function items(): HasMany
    {
        return $this->hasMany(OrderItem::class);
    }

    // Order kis user ka hai
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}