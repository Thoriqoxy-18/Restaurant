<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class MenuItem extends Model
{
    protected $fillable = [
        'category_id', 'name', 'slug', 'description', 'price', 'image_path',
        'has_spice_level', 'is_signature', 'is_bestseller', 'is_available', 'rating',
    ];

    protected function casts(): array
    {
        return [
            'price' => 'decimal:2', 'has_spice_level' => 'boolean',
            'is_signature' => 'boolean', 'is_bestseller' => 'boolean',
            'is_available' => 'boolean', 'rating' => 'decimal:1',
        ];
    }

    public function category(): BelongsTo { return $this->belongsTo(Category::class); }
    public function orderItems(): HasMany { return $this->hasMany(OrderItem::class); }
    public function options(): HasMany { return $this->hasMany(MenuItemOption::class)->orderBy('sort_order'); }
}
