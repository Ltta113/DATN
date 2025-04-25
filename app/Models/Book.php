<?php

namespace App\Models;

use Illuminate\Contracts\Database\Eloquent\Builder as EloquentBuilder;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;

class Book extends Model
{
    /** @use HasFactory<\Database\Factories\BookFactory> */
    use HasFactory;

    protected $fillable = [
        'title',
        'description',
        'slug',
        'published_at',
        'publisher_id',
        'cover_image',
        'status',
        'price',
        'stock',
        'language',
        'public_id',
        'isbn',
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
    public function authors(): BelongsToMany
    {
        return $this->belongsToMany(Author::class, 'book_authors');
    }

    /**
     * Get the book categories that belong to the book.
     *
     * @return BelongsToMany
     */
    public function categories(): BelongsToMany
    {
        return $this->belongsToMany(Category::class, 'book_categories');
    }

    /**
     * Get bookmarks that belong to the book.
     *
     * @return BelongsToMany
     */
    public function bookmarks(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'bookmarks', 'book_id', 'user_id');
    }

    /**
     * Get reviews for the book.
     *
     * @return MorphMany
     */
    public function reviews(): MorphMany
    {
        return $this->morphMany(Review::class, 'reviewable');
    }

    /**
     * Scope a query to get newest books.
     *
     * @param Builder $query
     *
     * @return Collection
     */
    public function scopeGetNewestBooks(Builder $query): EloquentBuilder
    {
        return $query
            ->where('status', 'active')
            ->orderBy('published_at', 'desc');
    }

    public function scopeGetBestSoldBooks(Builder $query): EloquentBuilder
    {
        return $query
            ->where('status', 'active')
            ->orderBy('sold', 'desc');
    }

    public function scopeGetBestSoldBooksThisMonth(Builder $query): EloquentBuilder
    {
        return $query
            ->where('status', 'active')
            ->whereMonth('created_at', now()->month)
            ->orderBy('sold', 'desc');
    }

    public function getStarRatingAttribute()
    {
        return $this->reviews()->avg('rating') ?? 0;
    }

    public function getStarRatingCountAttribute()
    {
        return $this->reviews()->count() ?? 0;
    }
}
