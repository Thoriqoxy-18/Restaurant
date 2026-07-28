<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class OrderItem extends Model
{
    protected $fillable = ['order_id', 'menu_item_id', 'quantity', 'price', 'spice_level', 'options', 'notes'];

    protected function casts(): array
    {
        return [
            'quantity' => 'integer', 'price' => 'decimal:2',
            'spice_level' => 'integer', 'options' => 'json',
        ];
    }

    public function order(): BelongsTo { return $this->belongsTo(Order::class); }
    public function menuItem(): BelongsTo { return $this->belongsTo(MenuItem::class); }
}
