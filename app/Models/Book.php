<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class Book extends Model
{
    use SoftDeletes;

    protected $table = 'books';

    public $timestamps = true;

    protected $fillable = ['category_id', 'title', 'slugs', 'cover', 'type', 'content', 'description', 'likes', 'comments', 'status'];

    protected $hidden = [
        'updated_at', 'created_at',
    ];
    protected $casts = [
        'category_id' => 'integer',
        'status' => 'integer',
        'type' => 'integer'
    ];

    /**
     * Get the category that owns the Book
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    /**
     * Get all of the babs for the Book
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function babs(): HasMany
    {
        return $this->hasMany(Bab::class);
    }
}
