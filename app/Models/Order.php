<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Order extends Model
{
    protected $fillable = [
        'order_number', 'restaurant_table_id', 'customer_session_id', 'kasir_id',
        'status', 'payment_status', 'payment_method', 'payment_provider',
        'paid_at', 'confirmed_by', 'completed_at', 'completed_by', 'subtotal', 'tax',
        'service_charge', 'total', 'notes', 'estimated_ready_at',
    ];

    protected function casts(): array
    {
        return [
            'subtotal' => 'decimal:2',
            'tax' => 'decimal:2',
            'service_charge' => 'decimal:2',
            'total' => 'decimal:2',
            'paid_at' => 'datetime',
            'completed_at' => 'datetime',
            'estimated_ready_at' => 'datetime',
        ];
    }

    public function restaurantTable(): BelongsTo
    {
        return $this->belongsTo(RestaurantTable::class);
    }

    public function customerSession(): BelongsTo
    {
        return $this->belongsTo(CustomerSession::class);
    }

    public function kasir(): BelongsTo
    {
        return $this->belongsTo(User::class, 'kasir_id');
    }

    public function orderItems(): HasMany
    {
        return $this->hasMany(OrderItem::class);
    }

    public function payments(): HasMany
    {
        return $this->hasMany(Payment::class);
    }
}
