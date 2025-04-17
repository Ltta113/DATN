<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Book extends Model
{
    /** @use HasFactory<\Database\Factories\BookFactory> */
    use HasFactory;

    protected $fillable = [
        'title',
        'description',
        'slug',
        'isbn',
        'published_at',
        'publisher_id',
        'cover_image',
        'price',
        'stock',
        'language',
        'page_count',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'published_at' => 'datetime',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array
     */
    protected $hidden = [
        'created_at',
        'updated_at',
    ];

    /**
     * Get the publisher that owns the book.
     *
     * @return BelongsTo
     */
    public function publisher(): BelongsTo
    {
        return $this->belongsTo(Publisher::class);
    }

    /**
     * Get the book authors that belong to the book.
     *
     * @return BelongsToMany
     */
    public function book_authors(): BelongsToMany
    {
        return $this->belongsToMany(Author::class, 'book_authors');
    }

    /**
     * Get the book categories that belong to the book.
     *
     * @return BelongsToMany
     */
    public function book_categories(): BelongsToMany
    {
        return $this->belongsToMany(Category::class, 'book_categories');
    }

    /**
     * Scope a query to get newest books.
     *
     * @param Builder $query
     * @param int $limit
     *
     * @return Collection
     */
    public function scopeGetNewestBooks(Builder $query, int $limit = 5): Collection
    {
        return $query->orderBy('published_at', 'desc')->take($limit)->get();
    }
}
