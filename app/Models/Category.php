<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Category extends Model
{
    /** @use HasFactory<\Database\Factories\CategoryFactory> */
    use HasFactory;

    protected $fillable = [
        'name',
        'description',
        'slug',
        'parent_id',
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
     * Get the book categories that belong to the category.
     *
     * @return BelongsToMany
     */
    public function book_categories(): BelongsToMany
    {
        return $this->belongsToMany(Book::class, 'book_categories');
    }

    /**
     * Get the child categories for the category.
     *
     * @return HasMany
     */
    public function childCategories(): HasMany
    {
        return $this->hasMany(Category::class, 'parent_id');
    }
}
