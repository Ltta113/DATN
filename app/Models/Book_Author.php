<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Book_Author extends Model
{
    /** @use HasFactory<\Database\Factories\BookAuthorFactory> */
    use HasFactory;

    protected $fillable = [
        'book_id',
        'author_id',
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
     * Get the book that owns the book_author.
     *
     * @return BelongsToMany
     */
    public function book(): BelongsToMany
    {
        return $this->belongsToMany(Book::class);
    }

    /**
     * Get the author that owns the book_author.
     *
     * @return BelongsToMany
     */
    public function author(): BelongsToMany
    {
        return $this->belongsToMany(Author::class);
    }
}
