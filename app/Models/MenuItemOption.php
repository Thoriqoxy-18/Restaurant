<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class MenuItemOption extends Model
{
    protected $fillable = ['menu_item_id', 'group_name', 'type', 'sort_order', 'is_required'];

    protected function casts(): array
    {
        return ['is_required' => 'boolean', 'sort_order' => 'integer'];
    }

    public function menuItem(): BelongsTo { return $this->belongsTo(MenuItem::class); }
    public function choices(): HasMany { return $this->hasMany(MenuItemOptionChoice::class)->orderBy('sort_order'); }
}
