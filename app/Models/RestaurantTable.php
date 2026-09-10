<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

class RestaurantTable extends Model
{
    protected $fillable = ['code', 'qr_token', 'capacity', 'status', 'table_number', 'name'];

    protected function casts(): array
    {
        return ['capacity' => 'integer', 'table_number' => 'integer'];
    }

    public function getLabelAttribute(): string
    {
        if ($this->name) {
            return $this->name;
        }
        if ($this->table_number !== null) {
            return 'Meja ' . str_pad((string) $this->table_number, 2, '0', STR_PAD_LEFT);
        }
        return $this->code;
    }

    protected static function booted(): void
    {
        static::creating(fn ($table) => $table->qr_token ??= Str::random(64));
    }

    public function orders(): HasMany
    {
        return $this->hasMany(Order::class);
    }

    public function customerSessions(): HasMany
    {
        return $this->hasMany(CustomerSession::class);
    }
}
