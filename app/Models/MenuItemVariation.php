<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MenuItemVariation extends Model
{
    protected $fillable = ['menu_item_id', 'name', 'extra_price', 'sort_order'];

    protected function casts(): array
    {
        return ['extra_price' => 'decimal:2', 'sort_order' => 'integer'];
    }

    public function menuItem(): BelongsTo
    {
        return $this->belongsTo(MenuItem::class);
    }
}
