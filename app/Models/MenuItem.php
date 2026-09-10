<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class MenuItem extends Model
{
    protected $fillable = [
        'category_id', 'name', 'slug', 'description', 'price', 'old_price', 'image_path',
        'prep_time_minutes', 'is_vegan', 'has_spice_level', 'is_signature', 'is_bestseller',
        'is_available', 'rating',
    ];

    protected function casts(): array
    {
        return [
            'price' => 'decimal:2',
            'old_price' => 'decimal:2',
            'prep_time_minutes' => 'integer',
            'is_vegan' => 'boolean',
            'has_spice_level' => 'boolean',
            'is_signature' => 'boolean',
            'is_bestseller' => 'boolean',
            'is_available' => 'boolean',
            'rating' => 'decimal:1',
        ];
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public function variations(): HasMany
    {
        return $this->hasMany(MenuItemVariation::class)->orderBy('sort_order');
    }

    public function toppings(): HasMany
    {
        return $this->hasMany(MenuItemTopping::class)->orderBy('sort_order');
    }

    public function sauces(): HasMany
    {
        return $this->hasMany(MenuItemSauce::class)->orderBy('sort_order');
    }
}
