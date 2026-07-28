<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

class CustomerSession extends Model
{
    protected $fillable = ['restaurant_table_id', 'session_token', 'started_at', 'ended_at'];

    protected function casts(): array
    {
        return ['started_at' => 'datetime', 'ended_at' => 'datetime'];
    }

    protected static function booted(): void
    {
        static::creating(fn ($s) => $s->session_token ??= Str::random(64));
    }

    public function restaurantTable(): BelongsTo { return $this->belongsTo(RestaurantTable::class); }
    public function orders(): HasMany { return $this->hasMany(Order::class); }

    public function isValid(): bool
    {
        return is_null($this->ended_at) && $this->started_at->diffInHours(now()) < 12;
    }
}
