<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MenuItemOptionChoice extends Model
{
    protected $fillable = ['menu_item_option_id', 'name', 'price_modifier', 'sort_order'];

    protected function casts(): array
    {
        return ['price_modifier' => 'decimal:2', 'sort_order' => 'integer'];
    }

    public function option(): BelongsTo { return $this->belongsTo(MenuItemOption::class, 'menu_item_option_id'); }
}
