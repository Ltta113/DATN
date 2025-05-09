<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;
use Illuminate\Database\Eloquent\SoftDeletes;

class Combo extends Model
{
    /** @use HasFactory<\Database\Factories\ComboFactory> */
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'name',
        'description',
        'price',
        'is_active',
        'image',
        'slug',
        'public_id'
    ];

    protected $casts = [
        'price' => 'decimal:2',
        'is_active' => 'boolean'
    ];

    public function books()
    {
        return $this->belongsToMany(Book::class, 'combo_book');
    }

    public function hasEnoughStock(int $quantity = 1): bool
    {
        foreach ($this->books as $book) {
            if ($book->stock < $quantity) {
                return false;
            }
        }
        return true;
    }

    public function updateBooksStock(int $quantity = 1)
    {
        foreach ($this->books as $book) {
            $book->decrement('stock', $quantity);
            $book->increment('sold', $quantity);

            if ($book->stock === 0) {
                $this->is_active = false;
                $this->save();
                $this->books()->update(['status' => 'sold_out']);
            }
        }
    }

    public function refundBooks(int $quantity = 1)
    {
        foreach ($this->books as $book) {
            $book->increment('stock', $quantity);
            $book->decrement('sold', $quantity);

            if ($book->status === 'sold_out' && $book->stock > 0) {
                $book->update(['status' => 'active']);
            }
        }

        if (!$this->is_active && $this->books->every(fn($book) => $book->stock > 0)) {
            $this->update(['is_active' => true]);
        }
    }

    public function orderItems()
    {
        return $this->morphMany(OrderItem::class, 'orderable');
    }

    public function createSlug()
    {
        $slug = Str::slug($this->name);
        $originalSlug = $slug;
        $count = 1;

        while (static::where('slug', $slug)->where('id', '!=', $this->id)->exists()) {
            $slug = $originalSlug . '-' . $count;
            $count++;
        }

        $this->slug = $slug;
        $this->save();
    }
}
