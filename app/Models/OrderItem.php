<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class OrderItem extends Model
{
    // In fields mein data save karne ki ijazat hai
    protected $fillable = ['order_id', 'product_id', 'quantity', 'price'];

    // Batata hai ke ye item kis product ka hai
    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    // ✅ Ye naya add kiya hai: Batata hai ke ye item kis order ka hissa hai
    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }
}