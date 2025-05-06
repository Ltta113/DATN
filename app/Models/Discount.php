<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Discount extends Model
{
    /** @use HasFactory<\Database\Factories\DiscountFactory> */
    use HasFactory;

    protected $fillable = [
        'name',
        'description',
        'percent',
        'starts_at',
        'expires_at',
        'type',
        'value',
    ];
    protected $casts = [
        'starts_at' => 'datetime',
        'expires_at' => 'datetime',
    ];

    public function isActive(): bool
    {
        $now = now();
        return $this->starts_at <= $now && $this->expires_at >= $now;
    }

    public function isExpired(): bool
    {
        return $this->expires_at < now();
    }

    public function isFuture(): bool
    {
        return $this->starts_at > now();
    }

    public function isValid(): bool
    {
        $now = now();
        return $this->expires_at >= $now;
    }

    public function scopeValid($query)
    {
        $now = now();
        return $query->where('expires_at', '>=', $now);
    }

    public function scopeActive($query)
    {
        $now = now();
        return $query->where('starts_at', '<=', $now)
            ->where('expires_at', '>=', $now);
    }


    public function books()
    {
        return $this->hasMany(Book::class);
    }
}
