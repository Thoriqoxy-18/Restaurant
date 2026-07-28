<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

class RestaurantTable extends Model
{
    protected $fillable = ['code', 'qr_token', 'capacity', 'status'];

    protected function casts(): array
    {
        return ['capacity' => 'integer'];
    }

    protected static function booted(): void
    {
        static::creating(fn ($table) => $table->qr_token ??= Str::random(64));
    }

    public function orders(): HasMany { return $this->hasMany(Order::class); }
    public function customerSessions(): HasMany { return $this->hasMany(CustomerSession::class); }
}
